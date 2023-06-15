<?php

namespace Tests\Unit\Listeners;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Event;
use Mockery;
use App\Infrastructure\Eloquents\Artist;
use App\Infrastructure\Eloquents\Music;
use App\Infrastructure\Eloquents\ProvisionedArtist;
use App\Infrastructure\Eloquents\ProvisionedMusic;
use App\Infrastructure\Eloquents\PromotionVideo;
use App\Events\MusicRegistered;
use App\Listeners\MusicRegisteredListener;

class MusicRegisteredListenerTest extends TestCase
{

    use DatabaseMigrations;

    private $artistApplicationInterfaceName = 'App\Application\Artist\ArtistApplicationInterface';
    private $musicApplicationInterfaceName = 'App\Application\Music\MusicApplicationInterface';

    private function artistApplicationMock()
    {
        return Mockery::mock(
            'App\Application\Artist\ArtistApplication',
            [
                app('App\Domain\Artist\ArtistRepositoryInterface'),
                app('App\Domain\Artist\ArtistFactoryInterface')
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

    public function setUp()
    {
        parent::setUp();

        factory(Artist::class, 10)->create();
        factory(Music::class, 10)->create();
        factory(ProvisionedArtist::class, 10)->create();
        factory(ProvisionedMusic::class, 10)->create();
        factory(PromotionVideo::class, 10)->create();
    }

    public function tearDown()
    {
        Mockery::close();

        Artist::truncate();
        Music::truncate();
        ProvisionedArtist::truncate();
        ProvisionedMusic::truncate();
        PromotionVideo::truncate();

        parent::tearDown();
    }

    public function testHandleMusicEntityNotFound()
    {
        $musicApplicationMock = $this->musicApplicationMock();
        $musicApplicationMock->shouldReceive('find')->andReturn(null);
        $musicRegisteredListener = new MusicRegisteredListener(
            app($this->artistApplicationInterfaceName),
            $musicApplicationMock
        );

        $entityIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $musicRegistered = new MusicRegistered($entityIdValue);
        $result = $musicRegisteredListener->handle($musicRegistered);
        $this->assertFalse($result);
    }

    public function testHandleArtistEntityNotFound()
    {
        $artistApplicationMock = $this->artistApplicationMock();
        $artistApplicationMock->shouldReceive('get')->andReturn(null);
        $musicRegisteredListener = new MusicRegisteredListener(
            $artistApplicationMock,
            app($this->musicApplicationInterfaceName)
        );

        $entityIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $musicRegistered = new MusicRegistered($entityIdValue);
        $result = $musicRegisteredListener->handle($musicRegistered);
        $this->assertFalse($result);
    }

    public function testHandle()
    {
        Event::shouldReceive('dispatch')->andReturnUsing(function ($event) {});
        $musicRegisteredListener = new MusicRegisteredListener(
            app($this->artistApplicationInterfaceName),
            app($this->musicApplicationInterfaceName)
        );

        $entityIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $musicRegistered = new MusicRegistered($entityIdValue);
        $result = $musicRegisteredListener->handle($musicRegistered);
        $this->assertTrue($result);
    }

}
