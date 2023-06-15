<?php

namespace App\Infrastructure\Sns;
use App\Infrastructure\Remote\RemoteInterface;
use App\Infrastructure\Remote\Scheme;
use Config;

class Facebook implements FacebookInterface
{

    private $remote;

    public function __construct(
        RemoteInterface $remote
    ) {
        $this->remote = $remote;
    }

    public function post(string $message)
    {
        $pageId = Config::get('services.facebook.page_id');
        $token = Config::get('services.facebook.page_access_token');

        if (empty($pageId) || empty($token)) {
            return false;
        }

        $this->remote->sendPost(
            new Scheme(Scheme::https),
            'graph.facebook.com',
            "{$pageId}/feed",
            [
                'message' => $message,
                'access_token' => $token
            ]
        );
        return true;
    }

}
