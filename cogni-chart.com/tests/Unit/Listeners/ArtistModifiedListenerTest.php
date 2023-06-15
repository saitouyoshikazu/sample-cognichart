<?php

namespace Tests\Unit\Listeners;
use Tests\TestCase;
use Mockery;
use App\Events\ArtistModified;
use App\Listeners\ArtistModifiedListener;

class ArtistModifiedListenerTest extends TestCase
{

    private $artistRepositoryInterfaceName = 'App\Domain\Artist\ArtistRepositoryInterface';
    private $artistFactoryInterfaceName = 'App\Domain\Artist\ArtistFactoryInterface';

    private function artistApplicationMock()
    {
        return Mockery::mock(
            'App\Application\Artist\ArtistApplication',
            [
                app($this->artistRepositoryInterfaceName),
                app($this->artistFactoryInterfaceName)
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
        $iTunesArtistIdValue = '000020a1b2c3d4e5f6a7b8c9d';
        $refreshCachedEntityCalled = false;
        $artistApplicationMock = $this->artistApplicationMock();
        $artistApplicationMock->shouldReceive('refreshCachedEntity')->andReturnUsing(
            function ($artistDXO) use ($entityIdValue, &$refreshCachedEntityCalled)
            {
                if ($artistDXO->getEntityId()->value() === $entityIdValue) {
                    $refreshCachedEntityCalled = true;
                }
            }
        );
        $replaceITunesArtistIdCalled = false;
        $musicApplicationMock = $this->musicApplicationMock();
        $musicApplicationMock->shouldReceive('replaceITunesArtistId')->andReturnUsing(
            function ($musicDXO) use ($oldITunesArtistIdValue, $iTunesArtistIdValue, &$replaceITunesArtistIdCalled) {
                $oldITunesArtistId = $musicDXO->getOldITunesArtistId();
                $iTunesArtistId = $musicDXO->getITunesArtistId();
                if ($oldITunesArtistId->value() === $oldITunesArtistIdValue && $iTunesArtistId->value() === $iTunesArtistIdValue) {
                    $replaceITunesArtistIdCalled = true;
                }
            }
        );
        $artistModifiedListener = new ArtistModifiedListener(
            $artistApplicationMock,
            $musicApplicationMock
        );

        $refreshCachedEntityCalled = false;
        $replaceITunesArtistIdCalled = false;
        $artistModified = new ArtistModified($entityIdValue, $oldITunesArtistIdValue, $iTunesArtistIdValue);
        $artistModifiedListener->handle($artistModified);
        $this->assertTrue($refreshCachedEntityCalled);
        $this->assertTrue($replaceITunesArtistIdCalled);
    }

}
