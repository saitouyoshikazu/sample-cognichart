<?php

namespace Tests\Unit\Listeners;
use Tests\TestCase;
use Mockery;
use App\Events\MusicModified;
use App\Listeners\MusicModifiedListener;

class MusicModifiedListenerTest extends TestCase
{

    private $musicRepositoryInterfaceName = 'App\Domain\Music\MusicRepositoryInterface';
    private $musicFactoryInterfaceName = 'App\Domain\Music\MusicFactoryInterface';
    private $musicServiceInterfaceName = 'App\Domain\Music\MusicServiceInterface';

    private function musicApplicationMock()
    {
        return Mockery::mock(
            'App\Application\Music\MusicApplication',
            [
                app($this->musicRepositoryInterfaceName),
                app($this->musicFactoryInterfaceName),
                app($this->musicServiceInterfaceName)
            ]
        )->makePartial();
    }

    public function testHandle()
    {
        $entityIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $oldITunesArtistIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $oldMusicTitleValue = 'Shape Of You';

        $refreshCachedEntityCalled = false;
        $deletePromotionVideoBrokenLinkCalled = false;
        $musicApplicationMock = $this->musicApplicationMock();
        $musicApplicationMock->shouldReceive('refreshCachedEntity')->andReturnUsing(
            function ($musicDXO) use ($entityIdValue, &$refreshCachedEntityCalled)
            {
                if ($musicDXO->getEntityId()->value() === $entityIdValue) {
                    $refreshCachedEntityCalled = true;
                }
            }
        );
        $musicApplicationMock->shouldReceive('deletePromotionVideoBrokenLink')->andReturnUsing(
            function ($musicDXO) use ($entityIdValue, &$deletePromotionVideoBrokenLinkCalled)
            {
                if ($musicDXO->getEntityId()->value() === $entityIdValue) {
                    $deletePromotionVideoBrokenLinkCalled = true;
                }
            }
        );

        $musicModifiedListener = new MusicModifiedListener($musicApplicationMock);
        $musicModified = new MusicModified($entityIdValue, $oldITunesArtistIdValue, $oldMusicTitleValue);
        $musicModifiedListener->handle($musicModified);
        $this->assertTrue($refreshCachedEntityCalled);
        $this->assertTrue($deletePromotionVideoBrokenLinkCalled);
    }

}
