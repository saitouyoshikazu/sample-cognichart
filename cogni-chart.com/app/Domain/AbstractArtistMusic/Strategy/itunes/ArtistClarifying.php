<?php

namespace App\Domain\AbstractArtistMusic\Strategy\itunes;
use App\Domain\AbstractArtistMusic\Strategy\AbstractArtistClarifying;

class ArtistClarifying extends AbstractArtistClarifying
{

    public function clarify(string $itunesArtistIdValue)
    {
        $itunesArtistIdValue = trim($itunesArtistIdValue);
        if (empty($itunesArtistIdValue)) {
            return;
        }
        $params = ['id' => $itunesArtistIdValue];
        $response = $this->remote->sendGet($this->scheme, $this->host, $this->uri, $params);
        if (empty($response)) {
            return;
        }
        if ($response->getStatus() !== 200) {
            return;
        }
        $body = trim($response->getBody());
        if (empty($body)) {
            return;
        }
        $decodedBody = json_decode($body, true);
        if (!isset($decodedBody['resultCount']) || intVal($decodedBody['resultCount']) === 0) {
            return;
        }
        return $decodedBody['results'][0]['artistName'];
    }

}
