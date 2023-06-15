<?php

namespace App\Domain\AdminUser;

interface AdminUserFactoryInterface
{

    public function create(
        int     $idValue,
        string  $name,
        string  $email,
        int     $superUser,
        string  $lastModified
    );

}
