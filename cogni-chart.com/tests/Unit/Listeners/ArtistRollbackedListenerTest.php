<?php

namespace Tests\Unit\Listeners;
use Tests\TestCase;
use Mockery;
use App\Events\ArtistRollbacked;
use App\Listeners\ArtistRollbackedListener;

class ArtistRollbackedListenerTest extends TestCase
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

    public function testHandle()
    {
        $entityIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $oldITunesArtistIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $applicationCalled = false;
        $artistApplicationMock = $this->artistApplicationMock();
        $artistApplicationMock->shouldReceive('refreshCachedEntity')->andReturnUsing(
            function ($artistDXO) use ($entityIdValue, $oldITunesArtistIdValue, &$applicationCalled)
            {
                if ($artistDXO->getEntityId()->value() === $entityIdValue &&
                    $artistDXO->getBusinessId()->iTunesArtistId()->value() === $oldITunesArtistIdValue) {
                    $applicationCalled = true;
                }
            }
        );

        $artistRollbackedListener = new ArtistRollbackedListener($artistApplicationMock);
        $artistRollbacked = new ArtistRollbacked($entityIdValue, $oldITunesArtistIdValue);
        $artistRollbackedListener->handle($artistRollbacked);
        $this->assertTrue($applicationCalled);
    }

}
