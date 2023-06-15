<?php

namespace App\Infrastructure\Remote\FileGetContents;
use App\Infrastructure\Remote\RemoteInterface;
use App\Infrastructure\Remote\Scheme;
use App\Infrastructure\Remote\RemoteException;
use App\Infrastructure\Remote\RemoteResponse;

class Remote implements RemoteInterface
{

    public function buildUrl(Scheme $scheme, string $host, string $url = null)
    {
        $host = trim($host);
        if (empty($host)) {
            throw new RemoteException('The host can\'t be set empty value.');
        }
        $urlString = "{$scheme->value()}://{$host}";
        if (!empty($url)) {
            $urlString .= "/" . $url;
        }
        return $urlString;
    }

    public function buildData(array $params = null)
    {
        $data = "";
        if (!empty($params)) {
            $data = http_build_query($params, "", "&");
        }
        return $data;
    }

    public function buildPostHeader(string $queriedData, array $addHeaders = null)
    {
        return $this->buildHeader(
            [
                'Content-Type'      =>  'application/x-www-form-urlencoded',
                'Content-Length'    =>  strlen($queriedData)
            ],
            $addHeaders
        );
    }

    public function buildGetHeader(array $addHeaders = null)
    {
        return $this->buildHeader(
            [
                'Content-Type'      =>  'application/x-www-form-urlencoded',
            ],
            $addHeaders
        );
    }

    public function buildPostConetxt(Scheme $scheme, array $buildHeaders, string $queriedData)
    {
        return [
            $scheme->value() => [
                "method"  => "POST",
                "header"  => implode("\r\n", $buildHeaders),
                "content" => $queriedData
            ]
        ];
    }

    public function buildGetContext(Scheme $scheme, array $buildHeaders)
    {
        return [
            $scheme->value() => [
                "method"  => "GET",
                "header"  => implode("\r\n", $buildHeaders)
            ]
        ];
    }

    public function buildStreamContext(array $context, array $streamContextOptions = null)
    {
        $streamContext = stream_context_create($context);
        if (!empty($streamContextOptions)) {
            foreach ($streamContextOptions AS $streamContextOption) {
                if (!$streamContextOption instanceof StreamContextOption) {
                    continue;
                }
                stream_context_set_option(
                    $streamContext,
                    $streamContextOption->getWrapper(),
                    $streamContextOption->getOption(),
                    $streamContextOption->getValue()
                );
            }
        }
        return $streamContext;
    }

    public function send(string $fullUrl, $streamContext)
    {
        $body = file_get_contents($fullUrl, false, $streamContext);
        if ($body === false) {
            return null;
        }

        preg_match('/HTTP\/[1|2]\.[0|1|x] ([0-9]{3})/', $http_response_header[0], $matches);
        $status = $matches[1];

        $headers = $http_response_header;

        return new RemoteResponse($status, $headers, $body);
    }

    public function sendPost(
        Scheme      $scheme,
        string      $host,
        string      $url = null,
        array       $params = null,
        array       $addHeaders = null,
        array       $streamContextOptions = null
    ) {
        $fullUrl = $this->buildUrl($scheme, $host, $url);

        $data = $this->buildData($params);

        $header = $this->buildPostHeader($data, $addHeaders);

        $context = $this->buildPostConetxt($scheme, $header, $data);

        $streamContext = $this->buildStreamContext($context, $streamContextOptions);

        return $this->send($fullUrl, $streamContext);
    }

    public function sendGet(
        Scheme      $scheme,
        string      $host,
        string      $url = null,
        array       $params = null,
        array       $addHeaders = null,
        array       $streamContextOptions = null
    ) {
        $fullUrl = $this->buildUrl($scheme, $host, $url);

        $data = $this->buildData($params);
        if (!empty($data)) {
            $fullUrl .= "?" . $data;
        }

        $header = $this->buildGetHeader($addHeaders);

        $context = $this->buildGetContext($scheme, $header);

        $streamContext = $this->buildStreamContext($context, $streamContextOptions);

        return $this->send($fullUrl, $streamContext);
    }

    private function buildHeader(array $default, array $add = null)
    {
        $headers = $default;
        if (!empty($add)) {
            $headers = array_merge($headers, $add);
        }
        $build = [];
        foreach ($headers AS $headerKey => $headerValue) {
            $build[] = "{$headerKey}: {$headerValue}";
        }
        return $build;
    }

}
