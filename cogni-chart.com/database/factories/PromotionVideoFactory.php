<?php

use Faker\Generator as Faker;

$factory->define(
    App\Infrastructure\Eloquents\PromotionVideo::class,
    function (Faker $faker) {
        static $callCount = 0;
        static $alreadyId = [];
        $musicIds = [
            '000010a1b2c3d4e5f6a7b8c9d',
            '000030a1b2c3d4e5f6a7b8c9d',
            '000050a1b2c3d4e5f6a7b8c9d',
            '000070a1b2c3d4e5f6a7b8c9d',
        ];
        $excludeId = [
            '000010a1b2c3d4e5f6a7b8c9d' =>  true,
            '000020a1b2c3d4e5f6a7b8c9d' =>  true,
            '000030a1b2c3d4e5f6a7b8c9d' =>  true,
            '000040a1b2c3d4e5f6a7b8c9d' =>  true,
            '000050a1b2c3d4e5f6a7b8c9d' =>  true,
            '000060a1b2c3d4e5f6a7b8c9d' =>  true,
            '000070a1b2c3d4e5f6a7b8c9d' =>  true,
            '000080a1b2c3d4e5f6a7b8c9d' =>  true
        ];
        $music_id = null;
        $url = null;
        $thumbnailUrl = null;
        if (isset($musicIds[$callCount])) {
            $music_id = $musicIds[$callCount];
            $url = $faker->url;
            $thumbnailUrl = $faker->url;
        } else {
            while (true) {
                $tmpMusicId = $faker->md5;
                $tmpUrl = $faker->url;
                $tmpThumbnailUrl = $faker->url;
                if (!isset($excludeId[$tmpMusicId]) && !isset($alreadyId[$tmpMusicId])) {
                    $music_id = $tmpMusicId;
                    $url = $tmpUrl;
                    $thumbnailUrl = $tmpThumbnailUrl;
                    break;
                }
            }
        }
        $alreadyId[$music_id] = true;
        $callCount++;
        return [
            'music_id'      =>  $music_id,
            'url'           =>  $url,
            'thumbnail_url' =>  $thumbnailUrl
        ];
    }
);
