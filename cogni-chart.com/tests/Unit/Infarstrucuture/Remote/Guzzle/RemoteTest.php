<?php

namespace Tests\Unit\Infrastructure\Remote\Guzzle;
use Tests\TestCase;
use App\Infrastructure\Remote\Scheme;

class RemoteTest extends TestCase
{

    public function testSendPost()
    {
        $remote = app('App\Infrastructure\Remote\Guzzle\Remote');

        $scheme = new Scheme('https');
        $host = 'vm.general.com';
        $url = 'country/get/US';
        $options = ['verify' => env('GENERAL_CACERT_FILE')];

        $res = $remote->sendPost($scheme, $host, $url, null, null, $options);
        $this->assertEquals($res->getStatus(), 200);
    }

    public function testSendGet()
    {
        $remote = app('App\Infrastructure\Remote\Guzzle\Remote');

        $scheme = new Scheme('https');
        $host = 'vm.general.com';
        $url = 'country/get/US';
        $options = ['verify' => env('GENERAL_CACERT_FILE')];

        $res = $remote->sendGet($scheme, $host, $url, null, null, $options);
        $this->assertEquals($res->getStatus(), 200);
    }

}
