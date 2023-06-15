<?php

namespace App\Domain\AbstractArtistMusic;
use App\Domain\BusinessIdInterface;
use App\Domain\EntityId;
use App\Domain\ValueObjects\ApiName;

class AbstractArtistMusicBusinessId implements BusinessIdInterface
{

    private $apiName;
    private $id;

    public function __construct(ApiName $apiName, EntityId $id)
    {
        $this->setApiName($apiName);
        $this->setId($id);
    }

    public function setApiName(ApiName $apiName)
    {
        $this->apiName = $apiName;
        return $this;
    }

    public function apiName()
    {
        return $this->apiName;
    }

    public function setId(EntityId $id)
    {
        $this->id = $id;
        return $this;
    }

    public function id()
    {
        return $this->id;
    }

    public function value()
    {
        return $this->apiName()->value() . '-' . $this->id()->value();
    }

}
