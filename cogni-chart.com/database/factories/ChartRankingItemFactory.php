<?php

use Faker\Generator as Faker;

$factory->define(
    App\Infrastructure\Eloquents\ChartRankingItem::class,
    function (Faker $faker) {
        static $callCount = 0;
        static $already = [];
        $requires = [
            [
                'id' => '0123456789abcdef0123456789abcdef',
                'chart_arist' => 'Ed Sheeran',
                'chart_music' => 'Shape Of You',
                'artist_id' => '0123456789abcdef0123456789abcdef',
                'music_id' => '0123456789abcdef0123456789abcdef'
            ],
            [
                'id' => '1123456789abcdef0123456789abcdef',
                'chart_arist' => 'Kendrick Lamar',
                'chart_music' => 'Humble.',
                'artist_id' => '1123456789abcdef0123456789abcdef',
                'music_id' => ''
            ],
            [
                'id' => '2123456789abcdef0123456789abcdef',
                'chart_arist' => 'Post Malone Featuring 21 Savage',
                'chart_music' => 'Rockstar',
                'artist_id' => '',
                'music_id' => '2123456789abcdef0123456789abcdef'
            ],
            [
                'id' => '3123456789abcdef0123456789abcdef',
                'chart_arist' => 'Camila Cabello Featuring Young Thug',
                'chart_music' => 'Havana',
                'artist_id' => '',
                'music_id' => ''
            ],
            [
                'id' => '4123456789abcdef0123456789abcdef',
                'chart_arist' => 'Lil Pump',
                'chart_music' => 'Gucci Gang',
                'artist_id' => '4123456789abcdef0123456789abcdef',
                'music_id' => '4123456789abcdef0123456789abcdef'
            ],
            [
                'id' => '5123456789abcdef0123456789abcdef',
                'chart_arist' => 'Imagine Dragons',
                'chart_music' => 'Thunder',
                'artist_id' => '5123456789abcdef0123456789abcdef',
                'music_id' => ''
            ],
            [
                'id' => '6123456789abcdef0123456789abcdef',
                'chart_arist' => 'Sam Smith',
                'chart_music' => 'Too Good At Goodbyes',
                'artist_id' => '',
                'music_id' => '6123456789abcdef0123456789abcdef'
            ],
            [
                'id' => '7123456789abcdef0123456789abcdef',
                'chart_arist' => 'G-Eazy Featuring A$AP Rocky & Cardi B',
                'chart_music' => 'No Limit',
                'artist_id' => '',
                'music_id' => ''
            ]
        ];
        $excludeId = [
            '00000000000000000000000000000000' => true,
            '0123456789abcdef0123456789abcdef' => true,
            '1123456789abcdef0123456789abcdef' => true,
            '2123456789abcdef0123456789abcdef' => true,
            '3123456789abcdef0123456789abcdef' => true,
            '4123456789abcdef0123456789abcdef' => true,
            '5123456789abcdef0123456789abcdef' => true,
            '6123456789abcdef0123456789abcdef' => true,
            '7123456789abcdef0123456789abcdef' => true
        ];
        $excludeBusinessId['Halsey']['Bad At Love'] = true;
        $excludeBusinessId['Ed Sheeran']['Shape Of You'] = true;
        $excludeBusinessId['Kendrick Lamar']['Humble.'] = true;
        $excludeBusinessId['Post Malone Featuring 21 Savage']['Rockstar'] = true;
        $excludeBusinessId['Camila Cabello Featuring Young Thug']['Havana'] = true;
        $excludeBusinessId['Lil Pump']['Gucci Gang'] = true;
        $excludeBusinessId['Imagine Dragons']['Thunder'] = true;
        $excludeBusinessId['Sam Smith']['Too Good At Goodbyes'] = true;
        $excludeBusinessId['G-Eazy Featuring A$AP Rocky & Cardi B']['No Limit'] = true;
        $excludeArtistId = [
            '000080a1b2c3d4e5f6a7b8c9d' =>  true
        ];
        $excludeMusicId = [
            '000080a1b2c3d4e5f6a7b8c9d' =>  true
        ];

        $id             = '';
        $chart_arist    = '';
        $chart_music    = '';
        $artist_id      = '';
        $music_id       = '';
        if (isset($requires[$callCount])) {
            $id             = $requires[$callCount]['id'         ];
            $chart_arist    = $requires[$callCount]['chart_arist'];
            $chart_music    = $requires[$callCount]['chart_music'];
            $artist_id      = $requires[$callCount]['artist_id'  ];
            $music_id       = $requires[$callCount]['music_id'   ];
        } else {
            while (true) {
                $tmpId = $faker->md5;
                $tmpChartArtist = $faker->name;
                $tmpChartMusic = $faker->catchPhrase;
                $tmpArtistId = $faker->md5;
                $tmpMusicId = $faker->md5;

                if (
                    !isset($excludeId[$tmpId]) &&
                    !isset($already[$tmpId]) &&
                    !isset($excludeBusinessId[$tmpChartArtist][$tmpChartMusic]) &&
                    !isset($excludeArtistId[$tmpArtistId]) &&
                    !isset($excludeMusicId[$tmpMusicId])
                ) {
                    $id             = $tmpId;
                    $chart_arist    = $tmpChartArtist;
                    $chart_music    = $tmpChartMusic;
                    $artist_id      = $tmpArtistId;
                    $music_id       = $tmpMusicId;
                    break;
                }
            }
        }

        $already[$id] = true;
        $callCount++;
        return [
            'id'            =>  $id         ,
            'chart_artist'  =>  $chart_arist,
            'chart_music'   =>  $chart_music,
            'artist_id'     =>  $artist_id  ,
            'music_id'      =>  $music_id    
        ];
    }
);
