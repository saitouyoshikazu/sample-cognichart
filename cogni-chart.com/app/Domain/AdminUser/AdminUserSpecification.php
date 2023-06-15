<?php

namespace App\Domain\AdminUser;
use App\Domain\ValueObjects\SortColumn;

class AdminUserSpecification
{

    public function paginate(string $name = null, SortColumn $sortColumn, AdminUserRepositoryInterface $adminUserRepository)
    {
        return $adminUserRepository->getPaginator($name, $sortColumn);
    }

    public function canDelete(AdminUserId $myId, AdminUserId $id, AdminUserRepositoryInterface $adminUserRepository)
    {
        $adminUserEntity = $adminUserRepository->find($id);
        if (empty($adminUserEntity)) {
            throw new AdminUserException(__('The user wasn\'t found.'));
        }
        $I = $adminUserRepository->find($myId);
        if (!$I->isSuperUser()) {
            throw new AdminUserException(__('You don\'t have authority to delete AdminUser.'));
        }
        if ($I->equals($adminUserEntity)) {
            throw new AdminUserException(__('You don\'t have authority to delete AdminUser.'));
        }
    }

    public function canUpdate(AdminUserId $myId, AdminUserEntity $adminUserEntity, AdminUserRepositoryInterface $adminUserRepository)
    {
        $adminUserId = $adminUserEntity->getId();
        $currentAdminUserEntity = $adminUserRepository->find($adminUserId);
        if (empty($currentAdminUserEntity)) {
            throw new AdminUserException(__('The user wasn\'t found.'));
        }
        $I = $adminUserRepository->find($myId);
        if (!$I->isSuperUser() && !$I->equals($currentAdminUserEntity)) {
            throw new AdminUserException(__('You don\'t have authority to update AdminUser.'));
        }
        if ($currentAdminUserEntity->superUserChanged($adminUserEntity) && !$I->isSuperUser()) {
            throw new AdminUserException(__('You don\'t have authority to update AdminUser.'));
        }
    }

}
