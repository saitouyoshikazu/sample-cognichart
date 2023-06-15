<?php

namespace App\Domain\AdminUser;
use App\Domain\ValueObjects\VODateTime;

class AdminUserFactory implements AdminUserFactoryInterface
{

    public function create(
        int     $idValue,
        string  $name,
        string  $email,
        int     $superUser,
        string  $lastModified
    ) {
        $id = new AdminUserId($idValue);
        $adminUseerEntity = new AdminUserEntity($id);
        $adminUseerEntity
            ->setName($name)
            ->setEmail($email)
            ->setSuperUser($superUser)
            ->setLastModified(new VODateTime($lastModified));
        return $adminUseerEntity;
    }

}
