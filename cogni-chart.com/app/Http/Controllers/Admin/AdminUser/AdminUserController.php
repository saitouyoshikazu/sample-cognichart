<?php

namespace App\Http\Controllers\Admin\AdminUser;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Application\AdminUser\AdminUserApplicationInterface;
use App\Application\DXO\AdminUserDXO;
use App\Domain\AdminUser\AdminUserFactoryInterface;
use App\Expand\Validation\ExpValidation;
use Illuminate\Validation\Rule;
use Config;

class AdminUserController extends Controller
{

    private $adminUserApplication;

    private $adminUserFactory;

    public function __construct(AdminUserApplicationInterface $adminUserApplication, AdminUserFactoryInterface $adminUserFactory)
    {
        $this->middleware('auth');
        $this->adminUserApplication = $adminUserApplication;
        $this->adminUserFactory = $adminUserFactory;
    }

    public function list(Request $request)
    {
        $expValidation = new ExpValidation(['search_name', 'sortcolumn', 'sortdestination']);
        $expValidation->validateWithRedirect($request);

        $adminUserDXO = new AdminUserDXO();
        $adminUserDXO->list($request->input('search_name'), $request->input('sortcolumn'), $request->input('sortdestination'));
        $domainPaginator = $this->adminUserApplication->list($adminUserDXO);
        $adminUserEntities = null;
        $adminUserPaginator = null;
        if (!empty($domainPaginator)) {
            $adminUserEntities = $domainPaginator->getEntities();
            $adminUserPaginator = $domainPaginator->getPaginator();
        }
        return view(
            'admin.adminuser.list',
            [
                'search_name' => $request->input('search_name'),
                'sortcolumn' => $request->input('sortcolumn'),
                'sortdestination' => $request->input('sortdestination'),
                'adminUserEntities' => $adminUserEntities,
                'adminUserPaginator' => $adminUserPaginator
            ]
        );
    }

    public function get(Request $request)
    {
        $expValidation = new ExpValidation(['adminuser_id']);
        $expValidation->validateWithRedirect($request);

        $adminUserDXO = new AdminUserDXO();
        $adminUserDXO->get($request->input('adminuser_id'));
        $adminUserEntity = $this->adminUserApplication->get($adminUserDXO);
        $selfAdminUserEntity = $this->adminUserFactory->create(
            \Auth::user()->id,
            \Auth::user()->name,
            \Auth::user()->email,
            \Auth::user()->is_super,
            \Auth::user()->updated_at
        );
        return view('admin.adminuser.modify', ['selfAdminUserEntity' => $selfAdminUserEntity, 'adminUserEntity' => $adminUserEntity]);
    }

    public function delete(Request $request)
    {
        $expValidation = new ExpValidation(['adminuser_id']);
        $expValidation->validateWithRedirect($request);

        $adminUserDXO = new AdminUserDXO();
        $adminUserDXO->delete(\Auth::user()->id, $request->input('adminuser_id'));
        try {
            $result = $this->adminUserApplication->delete($adminUserDXO);
            if ($result === false) {
                return redirect('/adminuser/list')->withErrors(['application' => __('Couldn\'t delete AdminUser.')]);
            }
        } catch(\Exception $e) {
            return redirect('/adminuser/list')->withErrors(['application' => __($e->getMessage())]);
        }
        return redirect('/adminuser/list');
    }

    public function update(Request $request)
    {
        $this->validate(
            $request,
            [
                'adminuser_id'  =>  Config::get('validation_rules.adminuser_id'),
                'name'          =>  Config::get('validation_rules.name'),
                'email'         =>  ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($request->input('adminuser_id'))],
                'superuser'     =>  Config::get('validation_rules.superuser')
            ],
            [],
            [
                'adminuser_id'  =>  __('adminuser_id'),
                'name'          =>  __('name'),
                'email'         =>  __('email'),
                'superuser'     =>  __('superuser')
            ]
        );
        $adminUserDXO = new AdminUserDXO();
        $adminUserDXO->update(
            \Auth::user()->id,
            $request->input('adminuser_id'),
            $request->input('name'),
            $request->input('email'),
            $request->input('superuser', null)
        );
        try {
            $result = $this->adminUserApplication->update($adminUserDXO);
            if ($result === false) {
                return redirect('/adminuser/list')->withErrors(['application' => __('Couldn\'t update AdminUser.')]);
            }
        } catch(\Exception $e) {
            return redirect('/adminuser/list')->withErrors(['application' => __($e->getMessage())]);
        }
        return redirect('/adminuser/list');
    }

}
