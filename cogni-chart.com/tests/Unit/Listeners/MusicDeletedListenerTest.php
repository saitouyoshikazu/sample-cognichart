<?php

namespace Tests\Unit\Listeners;
use Tests\TestCase;
use Mockery;
use App\Events\MusicDeleted;
use App\Listeners\MusicDeletedListener;

class MusicDeletedListenerTest extends TestCase
{

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

    private function chartRankingItemApplicationMock()
    {
        return Mockery::mock(
            'App\Application\ChartRankingItem\ChartRankingItemApplication',
            [
                app('App\Domain\ChartRankingItem\ChartRankingItemRepositoryInterface'),
                app('App\Domain\ChartRankingItem\ChartRankingItemFactoryInterface')
            ]
        )->makePartial();
    }

    public function testHandle()
    {
        $musicIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $deletePromotionVideoBrokenLinkCalled = false;
        $musicApplicationMock = $this->musicApplicationMock();
        $musicApplicationMock->shouldReceive('deletePromotionVideoBrokenLink')->andReturnUsing(
            function ($musicDXO) use ($musicIdValue, &$deletePromotionVideoBrokenLinkCalled) {
                $entityId = $musicDXO->getEntityId();
                if ($entityId->value() === $musicIdValue) {
                    $deletePromotionVideoBrokenLinkCalled = true;
                }
            }
        );
        $detachMusicCalled = false;
        $chartRankingItemApplicationMock = $this->chartRankingItemApplicationMock();
        $chartRankingItemApplicationMock->shouldReceive('detachMusic')->andReturnUsing(
            function ($chartRankingItemDXO) use ($musicIdValue, &$detachMusicCalled) {
                $musicId = $chartRankingItemDXO->getMusicId();
                if ($musicId->value() === $musicIdValue) {
                    $detachMusicCalled = true;
                }
            }
        );
        $musicDeletedListener = new MusicDeletedListener(
            $musicApplicationMock,
            $chartRankingItemApplicationMock
        );

        $deletePromotionVideoBrokenLinkCalled = false;
        $detachMusicCalled = false;
        $musicDeleted = new MusicDeleted($musicIdValue);
        $musicDeletedListener->handle($musicDeleted);
        $this->assertTrue($deletePromotionVideoBrokenLinkCalled);
        $this->assertTrue($detachMusicCalled);
    }

}
