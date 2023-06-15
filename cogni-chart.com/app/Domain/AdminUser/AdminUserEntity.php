<?php

namespace App\Domain\AdminUser;
use App\Domain\ValueObjects\VODateTime;

class AdminUserEntity
{

    private $id;
    private $name;
    private $email;
    private $superUser;
    private $lastModified;

    public function __construct(AdminUserId $id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setName(string $name)
    {
        $this->name = $name;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setEmail(string $email)
    {
        $this->email = $email;
        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setSuperUser(int $superUser)
    {
        $this->superUser = $superUser;
        return $this;
    }

    public function isSuperUser()
    {
        if ($this->superUser === 1) {
            return true;
        }
        return false;
    }

    public function setLastModified(VODateTime $lastModified = null)
    {
        $this->lastModified = $lastModified;
        return $this;
    }

    public function getLastModified()
    {
        return $this->lastModified;
    }

    public function equals(AdminUserEntity $comparative)
    {
        if ($this->id->equals($comparative->getId())) {
            return true;
        }
        return false;
    }

    public function superUserChanged(AdminUserEntity $adminUserEntity)
    {
        if ($this->isSuperUser() !== $adminUserEntity->isSuperUser()) {
            return true;
        }
        return false;
    }

}
