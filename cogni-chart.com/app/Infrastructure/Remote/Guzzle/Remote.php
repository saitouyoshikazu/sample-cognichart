<?php

namespace App\Infrastructure\Remote\Guzzle;
use GuzzleHttp\Client;
use App\Infrastructure\Remote\RemoteInterface;
use App\Infrastructure\Remote\Scheme;
use App\Infrastructure\Remote\RemoteException;
use App\Infrastructure\Remote\RemoteResponse;

class Remote implements RemoteInterface
{

/**
 * GuzzleでSSL証明書を追加指定する場合は、以下のようにverifyオプションで実行する。
 * gneralサーバへの問い合わせ時には['verify' => '証明書パス']をoptionsに指定して実行すること。
 *  $client->request('GET', '/', ['verify' => '/path/to/cert.pem']);
 */

    private function buildClient(Scheme $scheme, string $host)
    {
        $host = trim($host);
        if (empty($host)) {
            throw new RemoteException('The host can\'t be set empty value.');
        }
        $uriString = "{$scheme->value()}://{$host}";
        return new Client(['base_uri' => $uriString]);
    }

    private function buildBody(array &$guzzleParameter, array $params = null)
    {
        if (empty($params)) {
            return;
        }
        $guzzleParameter['form_params'] = $params;
    }

    private function buildQuery(array &$guzzleParameter, array $params = null)
    {
        if (empty($params)) {
            return;
        }
        $guzzleParameter['query'] = $params;
    }

    private function buildHeader(array &$guzzleParameter, array $addHeaders = null)
    {
        if (empty($addHeaders)) {
            return;
        }
        $guzzleParameter['headers'] = $addHeaders;
    }

    private function buildOption(array &$guzzleParameter, array $options = null)
    {
        if (empty($options)) {
            return;
        }
        foreach ($options AS $optionName => $optionValue) {
            $guzzleParameter[$optionName] = $optionValue;
        }
    }

    public function sendPost(
        Scheme      $scheme,
        string      $host,
        string      $url = null,
        array       $params = null,
        array       $addHeaders = null,
        array       $options = null
    ) {
        $client = $this->buildClient($scheme, $host);

        $guzzleParameter = [];

        $this->buildBody($guzzleParameter, $params);

        $this->buildHeader($guzzleParameter, $addHeaders);

        $this->buildOption($guzzleParameter, $options);

        $urlString = '';
        if (!empty($url)) {
            $urlString = '/' . $url;
        }

        $response = $client->post($urlString, $guzzleParameter);
        if (empty($response)) {
            return null;
        }
        $status = $response->getStatusCode();
        $headers = $response->getHeaders();
        $body = $response->getBody();
        return new RemoteResponse($status, $headers, $body);
    }

    public function sendGet(
        Scheme      $scheme,
        string      $host,
        string      $url = null,
        array       $params = null,
        array       $addHeaders = null,
        array       $options = null
    ) {
        $client = $this->buildClient($scheme, $host);

        $guzzleParameter = [];

        $this->buildQuery($guzzleParameter, $params);

        $this->buildHeader($guzzleParameter, $addHeaders);

        $this->buildOption($guzzleParameter, $options);

        $urlString = '';
        if (!empty($url)) {
            $urlString = '/' . $url;
        }

        $response = $client->get($urlString, $guzzleParameter);
        if (empty($response)) {
            return null;
        }
        $status = $response->getStatusCode();
        $headers = $response->getHeaders();
        $body = $response->getBody();
        return new RemoteResponse($status, $headers, $body);
    }

}
