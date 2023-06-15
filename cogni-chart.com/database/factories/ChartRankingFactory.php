<?php

use Faker\Generator as Faker;

$factory->define(
    App\Infrastructure\Eloquents\ChartRanking::class,
    function (Faker $faker) {
        static $callCount = 0;
        static $already = [];

        $chartTermIdIndex = $callCount / 8;
        $chartRankingItemIdIndex = $callCount % 8;
        $ranking = $chartRankingItemIdIndex + 1;

        $chartTermIds = [
            '0013456789abcdef0123456789abcdef',
            '0113456789abcdef0123456789abcdef',
            '1013456789abcdef0123456789abcdef',
            '1113456789abcdef0123456789abcdef',
            '2013456789abcdef0123456789abcdef',
            '2113456789abcdef0123456789abcdef',
            '3013456789abcdef0123456789abcdef',
            '3113456789abcdef0123456789abcdef',
        ];

        $chartRankingItemIds = [
            '0123456789abcdef0123456789abcdef',
            '1123456789abcdef0123456789abcdef',
            '2123456789abcdef0123456789abcdef',
            '3123456789abcdef0123456789abcdef',
            '4123456789abcdef0123456789abcdef',
            '5123456789abcdef0123456789abcdef',
            '6123456789abcdef0123456789abcdef',
            '7123456789abcdef0123456789abcdef',
        ];

        $chartTermId = '';
        $chartRankingItemId = $chartRankingItemIds[$chartRankingItemIdIndex];
        if (isset($chartTermIds[$chartTermIdIndex])) {
            $chartTermId = $chartTermIds[$chartTermIdIndex];
        } else {
            while (true) {
                $tmpChartTermId = $faker->md5;
                if (!isset($already[$tmpChartTermId])) {
                    $chartTermId = $tmpChartTermId;
                    break;
                }
            }
        }
        $already[$chartTermId] = true;
        $callCount++;
        return [
            'chart_term_id'             =>  $chartTermId,
            'ranking'                   =>  $ranking,
            'chart_ranking_item_id'     =>  $chartRankingItemId
        ];
    }
);
