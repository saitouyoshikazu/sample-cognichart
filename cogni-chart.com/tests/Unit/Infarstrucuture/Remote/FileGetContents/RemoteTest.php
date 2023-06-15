<?php

namespace Tests\Unit\Infrastructure\Remote\FileGetContents;
use Tests\TestCase;
use App\Infrastructure\Remote\Scheme;
use App\Infrastructure\Remote\FileGetContents\StreamContextOption;

class RemoteTest extends TestCase
{

    public function testScheme()
    {
        $scheme = new Scheme('http');
        $this->assertEquals(get_class($scheme), 'App\Infrastructure\Remote\Scheme');

        $scheme = new Scheme('https');
        $this->assertEquals(get_class($scheme), 'App\Infrastructure\Remote\Scheme');
    }

    /**
     * @expectedException   App\Infrastructure\Remote\RemoteException
     */
    public function testSchemeFailed()
    {
        $scheme = new Scheme('tcp');
    }

    public function testBuildUrl()
    {
        $remote = app('App\Infrastructure\Remote\FileGetContents\Remote');

        $schemeVal = 'https';
        $host = 'vm.general.com';
        $verify = "{$schemeVal}://{$host}";
        $scheme = new Scheme($schemeVal);
        $res = $remote->buildUrl($scheme, $host);
        $this->assertEquals($res, $verify);

        $url = "country/get/US";
        $verify = "{$schemeVal}://{$host}/{$url}";
        $res = $remote->buildUrl($scheme, $host, $url);
        $this->assertEquals($res, $verify);

        $schemeVal = 'http';
        $verify = "{$schemeVal}://{$host}/{$url}";
        $scheme = new Scheme($schemeVal);
        $res = $remote->buildUrl($scheme, $host, $url);
        $this->assertEquals($res, $verify);
    }

    /**
     * @expectedException   App\Infrastructure\Remote\RemoteException
     */
    public function testBuildUrlFails()
    {
        $remote = app('App\Infrastructure\Remote\FileGetContents\Remote');

        $schemeVal = 'https';
        $host = ' ';
        $scheme = new Scheme($schemeVal);
        $remote->buildUrl($scheme, $host);

        $host = '';
        $remote->buildUrl($scheme, $host);
    }

    public function testBuildData()
    {
        $remote = app('App\Infrastructure\Remote\FileGetContents\Remote');

        $params = null;
        $res = $remote->buildData($params);
        $this->assertEquals($res, '');

        $params = [];
        $res = $remote->buildData($params);
        $this->assertEquals($res, '');

        $params = ['id' =>  'US'];
        $verify = 'id=US';
        $res = $remote->buildData($params);
        $this->assertEquals($res, $verify);

        $params = ['id' =>  'US',   'name'  =>  'USA'];
        $verify = 'id=US&name=USA';
        $res = $remote->buildData($params);
        $this->assertEquals($res, $verify);
    }

    public function testbBuildPostHeader()
    {
        $remote = app('App\Infrastructure\Remote\FileGetContents\Remote');

        $params = ['id' =>  'US'];
        $queriedData = $remote->buildData($params);
        $verify = [
            'Content-Type: application/x-www-form-urlencoded',
            'Content-Length: ' . strlen($queriedData)
        ];
        $res = $remote->buildPostHeader($queriedData);
        $this->assertEquals($res, $verify);

        $addHeaders = ['testHeader' =>  'testHeaderValue'];
        $verify = [
            'Content-Type: application/x-www-form-urlencoded',
            'Content-Length: ' . strlen($queriedData),
            'testHeader: testHeaderValue'
        ];
        $res = $remote->buildPostHeader($queriedData, $addHeaders);
        $this->assertEquals($res, $verify);

        $addHeaders = ['Content-Type'   =>  'image/jpeg'];
        $verify = [
            'Content-Type: image/jpeg',
            'Content-Length: ' . strlen($queriedData),
        ];
        $res = $remote->buildPostHeader($queriedData, $addHeaders);
        $this->assertEquals($res, $verify);
    }

