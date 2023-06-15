<?php

namespace App\Infrastructure\Repositories;
use App\Domain\AdminUser\AdminUserRepositoryInterface;
use App\Domain\AdminUser\AdminUserFactoryInterface;
use App\Domain\AdminUser\AdminUserId;
use App\Domain\AdminUser\AdminUserEntity;
use App\Domain\AdminUser\AdminUserSpecification;
use App\Domain\ValueObjects\SortColumn;
use App\Infrastructure\Eloquents\User;
use App\Domain\DomainPaginator;

class AdminUserRepository implements AdminUserRepositoryInterface
{

    private $adminUserFactory;

    public function __construct(AdminUserFactoryInterface $adminUserFactory)
    {
        $this->adminUserFactory = $adminUserFactory;
    }

    public function find(AdminUserId $id)
    {
        $row = User::find($id->value());
        if (empty($row)) {
            return null;
        }
        $adminUserEntity = $this->adminUserFactory->create(
            $row->id,
            $row->name,
            $row->email,
            $row->is_super,
            $row->updated_at
        );
        return $adminUserEntity;
    }

    public function paginate(string $name = null, SortColumn $sortColumn, AdminUserSpecification $adminUserSpecification)
    {
        return $adminUserSpecification->paginate($name, $sortColumn, $this);
    }

    public function getPaginator(string $name = null, SortColumn $sortColumn)
    {
        $builder = User::query();
        if (!empty($name)) {
            $builder = User::nameLike($builder, $name);
        }
        $builder = User::searchOrder($builder, $sortColumn->getColumn(), $sortColumn->getDestination());
        $rows = User::executePaginate($builder);
        if (empty($rows)) {
            return null;
        }
        $adminUserEntities = [];
        foreach ($rows AS $row) {
            $adminUserEntity = $this->adminUserFactory->create(
                $row->id,
                $row->name,
                $row->email,
                $row->is_super,
                $row->updated_at
            );
            if (!empty($adminUserEntity)) {
                $adminUserEntities[] = $adminUserEntity;
            }
        }
        $domainPaginator = new DomainPaginator($adminUserEntities, $rows);
        return $domainPaginator;
    }

    public function delete(AdminUserId $myId, AdminUserId $id, AdminUserSpecification $adminUserSpecification)
    {
        $adminUserSpecification->canDelete($myId, $id, $this);
        $result = User::destroy($id->value());
        if ($result !== 1) {
            return false;
        }
        return true;
    }

    public function store(AdminUserId $myId, AdminUserEntity $adminUserEntity, AdminUserSpecification $adminUserSpecification)
    {
        $adminUserSpecification->canUpdate($myId, $adminUserEntity, $this);
        $result = User::where(['id' => $adminUserEntity->getId()->value()])->update([
            'name'      =>  $adminUserEntity->getName(),
            'email'     =>  $adminUserEntity->getEmail(),
            'is_super'  =>  $adminUserEntity->isSuperUser()
        ]);
        if ($result !== 1) {
            return false;
        }
        return true;
    }

}
