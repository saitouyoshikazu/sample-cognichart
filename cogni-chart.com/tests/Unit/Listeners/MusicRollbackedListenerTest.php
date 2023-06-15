<?php

namespace Tests\Unit\Listeners;
use Tests\TestCase;
use Mockery;
use App\Events\MusicRollbacked;
use App\Listeners\MusicRollbackedListener;

class MusicRollbackedListenerTest extends TestCase
{

    private $musicRepositoryInterfaceName = 'App\Domain\Music\MusicRepositoryInterface';
    private $musicFactoryInterfaceName = 'App\Domain\Music\MusicFactoryInterface';

    private function musicApplicationMock()
    {
        return Mockery::mock(
            'App\Application\Music\MusicApplication',
            [
                app($this->musicRepositoryInterfaceName),
                app($this->musicFactoryInterfaceName)
            ]
        )->makePartial();
    }

    public function testHandle()
    {
        $entityIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $oldITunesArtistIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $oldMusicTitleValue = 'Shape Of You';
        $applicationCalled = false;
        $musicApplicationMock = $this->musicApplicationMock();
        $musicApplicationMock->shouldReceive('refreshCachedEntity')->andReturnUsing(
            function ($musicDXO) use ($entityIdValue, $oldITunesArtistIdValue, $oldMusicTitleValue, &$applicationCalled)
            {
                if ($musicDXO->getEntityId()->value() === $entityIdValue &&
                    $musicDXO->getBusinessId()->iTunesArtistId()->value() === $oldITunesArtistIdValue &&
                    $musicDXO->getBusinessId()->musicTitle()->value() === $oldMusicTitleValue
                ) {
                    $applicationCalled = true;
                }
            }
        );

        $musicRollbackedListener = new MusicRollbackedListener($musicApplicationMock);
        $musicRollbacked = new MusicRollbacked($entityIdValue, $oldITunesArtistIdValue, $oldMusicTitleValue);
        $musicRollbackedListener->handle($musicRollbacked);
        $this->assertTrue($applicationCalled);
    }

}
