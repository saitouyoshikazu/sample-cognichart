<?php

if (!function_exists('cdn')) {
    function cdn(string $path, $secure = null)
    {
        $root = config('app.asset_url');
        return app('url')->assetFrom($root, $path, $secure);
    }
}
