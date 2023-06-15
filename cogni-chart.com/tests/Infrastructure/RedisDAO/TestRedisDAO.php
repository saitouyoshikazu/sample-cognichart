<?php

namespace Tests\Infrastructure\RedisDAO;
use App\Infrastructure\RedisDAO\RedisDAO;

class TestRedisDAO extends RedisDAO
{

    private $isCache = false;

    public function get(string $key)
    {
        $this->isCache = false;
        $cache = parent::get($key);
        if (!empty($cache)) {
            $this->isCache = true;
        }
        return $cache;
    }

    public function isCache()
    {
        return $this->isCache;
    }

    public function resetIsCache()
    {
        $this->isCache = false;
    }

}
