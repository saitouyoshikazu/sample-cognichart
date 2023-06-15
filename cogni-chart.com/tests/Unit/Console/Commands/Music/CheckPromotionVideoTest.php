<?php

namespace Tests\Unit\Console\Commands\Music;
use Tests\TestCase;
use Mockery;
use Config;
use App\Console\Commands\Music\CheckPromotionVideo;

class CheckPromotionVideoTest extends TestCase
{

    private $musicApplicationInterfaceName = 'App\Application\Music\MusicApplicationInterface';

    private function musicApplicationMock()
    {
        return Mockery::mock(
            'App\Application\Music\MusicApplication',
            [
                app('App\Domain\Music\MusicRepositoryInterface'),
                app('App\Domain\Music\MusicFactoryInterface'),
                app('App\Domain\Music\MusicServiceInterface')
            ]
        )->makePartial();
    }

    public function testInstantiate()
    {
        $checkPromotionVideo = new CheckPromotionVideo(app($this->musicApplicationInterfaceName));
        $this->assertEquals(get_class($checkPromotionVideo), 'App\Console\Commands\Music\CheckPromotionVideo');
    }

    public function testHandle()
    {
        $musicApplicationMock = $this->musicApplicationMock();
        $conditions = [];
        $musicApplicationMock->shouldReceive('checkPromotionVideo')->andReturnUsing(
            function ($musicDXO) use(&$conditions) {
                $checkPromotionVideoConditions = $musicDXO->getCheckPromotionVideoConditions();
                if (!empty($checkPromotionVideoConditions)) {
                    $conditions = $checkPromotionVideoConditions->getConditions();
                } else {
                    $conditions = [];
                }
            }
        );
        $checkPromotionVideo = new CheckPromotionVideo($musicApplicationMock);

        $today = new \DatetimeImmutable(date('Y-m-d'));

        $verify = [];
        $checkPromotionVideo->handle();
        $this->assertEquals($conditions, $verify);

        Config::set('app.check_promotion_video.created_at_gte', 'P1Y');
        Config::set('app.check_promotion_video.created_at_lt', '');
        Config::set('app.check_promotion_video.music_id_like', '');
        $paramDate = $today->sub(new \DateInterval('P1Y'));
        $verify = [
            [
                "scope" =>  "createdAtGTE",
                "param" =>  $paramDate->format('Y-m-d')
            ]
        ];
        $checkPromotionVideo->handle();
        $this->assertEquals($conditions, $verify);

        Config::set('app.check_promotion_video.created_at_gte', '');
        Config::set('app.check_promotion_video.created_at_lt', 'P1Y');
        Config::set('app.check_promotion_video.music_id_like', '000');
        $paramDate = $today->sub(new \DateInterval('P1Y'));
        $verify = [
            [
                "scope" =>  "createdAtLT",
                "param" =>  $paramDate->format('Y-m-d')
            ],
            [
                "scope" =>  "musicIdLike",
                "param" =>  "000"
            ]
        ];
        $checkPromotionVideo->handle();
        $this->assertEquals($conditions, $verify);
    }

}
