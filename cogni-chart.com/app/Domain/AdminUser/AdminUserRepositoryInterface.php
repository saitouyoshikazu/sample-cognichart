<?php

namespace App\Domain\AdminUser;
use App\Domain\AdminUser\AdminUserFactoryInterface;
use App\Domain\ValueObjects\SortColumn;

interface AdminUserRepositoryInterface
{

    public function __construct(AdminUserFactoryInterface $adminUserFactory);

    /**
     * Find user by id.
     * @param   AdminUserId $id     The id of user.
     * @return  AdminUserEntity     When id was matched.
     *          null                When id wasn't matched.
     */
    public function find(AdminUserId $id);

    /**
     * Get list of user paginated.
     * @param   string                  $name                       Substring of user's name.
     * @param   SortColumn              $sortColumn                 SortColumn.
     * @param   AdminUserSpecification  $adminUserSpecification     AdminUserSpecification.
     * @return  DomainPaginatorInterface
     */
    public function paginate(string $name = null, SortColumn $sortColumn, AdminUserSpecification $adminUserSpecification);

    /**
     * Execute paginate users.
     * @param   string                  $name                       Substring of user's name.
     * @param   SortColumn              $sortColumn                 SortColumn.
     * @return  DomainPaginatorInterface
     */
    public function getPaginator(string $name = null, SortColumn $sortColumn);

    /**
     * Delete user by id.
     * @param   AdminUserId             $myId                       The id of user that is logedin.
     * @param   AdminUserId             $id                         The id of user that will be deleted.
     * @param   AdminUserSpecification  $adminUserSpecification     AdminUserSpecification.
     * @return  true    When successfully deleted.
     *          false   When failed to delete.
     * @throws  AdminUserException  Can't execute delete.
     */
    public function delete(AdminUserId $myId, AdminUserId $id, AdminUserSpecification $adminUserSpecification);

    /**
     * Store user.
     * This method only execute update.
     * If you want to insert new user, please use auth.
     * @param   AdminUserId             $myId                       The id of user that is logedin.
     * @param   AdminUserEntity         $adminUserEntity            AdminUserEntity that will be updated.
     * @param   AdminUserSpecification  $adminUserSpecification     AdminUserSpecification.
     * @return  true    When successfully updated.
     *          false   When failed to update.
     * @throws  AdminUserException  Can't execute update.
     */
    public function store(AdminUserId $myId, AdminUserEntity $adminUserEntity, AdminUserSpecification $adminUserSpecification);

}
