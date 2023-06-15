<?php

namespace App\Application\AdminUser;
use App\Application\DXO\AdminUserDXO;
use App\Domain\AdminUser\AdminUserRepositoryInterface;

interface AdminUserApplicationInterface
{

    public function __construct(AdminUserRepositoryInterface $adminUserRepository);

    public function list(AdminUserDXO $adminUserDXO);

    public function get(AdminUserDXO $adminUserDXO);

    public function delete(AdminUserDXO $adminUserDXO);

    public function update(AdminUserDXO $adminUserDXO);

}
