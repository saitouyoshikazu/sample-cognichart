<?php

namespace App\Infrastructure\Storage;

interface AbstractChartTermStorageInterface
{

    public function __construct();

    public function put(string $fileName, string $contents);

    public function get(string $fileName);

    public function delete(string $fileName);

    public function exists(string $fileName);

    public function files();

}
