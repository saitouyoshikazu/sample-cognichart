<?php

use Faker\Generator as Faker;

$factory->define(
    App\Infrastructure\Eloquents\ProvisionedChartTerm::class,
    function (Faker $faker) {
        static $callCount = 0;
        static $already = [];
        $requires = [
            [
                'id'            =>  '0013456789abcdef0123456789abcdef',
                'chart_id'      =>  '0a1b2c3d4e5f',
                'start_date'    =>  '2017-12-10',
                'end_date'      =>  '2017-12-16'
            ],
            [
                'id'            =>  '0023456789abcdef0123456789abcdef',
                'chart_id'      =>  '0a1b2c3d4e5f',
                'start_date'    =>  '2017-12-17',
                'end_date'      =>  '2017-12-23'
            ],
            [
                'id'            =>  '1013456789abcdef0123456789abcdef',
                'chart_id'      =>  'f5e4d3c2b1a0',
                'start_date'    =>  '2017-12-02',
                'end_date'      =>  '2017-12-08'
            ],
            [
                'id'            =>  '1023456789abcdef0123456789abcdef',
                'chart_id'      =>  'f5e4d3c2b1a0',
                'start_date'    =>  '2017-12-09',
                'end_date'      =>  '2017-12-15'
            ],
            [
                'id'            =>  '2013456789abcdef0123456789abcdef',
                'chart_id'      =>  'ff55ee44dd33cc22bb11aa00',
                'start_date'    =>  '2017-11-29',
                'end_date'      =>  '2017-12-05'
            ],
            [
                'id'            =>  '2023456789abcdef0123456789abcdef',
                'chart_id'      =>  'ff55ee44dd33cc22bb11aa00',
                'start_date'    =>  '2017-12-06',
                'end_date'      =>  '2017-12-12'
            ],
            [
                'id'            =>  '3013456789abcdef0123456789abcdef',
                'chart_id'      =>  '000aaa111bbb222ccc333ddd444eee55',
                'start_date'    =>  '2017-12-02',
                'end_date'      =>  '2017-12-08'
            ],
            [
                'id'            =>  '3023456789abcdef0123456789abcdef',
                'chart_id'      =>  '000aaa111bbb222ccc333ddd444eee55',
                'start_date'    =>  '2017-12-09',
                'end_date'      =>  '2017-12-15'
            ]
        ];
        $excludeId = [
            '00000000000000000000000000000000'  =>  true,
            '0013456789abcdef0123456789abcdef'  =>  true,
            '0023456789abcdef0123456789abcdef'  =>  true,
            '1013456789abcdef0123456789abcdef'  =>  true,
            '1023456789abcdef0123456789abcdef'  =>  true,
            '2013456789abcdef0123456789abcdef'  =>  true,
            '2023456789abcdef0123456789abcdef'  =>  true,
            '3013456789abcdef0123456789abcdef'  =>  true,
            '3023456789abcdef0123456789abcdef'  =>  true,
            '0113456789abcdef0123456789abcdef'  =>  true,
            '0123456789abcdef0123456789abcdef'  =>  true,
            '1113456789abcdef0123456789abcdef'  =>  true,
            '1123456789abcdef0123456789abcdef'  =>  true,
            '2113456789abcdef0123456789abcdef'  =>  true,
            '2123456789abcdef0123456789abcdef'  =>  true,
            '3113456789abcdef0123456789abcdef'  =>  true,
            '3123456789abcdef0123456789abcdef'  =>  true,
        ];
        $excludeBusinessId['0a1b2c3d4e5f']['2017-12-02'] = true;
        $excludeBusinessId['0a1b2c3d4e5f']['2017-12-09'] = true;
        $excludeBusinessId['0a1b2c3d4e5f']['2017-12-16'] = true;
        $excludeBusinessId['0a1b2c3d4e5f']['2017-12-23'] = true;
        $excludeBusinessId['f5e4d3c2b1a0']['2017-11-24'] = true;
        $excludeBusinessId['f5e4d3c2b1a0']['2017-12-01'] = true;
        $excludeBusinessId['f5e4d3c2b1a0']['2017-12-08'] = true;
        $excludeBusinessId['f5e4d3c2b1a0']['2017-12-15'] = true;
        $excludeBusinessId['ff55ee44dd33cc22bb11aa00']['2017-11-21'] = true;
        $excludeBusinessId['ff55ee44dd33cc22bb11aa00']['2017-11-28'] = true;
        $excludeBusinessId['ff55ee44dd33cc22bb11aa00']['2017-12-05'] = true;
        $excludeBusinessId['ff55ee44dd33cc22bb11aa00']['2017-12-12'] = true;
        $excludeBusinessId['000aaa111bbb222ccc333ddd444eee55']['2017-11-24'] = true;
        $excludeBusinessId['000aaa111bbb222ccc333ddd444eee55']['2017-12-01'] = true;
        $excludeBusinessId['000aaa111bbb222ccc333ddd444eee55']['2017-12-08'] = true;
        $excludeBusinessId['000aaa111bbb222ccc333ddd444eee55']['2017-12-15'] = true;

        $id         =   '';
        $chart_id   =   '';
        $start_date =   '';
        $end_date   =   '';
        $interval = new \DateInterval('P6D');
        if (isset($requires[$callCount])) {
            $id         = $requires[$callCount]['id'        ];
            $chart_id   = $requires[$callCount]['chart_id'  ];
            $start_date = $requires[$callCount]['start_date'];
            $end_date   = $requires[$callCount]['end_date'  ];
        } else {
            while (true) {
                $tmpId = $faker->md5;
                $tmpChartId = $faker->md5;
                $tmpEndDate = $faker->date('Y-m-d');

                if (!isset($excludeId[$tmpId]) && !isset($already[$tmpId]) && !isset($excludeBusinessId[$tmpChartId][$tmpEndDate])) {
                    $id         = $tmpId;
                    $chart_id   = $tmpChartId;
                    $end_date   = $tmpEndDate;
                    $dti = new \DatetimImmutable($tmpEndDate);
                    $dti = $dti->sub($interval);
                    $start_date = $dti->format('Y-m-d');
                    break;
                }
            }
        }

        $already[$id] = true;
        $callCount++;

        return [
            'id'            =>  $id,
            'chart_id'      =>  $chart_id,
            'start_date'    =>  $start_date,
            'end_date'      =>  $end_date
        ];
    }
);
