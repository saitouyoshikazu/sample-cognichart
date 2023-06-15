<?php

namespace App\Domain\Music;
use Config;
use App\Infrastructure\Remote\RemoteInterface;
use App\Domain\ValueObjects\ArtistName;
use App\Domain\ValueObjects\MusicTitle;
use App\Domain\Music\MusicEntity;
use App\Infrastructure\Remote\Scheme;

class MusicService implements MusicServiceInterface
{

    private $remote;

    public function __construct(RemoteInterface $remote)
    {
        $this->remote = $remote;
    }

    public function searchPromotionVideo(ArtistName $artistName, MusicTitle $musicTitle)
    {
        $q = $artistName->value() . ' ' . $musicTitle->value();
        $settings = Config::get('app.youtube_data_api_v3');
        $parameters = [
            'type'          =>  'video',
            'part'          =>  'id,snippet',
            'fields'        =>  'items(id(videoId),snippet(thumbnails(default(url))))',
            'maxResults'    =>  1,
            'order'         =>  'relevance',
            'q'             =>  $q,
            'key'           =>  $settings['key']
        ];
        $result = $this->remote->sendGet(new Scheme($settings['scheme']), $settings['host'], $settings['search']['uri'], $parameters);
        $bodyJson = $result->getBody();
        if (empty($bodyJson)) {
            return null;
        }
        $body = json_decode($bodyJson, true);
        if (empty($body)) {
            return null;
        }
        if (empty($body['items'][0])) {
            return null;
        }
        $item = $body['items'][0];
        if (empty($item['id']['videoId'])) {
            return null;
        }
        $row['url'] = $item['id']['videoId'];
        $row['thumbnail_url'] = empty($item['snippet']['thumbnails']['default']['url']) ? null : $item['snippet']['thumbnails']['default']['url'];
        return $row;
    }

    public function checkPromotionVideo(MusicEntity $musicEntity)
    {
        $promotionVideoUrl = $musicEntity->promotionVideoUrl();
        if (empty($promotionVideoUrl)) {
            return false;
        }
        $id = $promotionVideoUrl->value();
        $settings = Config::get('app.youtube_data_api_v3');
        $parameters = [
            'id'    =>  $id,
            'part'  =>  'status',
            'key'   =>  $settings['key'],
        ];
        $result = $this->remote->sendGet(new Scheme($settings['scheme']), $settings['host'], $settings['check']['uri'], $parameters);
        $bodyJson = $result->getBody();
        if (empty($bodyJson)) {
            return true;
        }
        $body = json_decode($bodyJson, true);
        if (empty($body)) {
            return true;
        }
        if (empty($body['items'][0])) {
            return false;
        }
        return true;
    }

}
