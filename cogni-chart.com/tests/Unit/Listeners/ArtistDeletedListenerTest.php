<?php

namespace Tests\Unit\Listeners;
use Tests\TestCase;
use Mockery;
use App\Events\ArtistDeleted;
use App\Listeners\ArtistDeletedListener;

class ArtistDeletedListenerTest extends TestCase
{

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

    public function testHandle()
    {
        $entityIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $oldITunesArtistIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $detachArtistCalled = false;
        $chartRankingItemApplicationMock = $this->chartRankingItemApplicationMock();
        $chartRankingItemApplicationMock->shouldReceive('detachArtist')->andReturnUsing(
            function ($chartRankingItemDXO) use ($entityIdValue, &$detachArtistCalled) {
                $artistId = $chartRankingItemDXO->getArtistId();
                if ($artistId->value() === $entityIdValue) {
                    $detachArtistCalled = true;
                }
            }
        );
        $deleteWithITunesArtistIdCalled = false;
        $musicApplicationMock = $this->musicApplicationMock();
        $musicApplicationMock->shouldReceive('deleteWithITunesArtistId')->andReturnUsing(
            function ($musicDXO) use ($oldITunesArtistIdValue, &$deleteWithITunesArtistIdCalled) {
                $iTunesArtistId = $musicDXO->getITunesArtistId();
                if ($iTunesArtistId->value() === $oldITunesArtistIdValue) {
                    $deleteWithITunesArtistIdCalled = true;
                }
            }
        );
        $artistDeletedListener = new ArtistDeletedListener(
            $chartRankingItemApplicationMock,
            $musicApplicationMock
        );

        $detachArtistCalled = false;
        $deleteWithITunesArtistIdCalled = false;
        $artistDeleted = new ArtistDeleted(
            $entityIdValue,
            $oldITunesArtistIdValue
        );
        $artistDeletedListener->handle($artistDeleted);
        $this->assertTrue($detachArtistCalled);
        $this->assertTrue($deleteWithITunesArtistIdCalled);
    }

}
