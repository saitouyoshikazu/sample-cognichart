<?php

namespace App\Domain\ValueObjects;

class CheckPromotionVideoConditions
{

    private $conditions;

    public function __construct()
    {
        $this->conditions = [];
    }

    public function appendCreatedAtGTE(string $createdAt)
    {
        $this->conditions[] = [
            "scope" =>  "createdAtGTE",
            "param" =>  $createdAt
        ];
    }

    public function appendCreatedAtLT(string $createdAt)
    {
        $this->conditions[] = [
            "scope" =>  "createdAtLT",
            "param" =>  $createdAt
        ];
    }

    public function appendMusicIdLike(string $musicIdLike)
    {
        $this->conditions[] = [
            "scope" =>  "musicIdLike",
            "param" =>  $musicIdLike
        ];
    }

    public function getConditions()
    {
        return $this->conditions;
    }

}