    public function testBuildGetHeader()
    {
        $remote = app('App\Infrastructure\Remote\FileGetContents\Remote');

        $verify = [
            'Content-Type: application/x-www-form-urlencoded'
        ];
        $res = $remote->buildGetHeader();
        $this->assertEquals($res, $verify);

        $addHeaders = ['testHeader' =>  'testHeaderValue'];
        $verify = [
            'Content-Type: application/x-www-form-urlencoded',
            'testHeader: testHeaderValue'
        ];
        $res = $remote->buildGetHeader($addHeaders);
        $this->assertEquals($res, $verify);

        $addHeaders = ['Content-Type'   =>  'image/jpeg'];
        $verify = [
            'Content-Type: image/jpeg'
        ];
        $res = $remote->buildGetHeader($addHeaders);
        $this->assertEquals($res, $verify);
    }

    public function testBuildPostConetxt()
    {
        $remote = app('App\Infrastructure\Remote\FileGetContents\Remote');

        $schemeVal = 'http';
        $params = ['id' =>  'US'];
        $queriedData = $remote->buildData($params);
        $buildHeaders = $remote->buildPostHeader($queriedData);
        $verify = [
            $schemeVal => [
                "method"  => "POST",
                "header"  => implode("\r\n", $buildHeaders),
                "content" => $queriedData
            ]
        ];
        $scheme = new Scheme($schemeVal);
        $res = $remote->buildPostConetxt($scheme, $buildHeaders, $queriedData);
        $this->assertEquals($res, $verify);

        $schemeVal = 'https';
        $verify = [
            $schemeVal => [
                "method"  => "POST",
                "header"  => implode("\r\n", $buildHeaders),
                "content" => $queriedData
            ]
        ];
        $scheme = new Scheme($schemeVal);
        $res = $remote->buildPostConetxt($scheme, $buildHeaders, $queriedData);
        $this->assertEquals($res, $verify);
    }

    public function testBuildGetContext()
    {
        $remote = app('App\Infrastructure\Remote\FileGetContents\Remote');

        $schemeVal = 'http';
        $buildHeaders = $remote->buildGetHeader();
        $verify = [
            $schemeVal => [
                "method"  => "GET",
                "header"  => implode("\r\n", $buildHeaders)
            ]
        ];
        $scheme = new Scheme($schemeVal);
        $res = $remote->buildGetContext($scheme, $buildHeaders);
        $this->assertEquals($res, $verify);

        $schemeVal = 'https';
        $verify = [
            $schemeVal => [
                "method"  => "GET",
                "header"  => implode("\r\n", $buildHeaders)
            ]
        ];
        $scheme = new Scheme($schemeVal);
        $res = $remote->buildGetContext($scheme, $buildHeaders);
        $this->assertEquals($res, $verify);
    }

    public function testSend()
    {
        $remote = app('App\Infrastructure\Remote\FileGetContents\Remote');

        $schemeVal = 'https';
        $host = 'vm.general.com';
        $url = 'country/get/US';

        $scheme = new Scheme($schemeVal);
        $fullUrl = $remote->buildUrl($scheme, $host, $url);
        $queriedData = $remote->buildData();
        $buildHeaders = $remote->buildPostHeader($queriedData);
        $context = $remote->buildPostConetxt($scheme, $buildHeaders, $queriedData);
        $streamContextOption = new StreamContextOption('ssl', 'cafile', env('GENERAL_CACERT_FILE'));
        $streamContext = $remote->buildStreamContext($context, [$streamContextOption]);
        $res = $remote->send($fullUrl, $streamContext);
        $this->assertEquals($res->getStatus(), 200);
    }

    public function testSendPost()
    {
        $remote = app('App\Infrastructure\Remote\FileGetContents\Remote');

        $scheme = new Scheme('https');
        $host = 'vm.general.com';
        $url = 'country/get/US';
        $streamContextOption = new StreamContextOption('ssl', 'cafile', env('GENERAL_CACERT_FILE'));

        $res = $remote->sendPost($scheme, $host, $url, null, null, [$streamContextOption]);
        $this->assertEquals($res->getStatus(), 200);
    }

    public function testSendGet()
    {
        $remote = app('App\Infrastructure\Remote\FileGetContents\Remote');

        $scheme = new Scheme('https');
        $host = 'vm.general.com';
        $url = 'country/get/US';
        $streamContextOption = new StreamContextOption('ssl', 'cafile', env('GENERAL_CACERT_FILE'));

        $res = $remote->sendGet($scheme, $host, $url, null, null, [$streamContextOption]);
        $this->assertEquals($res->getStatus(), 200);
    }

}
