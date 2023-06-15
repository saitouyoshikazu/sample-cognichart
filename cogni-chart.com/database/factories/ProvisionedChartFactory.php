<?php

use Faker\Generator as Faker;

$factory->define(
    App\Infrastructure\Eloquents\ProvisionedChart::class,
    function (Faker $faker) {
        static $callCount = 0;
        static $already = [];
        $requires = [
            [
                'id'                =>  'ff55ee44dd33cc22bb11aa00',
                'country_id'        =>  'AU',
                'display_position'  =>  1,
                'chart_name'        =>  'ARIA Singles Chart',
                'scheme'            =>  'https',
                'host'              =>  'www.ariacharts.com.au',
                'uri'               =>  'charts/singles-chart'
            ],
            [
                'id'                =>  '000aaa111bbb222ccc333ddd444eee',
                'country_id'        =>  'ZZ',
                'display_position'  =>  2,
                'chart_name'        =>  'Country Does Not Exist',
                'scheme'            =>  'https',
                'host'              =>  'www.country.doesnot.exist.com',
                'uri'               =>  'country/doesnot/exist'
            ],
            [
                'id'                =>  '000aaa111bbb222ccc333ddd444eee55',
                'country_id'        =>  'GB',
                'display_position'  =>  3,
                'chart_name'        =>  'Official Singles Chart Top 200',
                'scheme'            =>  'https',
                'host'              =>  'www.officialcharts.com',
                'uri'               =>  'charts/singles-chart'
            ],
        ];
        $excludeId = [
            '0a1b2c3d4e5f'                      =>  true,
            'f5e4d3c2b1a0'                      =>  true,
            '00aa11bb22cc33dd44ee55ff'          =>  true,
            'ff55ee44dd33cc22bb11aa00'          =>  true,
            '000aaa111bbb222ccc333ddd444eee'    =>  true,
            '000aaa111bbb222ccc333ddd444eee55'  =>  true,
            '00000000000000000000000000000000'  =>  true
        ];
        $excludeBusinessId = [
            'AU'    =>  [
                'ARIA SINGLES CHART'    =>  true
             ],
            'GB'    =>  [
                'Official Singles Chart Top 100'    =>  true,
                'Official Singles Chart Top 200'    =>  true
            ],
            'US'    =>  [
                'USA Singles Chart'     =>  true,
                'Billboard Hot 200'     =>  true
            ],
            'ZZ'    =>  [
                'Country Does Not Exist'    =>  true,
                'DoesNotExist'              =>  true
            ]
        ];

        $id                 = '';
        $country_id         = '';
        $display_position   = null;
        $chart_name         = '';
        $scheme             = '';
        $host               = '';
        $uri                = '';

        if (array_key_exists($callCount, $requires)) {
            $id                 = $requires[$callCount]['id'              ];
            $country_id         = $requires[$callCount]['country_id'      ];
            $display_position   = $requires[$callCount]['display_position'];
            $chart_name         = $requires[$callCount]['chart_name'      ];
            $scheme             = $requires[$callCount]['scheme'          ];
            $host               = $requires[$callCount]['host'            ];
            $uri                = $requires[$callCount]['uri'             ];
        } else {
            while (true) {
                $tmpId = $faker->md5;
                $tmpCountryId = strtoupper($faker->countryCode);
                $tmpChartName = $faker->company;

                if (!isset($excludeId[$tmpId]) && !isset($excludeBusinessId[$tmpCountryId][$tmpChartName]) && !isset($already[$tmpId])) {
                    $id                 =   $tmpId;
                    $country_id         =   $tmpCountryId;
                    $display_position   =   $callCount;
                    $chart_name         =   $tmpChartName;
                    $scheme             =   'https';
                    $host               =   $faker->domainName;
                    $uri                =   $faker->word;
                    break;
                }
            }
        }
        $already[$id] = true;
        $callCount++;
        return [
            'id'                =>  $id              ,
            'country_id'        =>  $country_id      ,
            'display_position'  =>  $display_position,
            'chart_name'        =>  $chart_name      ,
            'scheme'            =>  $scheme          ,
            'host'              =>  $host            ,
            'uri'               =>  $uri              
        ];
    }
);
