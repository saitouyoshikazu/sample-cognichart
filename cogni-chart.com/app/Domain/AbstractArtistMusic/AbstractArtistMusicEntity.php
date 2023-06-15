<?php

namespace App\Domain\AbstractArtistMusic;
use App\Domain\Entity;
use App\Domain\EntityId;
use App\Domain\ValueObjects\ApiName;

class AbstractArtistMusicEntity extends Entity
{

    private $apiName;
    private $response;

    public function __construct(EntityId $id, ApiName $apiName)
    {
        parent::__construct($id);
        $this->setApiName($apiName);
    }

    public function setApiName(ApiName $apiName)
    {
        $this->apiName = $apiName;
        $this->setBusinessId();
    }

    public function apiName()
    {
        return $this->apiName;
    }

    protected function setBusinessId()
    {
        if (empty($this->id()) || empty($this->apiName())) {
            $this->businessId = null;
            return;
        }
        if (empty($this->businessId)) {
            $this->businessId = new AbstractArtistMusicBusinessId($this->apiName(), $this->id());
            return;
        }
        $this->businessId->setApiName($this->apiName());
        return;
    }

    public function setResponse(array $response)
    {
        $this->response = $response;
        return $this;
    }

    public function response()
    {
        return $this->response;
    }

}
