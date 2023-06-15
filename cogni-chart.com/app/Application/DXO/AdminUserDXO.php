<?php

namespace App\Application\DXO;
use App\Domain\AdminUser\AdminUserId;
use App\Domain\ValueObjects\SortColumn;

class AdminUserDXO
{

    private $myId;

    private $id;

    private $name;

    private $email;

    private $superUser;

    private $sortColumnName;

    private $sortDestination;

    private $columnNameMap = [
        'name'          =>  'name',
        'email'         =>  'email',
        'super/user'    =>  'is_super',
        'last modified' =>  'updated_at'
    ];

    private $destinationMap = [
        'asc'   =>  'asc',
        'desc'  =>  'desc'
    ];

    public function list(string $name = null, string $sortColumnName = null, string $sortDestination = null)
    {
        $this->name = $name;
        $this->sortColumnName = $sortColumnName;
        $this->sortDestination = $sortDestination;
    }

    public function get(int $id)
    {
        $this->id = $id;
    }

    public function delete(int $myId, int $id)
    {
        $this->myId = $myId;
        $this->id = $id;
    }

    public function update(int $myId, int $id, string $name, string $email, int $superUser = null)
    {
        $this->myId = $myId;
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->superUser = $superUser;
    }

    public function getMyId()
    {
        return new AdminUserId($this->myId);
    }

    public function getId()
    {
        return new AdminUserId($this->id);
    }

    public function getName()
    {
        return trim($this->name);
    }

    public function getEmail()
    {
        return trim($this->email);
    }

    public function getSuperUser()
    {
        return $this->superUser;
    }

    public function getSortColumn()
    {
        $sortColumnName = trim($this->sortColumnName);
        $columnName = $this->columnNameMap['last modified'];
        if (!empty($sortColumnName) && array_key_exists($sortColumnName, $this->columnNameMap)) {
            $columnName = $this->columnNameMap[$sortColumnName];
        }
        $sortDestination = trim($this->sortDestination);
        $destination = $this->destinationMap['desc'];
        if (!empty($sortDestination) && array_key_exists($sortDestination, $this->destinationMap)) {
            $destination = $this->destinationMap[$sortDestination];
        }
        return new SortColumn($columnName, $destination);
    }

}
