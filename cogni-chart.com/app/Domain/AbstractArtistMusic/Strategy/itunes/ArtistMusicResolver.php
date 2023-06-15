<?php

namespace App\Domain\AbstractArtistMusic\Strategy\itunes;
use App\Domain\AbstractArtistMusic\Strategy\AbstractArtistMusicResolver;
use App\Domain\ValueObjects\ChartArtist;
use App\Domain\ValueObjects\ChartMusic;
use App\Domain\AbstractArtistMusic\Strategy\Resolved;

class ArtistMusicResolver extends AbstractArtistMusicResolver
{

    protected function executeResolve(ChartArtist $chartArtist, ChartMusic $chartMusic, array $response): ?Resolved
    {
        if (empty($response)) {
            return null;
        }
        if ($response['resultCount'] == 0) {
            return null;
        }
        $artistValue = $this->symbolHandler->removeSymbol($chartArtist->value());
        $musicValue = $this->symbolHandler->removeSymbol($chartMusic->value());
        $sArtistValue = strtolower($artistValue);
        $sMusicValue = strtolower($musicValue);
        $candidates = $response['results'];
        foreach ($candidates AS $candidate) {
            if (empty($candidate['kind']) || $candidate['kind'] !== 'song') {
                continue;
            }
            $candidateArtist = $this->symbolHandler->removeSymbol($candidate['artistName']);
            $sCandidateArtist = strtolower($candidateArtist);
            $candidateMusic = $this->symbolHandler->removeSymbol($candidate['trackName']);
            $sCandidateMusic = strtolower($candidateMusic);
            if (
                (strpos($sMusicValue , $sCandidateMusic ) !== false || strpos($sCandidateMusic , $sMusicValue ) !== false) &&
                (strpos($sArtistValue, $sCandidateArtist) !== false || strpos($sCandidateArtist, $sArtistValue) !== false)
            ) {
                $iTunesBaseUrlValue = "";
                if (!empty($candidate['trackViewUrl'])) {
                    $parsed = parse_url($candidate['trackViewUrl']);
                    $iTunesBaseUrlValue = $parsed['scheme']."://".$parsed['host'].$parsed['path'];
                    parse_str($parsed['query'], $query);
                    if (!empty($query['i'])) {
                        $iTunesBaseUrlValue .= "?i=".$query['i'];
                    }
                }

                $resolved = new Resolved(
                    $candidate['artistName'],
                    $candidate['trackName'],
                    $candidate['artistId'],
                    $iTunesBaseUrlValue
                );
                return $resolved;
            }
        }
        return null;
    }

}
