<?php

namespace App\Application\AdminUser;
use DB;
use App\Application\DXO\AdminUserDXO;
use App\Domain\AdminUser\AdminUserRepositoryInterface;
use App\Domain\AdminUser\AdminUserSpecification;

class AdminUserApplication implements AdminUserApplicationInterface
{

    private $adminUserRepository;

    public function __construct(AdminUserRepositoryInterface $adminUserRepository)
    {
        $this->adminUserRepository = $adminUserRepository;
    }

    public function list(AdminUserDXO $adminUserDXO)
    {
        $adminUserSpecification = new AdminUserSpecification();
        $domainPaginator = $this->adminUserRepository->paginate($adminUserDXO->getName(), $adminUserDXO->getSortColumn(), $adminUserSpecification);
        return $domainPaginator;
    }

    public function get(AdminUserDXO $adminUserDXO)
    {
        return $this->adminUserRepository->find($adminUserDXO->getId());
    }

    public function delete(AdminUserDXO $adminUserDXO)
    {
        DB::beginTransaction();
        try {
            $adminUserSpecification = new AdminUserSpecification();
            $result = $this->adminUserRepository->delete(
                $adminUserDXO->getMyId(),
                $adminUserDXO->getId(),
                $adminUserSpecification
            );
            if ($result === false) {
                DB::rollback();
                return false;
            }
        } catch(\Exception $e) {
            DB::rollback();
            throw $e;
        }
        DB::commit();
        return true;
    }

    public function update(AdminUserDXO $adminUserDXO)
    {
        $adminUserEntity = $this->adminUserRepository->find($adminUserDXO->getId());
        if (empty($adminUserEntity)) {
            throw new AdminUserException(__('The user wasn\'t found.'));
        }
        $adminUserEntity
            ->setName($adminUserDXO->getName())
            ->setEmail($adminUserDXO->getEmail());
        if (!is_null($adminUserDXO->getSuperUser())) {
            $adminUserEntity->setSuperUser($adminUserDXO->getSuperUser());
        }

        DB::beginTransaction();
        try {
            $adminUserSpecification = new AdminUserSpecification();
            $result = $this->adminUserRepository->store(
                $adminUserDXO->getMyId(),
                $adminUserEntity,
                $adminUserSpecification
            );
            if ($result === false) {
                DB::rollback();
                return false;
            }
        } catch(\Exception $e) {
                DB::rollback();
                throw $e;
        }
        DB::commit();
        return true;
    }

}
