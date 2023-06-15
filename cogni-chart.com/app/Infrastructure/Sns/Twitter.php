<?php

namespace App\Infrastructure\Sns;
use Config;
use Abraham\TwitterOAuth\TwitterOAuth;

class Twitter implements TwitterInterface
{

    private $oAuth;

    public function __construct()
    {
        $this->oAuth = new TwitterOAuth(
            Config::get('services.twitter.consumer_key'),
            Config::get('services.twitter.consumer_secret'),
            Config::get('services.twitter.access_token'),
            Config::get('services.twitter.access_token_secret')
        );
    }

    public function post(string $message)
    {
        $messageByte = strlen(bin2hex($message)) / 2;
        if ($messageByte > 280) {
            return false;
        }

        $response = $this->oAuth->post("statuses/update", ["status" => $message]);
        if (empty($response)) {
            return false;
        }
        if (isset($response->errors) && $response->errors != '') {
            return false;
        }
        return true;
    }

}
