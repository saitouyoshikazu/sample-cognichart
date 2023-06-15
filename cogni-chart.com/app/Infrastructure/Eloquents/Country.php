<?php

namespace App\Infrastructure\Eloquents;
use Config;
use App\Infrastructure\Remote\Scheme;

class Country
{

    public static function get(string $country_id)
    {
        $remote = app('App\Infrastructure\Remote\RemoteInterface');
        $scheme = new Scheme(Config::get('app.general_server.scheme'));
        $host = Config::get('app.general_server.host');
        $url = "country/get/{$country_id}";
        $options = ['verify' => Config::get('app.general_server.cacertfile')];
        $response = $remote->sendGet(
            $scheme,
            $host,
            $url,
            null,
            null,
            $options
        );
        if ($response->getStatus() !== 200) {
            return null;
        }
        $bodyString = trim($response->getBody());
        if (empty($bodyString)) {
            return null;
        }
        $rows = json_decode($bodyString);
        if (empty($rows)) {
            return null;
        }
        $row = $rows;
        if (is_array($rows)) {
            $row = $rows[0];
        }
        return $row;
    }

    public static function list(array $country_ids = null)
    {
        $routeParam = "";
        if (!empty($country_ids)) {
            $routeParam = implode(',', $country_ids);
        }

        $remote = app('App\Infrastructure\Remote\RemoteInterface');
        $scheme = new Scheme(Config::get('app.general_server.scheme'));
        $host = Config::get('app.general_server.host');
        $url = "country/list/{$routeParam}";
        $options = ['verify' => Config::get('app.general_server.cacertfile')];
        $response = $remote->sendGet(
            $scheme,
            $host,
            $url,
            null,
            null,
            $options
        );
        if ($response->getStatus() !== 200) {
            return null;
        }
        $bodyString = trim($response->getBody());
        if (empty($bodyString)) {
            return null;
        }
        $rows = json_decode($bodyString);
        if (empty($rows)) {
            return null;
        }
        return $rows;
    }

}
