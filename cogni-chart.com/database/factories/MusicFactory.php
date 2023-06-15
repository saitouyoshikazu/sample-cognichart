<?php

use Faker\Generator as Faker;

$factory->define(
    App\Infrastructure\Eloquents\Music::class,
    function (Faker $faker) {
        static $callCount = 0;
        static $alreadyId = [];
        static $alreadyItunesArtistId = [];

        $requires = [
            [
                'id'                =>  '000010a1b2c3d4e5f6a7b8c9d' ,
                'itunes_artist_id'  =>  '000010a1b2c3d4e5f6a7b8c9d' ,
                'music_title'       =>  'Shape Of You'              ,
            ],
            [
                'id'                =>  '000020a1b2c3d4e5f6a7b8c9d' ,
                'itunes_artist_id'  =>  '000020a1b2c3d4e5f6a7b8c9d' ,
                'music_title'       =>  'Humble.'                   ,
            ],
            [
                'id'                =>  '000030a1b2c3d4e5f6a7b8c9d' ,
                'itunes_artist_id'  =>  '000030a1b2c3d4e5f6a7b8c9d' ,
                'music_title'       =>  'Rockstar'                  ,
            ],
            [
                'id'                =>  '000040a1b2c3d4e5f6a7b8c9d' ,
                'itunes_artist_id'  =>  '000040a1b2c3d4e5f6a7b8c9d' ,
                'music_title'       =>  'Havana'                    ,
            ],
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
        $excludeItunesArtistId = [
            '000010a1b2c3d4e5f6a7b8c9d' =>  true,
            '000020a1b2c3d4e5f6a7b8c9d' =>  true,
            '000030a1b2c3d4e5f6a7b8c9d' =>  true,
            '000040a1b2c3d4e5f6a7b8c9d' =>  true,
            '000050a1b2c3d4e5f6a7b8c9d' =>  true,
            '000060a1b2c3d4e5f6a7b8c9d' =>  true,
            '000070a1b2c3d4e5f6a7b8c9d' =>  true,
            '000080a1b2c3d4e5f6a7b8c9d' =>  true
        ];
        $excludeMusicTitle = [
            'Bad At Love'           =>  true,
            'Shape Of You'          =>  true,
            'Humble'                =>  true,
            'Rockstar'              =>  true,
            'Havana'                =>  true,
            'Gucci Gang'            =>  true,
            'Thunder'               =>  true,
            'Too Good At Goodbyes'  =>  true,
            'No Limit'              =>  true,
            'Not Found'             =>  true,
        ];
        $excludeMusicTitleLike = ['of', 'Of', 'gucci', 'Gucci'];

        $id = null;
        $itunes_artist_id = null;
        $music_title = null;
        if (isset($requires[$callCount])) {
            $id                 =   $requires[$callCount]['id'];
            $itunes_artist_id   =   $requires[$callCount]['itunes_artist_id'];
            $music_title        =   $requires[$callCount]['music_title'];
        } else {
            while (true) {
                $tmpId              =   $faker->md5;
                $tmpItunesArtistId  =   $faker->md5;
                $tmpMusicTitle      =   $faker->catchPhrase;
                if (
                    !isset($excludeId[$tmpId]                           )   &&  !isset($alreadyId[$tmpId]                           )   &&
                    !isset($excludeItunesArtistId[$tmpItunesArtistId]   )   &&  !isset($alreadyItunesArtistId[$tmpItunesArtistId]   )   &&
                    !isset($excludeMusicTitle[$tmpMusicTitle]           )
                ) {
                    $likeInvalid = false;
                    foreach ($excludeMusicTitleLike AS $like) {
                        if (strpos($tmpMusicTitle, $like) !== false) {
                            $likeInvalid = true;
                            break;
                        }
                    }
                    if ($likeInvalid) {
                        continue;
                    }
                    $id                 =   $tmpId              ;
                    $itunes_artist_id   =   $tmpItunesArtistId  ;
                    $music_title        =   $tmpMusicTitle      ;
                    break;
                }
            }
        }

        $alreadyId[$id] = true;
        $alreadyItunesArtistId[$itunes_artist_id] = true;
        $callCount++;
        return [
            'id'                =>  $id                 ,
            'itunes_artist_id'  =>  $itunes_artist_id   ,
            'music_title'       =>  $music_title        ,
        ];
    }
);
