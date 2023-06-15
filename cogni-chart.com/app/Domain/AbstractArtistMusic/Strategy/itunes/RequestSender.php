<?php

namespace App\Domain\AbstractArtistMusic\Strategy\itunes;
use App\Domain\AbstractArtistMusic\Strategy\AbstractRequestSender;
use App\Infrastructure\Remote\RemoteInterface;
use App\Domain\ValueObjects\ChartArtist;
use App\Domain\ValueObjects\ChartMusic;

use App\Infrastructure\Remote\Scheme;

class RequestSender extends AbstractRequestSender
{

    protected function execute(RemoteInterface $remote, ChartArtist $chartArtist, ChartMusic $chartMusic)
    {
        $musicValue = $this->symbolHandler->removeSymbol($chartMusic->value());
        $artistValue = $this->symbolHandler->removeSymbol($chartArtist->value());
        $musicValue = $this->convertMusic($musicValue);
        $artistValue = $this->convertArtist($artistValue);
        if (empty($musicValue) || empty($artistValue)) {
            return null;
        }
        $queryString = $this->createQueryString($musicValue, $artistValue);
        $uri = "{$this->uri}?{$queryString}";
        $response = $remote->sendGet($this->scheme, $this->host, $uri);
        if (empty($response)) {
            return null;
        }
        if ($response->getStatus() !== 200) {
            return null;
        }
        $body = trim($response->getBody());
        if (empty($body)) {
            return null;
        }
        $decodedBody = json_decode($body, true);
        if (!isset($decodedBody['resultCount']) || intVal($decodedBody['resultCount']) === 0) {
            return null;
        }
        return $decodedBody;
    }

    private function convertMusic(string $musicValue)
    {
        $musicValue = trim($musicValue);
        if (empty($musicValue)) {
            return null;
        }
        $exploded = explode(" ", $musicValue);
        $encoded = array_map(
            function ($arg) {
                return rawurlencode(strtolower($arg));
            },
            $exploded
        );
        $converted = implode("+", $encoded);
        return $converted;
    }

    private function convertArtist(string $artistValue)
    {
        $artistValue = trim($artistValue);
        if (empty($artistValue)) {
            return null;
        }
        $replaces = [
            '&',
            ',',
            '/'
        ];
        $artistValue = str_replace($replaces, " ", $artistValue);
        $ignores = [
            'ft',
            'feat',
            'feat.',
            'feature',
            'feature.',
            'featuring',
            'featuring.',
            'and',
            'with',
            'vs'
        ];
        $delimited = explode(" ", $artistValue);
        if (empty($delimited)) {
            return null;
        }
        $delimited = array_map("strtolower", $delimited);
        $convertedArray = [];
        foreach ($delimited AS $element) {
            $ignored = false;
            if ($this->symbolHandler->isAlphaNumberSymbol($element)) {
                foreach ($ignores AS $ignore) {
                    if ($element === $ignore) {
                        $ignored = true;
                        break;
                    }
                }
            }
            if ($ignored === false && strlen($element) > 1) {
                $convertedArray[] = rawurlencode($element);
            }
        }
        $converted = implode("+", $convertedArray);
        return $converted;
    }

    private function createQueryString(string $musicValue, string $artistValue)
    {
        $parameters = [
            "term={$musicValue}+{$artistValue}",
            "country=us",
            "media=music"
        ];
        $queryString = implode("&", $parameters);
        return $queryString;
    }

}
