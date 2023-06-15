<?php

use Faker\Generator as Faker;

$factory->define(
    App\Infrastructure\Eloquents\ProvisionedArtist::class,
    function (Faker $faker) {
        static $callCount = 0;
        static $alreadyId = [];
        static $alreadyItunesArtistId = [];

        $requires = [
            [
                'id'                =>  '000050a1b2c3d4e5f6a7b8c9d' ,
                'artist_name'       =>  'Lil Pump'                  ,
                'itunes_artist_id'  =>  '000050a1b2c3d4e5f6a7b8c9d' ,
            ],
            [
                'id'                =>  '000060a1b2c3d4e5f6a7b8c9d' ,
                'artist_name'       =>  'Imagine Dragons'           ,
                'itunes_artist_id'  =>  '000060a1b2c3d4e5f6a7b8c9d' ,
            ],
            [
                'id'                =>  '000070a1b2c3d4e5f6a7b8c9d' ,
                'artist_name'       =>  'Sam Smith'                 ,
                'itunes_artist_id'  =>  '000070a1b2c3d4e5f6a7b8c9d' ,
            ],
            [
                'id'                =>  '000080a1b2c3d4e5f6a7b8c9d' ,
                'artist_name'       =>  'G-Eazy'                    ,
                'itunes_artist_id'  =>  '000080a1b2c3d4e5f6a7b8c9d' ,
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
        $excludeArtistName = [
            'Halsey'            =>  true,
            'Ed Sheeran'        =>  true,
            'Kendrick Lamar'    =>  true,
            'Post Malone'       =>  true,
            'Camila Cabello'    =>  true,
            'Lil Pump'          =>  true,
            'Imagine Dragons'   =>  true,
            'Sam Smith'         =>  true,
            'G-Eazy'            =>  true
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
        $excludeArtistNameLike = ['ed', 'Ed', 'pump', 'Pump'];

        $id = null;
        $artist_name = null;
        $itunes_artist_id = null;
        if (isset($requires[$callCount])) {
            $id                 =   $requires[$callCount]['id'];
            $artist_name        =   $requires[$callCount]['artist_name'];
            $itunes_artist_id   =   $requires[$callCount]['itunes_artist_id'];
        } else {
            while (true) {
                $tmpId              =   $faker->md5;
                $tmpArtistName      =   $faker->name;
                $tmpItunesArtistId  =   $faker->md5;
                if (
                    !isset($excludeId[$tmpId])  &&  !isset($alreadyId[$tmpId])  &&
                    !isset($excludeArtistName[$tmpArtistName])  &&
                    !isset($excludeItunesArtistId[$tmpItunesArtistId])  &&  !isset($alreadyItunesArtistId[$tmpItunesArtistId])
                ) {
                    $likeInvalid = false;
                    foreach ($excludeArtistNameLike AS $like) {
                        if (strpos($tmpArtistName, $like) !== false) {
                            $likeInvalid = true;
                            break;
                        }
                    }
                    if ($likeInvalid) {
                        continue;
                    }
                    $id                 =   $tmpId              ;
                    $artist_name        =   $tmpArtistName      ;
                    $itunes_artist_id   =   $tmpItunesArtistId  ;
                    break;
                }
            }
        }

        $alreadyId[$id] = true;
        $alreadyItunesArtistId[$itunes_artist_id] = true;
        $callCount++;
        return [
            'id'                =>  $id                 ,
            'artist_name'       =>  $artist_name        ,
            'itunes_artist_id'  =>  $itunes_artist_id    
        ];
    }
);
