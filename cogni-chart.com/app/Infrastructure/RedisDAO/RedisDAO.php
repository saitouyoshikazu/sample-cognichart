<?php

namespace App\Infrastructure\RedisDAO;
use Redis;
use Config;
use App\Infrastructure\RedisDAO\RedisDAOInterface;

class RedisDAO implements RedisDAOInterface
{

    public function get(string $key)
    {
        return Redis::get($key);
    }

    public function set(string $key, string $value)
    {
        $response = Redis::set($key, $value);
        $expire = Config::get('database.redis.expire');
        if (!empty($expire)) {
            Redis::expire($key, $expire);
        }
        $result = $response->getPayload();
        if ($result === 'OK') {
            return true;
        }
        return false;
    }

    public function del(string $key)
    {
        return Redis::del($key);
    }

    public function keys(string $regex)
    {
        $regex = str_replace("\\", "\\\\", $regex);
        return Redis::keys($regex);
    }

    public function clear(string $regex)
    {
        $deleted = 0;
        $keys = $this->keys($regex);
        if (empty($keys)) {
            return $deleted;
        }
        foreach ($keys AS $delKey) {
            $deleted += $this->del($delKey);
        }
        return $deleted;
    }

}
