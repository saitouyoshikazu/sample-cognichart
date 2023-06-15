<?php

namespace App\Infrastructure\RedisDAO;

interface RedisDAOInterface
{

    /**
     * Execute get to redis.
     * 
     * @param   string  $key    The key.
     * @return  string  When the key exists on redis.
     *          null    When the key doesn't exist on redis.
     */
    public function get(string $key);

    /**
     * Execute set to redis.
     *
     * @param   string  $key    The key of value to set to redis.
     * @param   string  $value  The value to set to redis.
     * @return  true    When the value was correctly saved to redis.
     *          false   When the value wasn't saved to redis. This case contains Queued.
     */
    public function set(string $key, string $value);

    /**
     * Execute del to redis.
     *
     * @param   string  $key    The key.
     * @return  int     The count of deleted keys.
     */
    public function del(string $key);

    /**
     * Execute keys to redis by the regex string.
     * @param   string  $regex  The regex string of keys.
     * @return  array   Array of keys matching the $regex.
     */
    public function keys(string $regex);

    /**
     * Execute del to redis by the regex string.
     * @param   string  $regex  The regex string of keys.
     * @return  int     The count of deleted keys.
     */
    public function clear(string $regex);

}
