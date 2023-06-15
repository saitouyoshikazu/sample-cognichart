<?php

namespace App\Infrastructure\Storage;
use Storage;

class AbstractChartTermStorage implements AbstractChartTermStorageInterface
{

    private $disk;

    public function __construct()
    {

        if (env('APP_ENV') === 'testing') {
            $this->disk = Storage::disk('AbstractChartTermTest');
        } else if (env('APP_ENV') === 'release') {
            $this->disk = Storage::disk('AbstractChartTermS3');
        } else {
            $this->disk = Storage::disk('AbstractChartTerm');
        }
    }

    public function put(string $fileName, string $contents)
    {
        $result = $this->disk->put($fileName, $contents);
        if ($result === false) {
            return false;
        }
        return true;
    }

    public function get(string $fileName)
    {
        return $this->disk->get($fileName);
    }

    public function delete(string $fileName)
    {
        return $this->disk->delete($fileName);
    }

    public function exists(string $fileName)
    {
        return $this->disk->exists($fileName);
    }

    public function files()
    {
        return $this->disk->files();
    }

}
