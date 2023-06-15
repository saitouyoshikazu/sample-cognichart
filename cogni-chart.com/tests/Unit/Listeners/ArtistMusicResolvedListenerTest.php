<?php

namespace Tests\Unit\Listeners;
use Tests\TestCase;
use Mockery;
use Log;
use App\Infrastructure\Eloquents\ChartRankingItem;
use App\Infrastructure\Eloquents\Artist;
use App\Infrastructure\Eloquents\Music;
use App\Infrastructure\Eloquents\ProvisionedArtist;
use App\Infrastructure\Eloquents\ProvisionedMusic;
use App\Events\ArtistMusicResolved;
use App\Listeners\ArtistMusicResolvedListener;
use App\Application\DXO\ArtistDXO;
use App\Application\DXO\MusicDXO;
use App\Application\DXO\ChartRankingItemDXO;
use App\Domain\EntityId;
use App\Domain\Artist\ArtistException;
use App\Domain\Music\MusicException;


class ArtistMusicResolvedListenerTest extends TestCase
{

    private $artistApplicationInterfaceName = 'App\Application\Artist\ArtistApplicationInterface';
    private $musicApplicationInterfaceName = 'App\Application\Music\MusicApplicationInterface';
    private $chartRankingItemApplicationInterfaceName = 'App\Application\ChartRankingItem\ChartRankingItemApplicationInterface';

    public function setUp()
    {
        parent::setUp();
        factory(ChartRankingItem::class, 8)->create();
        factory(Artist::class, 4)->create();
        factory(Music::class, 4)->create();
        factory(ProvisionedArtist::class, 4)->create();
        factory(ProvisionedMusic::class, 4)->create();
    }

    public function tearDown()
    {
        Mockery::close();

        ChartRankingItem::truncate();
        Artist::truncate();
        Music::truncate();
        ProvisionedArtist::truncate();
        ProvisionedMusic::truncate();

        parent::tearDown();
    }

    public function artistApplicationMock()
    {
        return Mockery::mock(
            'App\Application\Artist\ArtistApplication',
            [
                app('App\Domain\Artist\ArtistRepositoryInterface'),
                app('App\Domain\Artist\ArtistFactoryInterface')
            ]
        )->makePartial();
    }

    public function musicApplicationMock()
    {
        return Mockery::mock(
            'App\Application\Music\MusicApplication',
            [
                app('App\Domain\Music\MusicRepositoryInterface'),
                app('App\Domain\Music\MusicFactoryInterface')
            ]
        )->makePartial();
    }

    public function chartRankingItemApplicationMock()
    {
        return Mockery::mock(
            'App\Application\ChartRankingItem\ChartRankingItemApplication',
            [
                app('App\Domain\ChartRankingItem\ChartRankingItemRepositoryInterface'),
                app('App\Domain\ChartRankingItem\ChartRankingItemFactoryInterface')
            ]
        )->makePartial();
    }

    public function testHandleArtistApplicationRegisterReturnFalse()
    {
        $artistApplicationMock = $this->artistApplicationMock();
        $artistApplicationMock->shouldReceive('register')->andReturn(false);
        $artistMusicResolvedListener = new ArtistMusicResolvedListener(
            $artistApplicationMock,
            app($this->musicApplicationInterfaceName),
            app($this->chartRankingItemApplicationInterfaceName)
        );
        $logged = false;
        Log::shouldReceive('error')->andReturnUsing(
            function ($message) use(&$logged) {
                if ($message === "Failed to register ArtistEntity.") {
                    $logged = true;
                }
            }
        );

        $artistMusicResolvedListener->handle(
            new ArtistMusicResolved(
                '00000000000000000000000000000000',
                'itunes',
                'Halsey',
                'Bad At Love',
                '000090a1b2c3d4e5f6a7b8c9d',
                '000090a1b2c3d4e5f6a7b8c9d'
            )
        );
        $this->assertTrue($logged);
    }

    /**
     * @expectedException App\Domain\Artist\ArtistException
     */
    public function testHandleArtistApplicationRegisterExceptionOccurred()
    {
        $artistApplicationMock = $this->artistApplicationMock();
        $artistApplicationMock->shouldReceive('register')->andReturnUsing(
            function () {
                throw new ArtistException('This is test.');
            }
        );
        $artistMusicResolvedListener = new ArtistMusicResolvedListener(
            $artistApplicationMock,
            app($this->musicApplicationInterfaceName),
            app($this->chartRankingItemApplicationInterfaceName)
        );

        $artistMusicResolvedListener->handle(
            new ArtistMusicResolved(
                '00000000000000000000000000000000',
                'itunes',
                'Halsey',
                'Bad At Love',
                '000090a1b2c3d4e5f6a7b8c9d',
                '000090a1b2c3d4e5f6a7b8c9d'
            )
        );
    }

    public function testHandleMusicApplicationRegisterReturnFalse()
    {
        $musicApplicationMock = $this->musicApplicationMock();
        $musicApplicationMock->shouldReceive('register')->andReturn(false);
        $artistMusicResolvedListener = new ArtistMusicResolvedListener(
            app($this->artistApplicationInterfaceName),
            $musicApplicationMock,
            app($this->chartRankingItemApplicationInterfaceName)
        );
        $logged = false;
        Log::shouldReceive('error')->andReturnUsing(
            function ($message) use(&$logged) {
                if ($message === "Failed to register MusicEntity.") {
                    $logged = true;
                }
            }
        );

        $artistMusicResolvedListener->handle(
            new ArtistMusicResolved(
                '00000000000000000000000000000000',
                'itunes',
                'Halsey',
                'Bad At Love',
                '000090a1b2c3d4e5f6a7b8c9d',
                '000090a1b2c3d4e5f6a7b8c9d'
            )
        );
        $this->assertTrue($logged);
    }

    /**
     * @expectedException App\Domain\Music\MusicException
     */
    public function testHandleMusicApplicationRegisterExceptionOccurred()
    {
        $musicApplicationMock = $this->musicApplicationMock();
        $musicApplicationMock->shouldReceive('register')->andReturnUsing(
            function () {
                throw new MusicException('This is test.');
            }
        );
        $artistMusicResolvedListener = new ArtistMusicResolvedListener(
            app($this->artistApplicationInterfaceName),
            $musicApplicationMock,
            app($this->chartRankingItemApplicationInterfaceName)
        );

        $artistMusicResolvedListener->handle(
            new ArtistMusicResolved(
                '00000000000000000000000000000000',
                'itunes',
                'Halsey',
                'Bad At Love',
                '000090a1b2c3d4e5f6a7b8c9d',
                '000090a1b2c3d4e5f6a7b8c9d'
            )
        );
    }

    public function testHandleArtistEntityNotFound()
    {
        $artistApplicationMock = $this->artistApplicationMock();
        $artistApplicationMock->shouldReceive('get')->andReturn(null);
        $artistMusicResolvedListener = new ArtistMusicResolvedListener(
            $artistApplicationMock,
            app($this->musicApplicationInterfaceName),
            app($this->chartRankingItemApplicationInterfaceName)
        );

        $logged = false;
        Log::shouldReceive('error')->andReturnUsing(
            function ($message) use(&$logged) {
                if ($message === "Couldn't find ArtistEntity.") {
                    $logged = true;
                }
            }
        );
        $artistMusicResolvedListener->handle(
            new ArtistMusicResolved(
                '00000000000000000000000000000000',
                'itunes',
                'Halsey',
                'Bad At Love',
                '000090a1b2c3d4e5f6a7b8c9d',
                '000090a1b2c3d4e5f6a7b8c9d'
            )
        );
        $this->assertTrue($logged);
    }

    public function testHandleMusicEntityNotFound()
    {
        $musicApplicationMock = $this->musicApplicationMock();
        $musicApplicationMock->shouldReceive('get')->andReturn(null);
        $artistMusicResolvedListener = new ArtistMusicResolvedListener(
            app($this->artistApplicationInterfaceName),
            $musicApplicationMock,
            app($this->chartRankingItemApplicationInterfaceName)
        );

        $logged = false;
        Log::shouldReceive('error')->andReturnUsing(
            function ($message) use(&$logged) {
                if ($message === "Couldn't find MusicEntity.") {
                    $logged = true;
                }
            }
        );
        $artistMusicResolvedListener->handle(
            new ArtistMusicResolved(
                '00000000000000000000000000000000',
                'itunes',
                'Halsey',
                'Bad At Love',
                '000090a1b2c3d4e5f6a7b8c9d',
                '000090a1b2c3d4e5f6a7b8c9d'
            )
        );
        $this->assertTrue($logged);
    }

    public function testHandleChartRankingItemEntityNotFound()
    {
        $chartRankingItemApplicationMock = $this->chartRankingItemApplicationMock();
        $chartRankingItemApplicationMock->shouldReceive('find')->andReturn(null);
        $artistMusicResolvedListener = new ArtistMusicResolvedListener(
            app($this->artistApplicationInterfaceName),
            app($this->musicApplicationInterfaceName),
            $chartRankingItemApplicationMock
        );

        $logged = false;
        Log::shouldReceive('error')->andReturnUsing(
            function ($message) use(&$logged) {
                if ($message === "Couldn't find ChartRankingItemEntity.") {
                    $logged = true;
                }
            }
        );
        $artistMusicResolvedListener->handle(
            new ArtistMusicResolved(
                '00000000000000000000000000000000',
                'itunes',
                'Halsey',
                'Bad At Love',
                '000090a1b2c3d4e5f6a7b8c9d',
                '000090a1b2c3d4e5f6a7b8c9d'
            )
        );
        $this->assertTrue($logged);
    }

    public function testHandleChartRankingItemApplicationModifyReturnFalse()
    {
        $chartRankingItemApplicationMock = $this->chartRankingItemApplicationMock();
        $chartRankingItemApplicationMock->shouldReceive('modify')->andReturn(false);
        $artistMusicResolvedListener = new ArtistMusicResolvedListener(
            app($this->artistApplicationInterfaceName),
            app($this->musicApplicationInterfaceName),
            $chartRankingItemApplicationMock
        );

        $logged = false;
        Log::shouldReceive('error')->andReturnUsing(
            function ($message) use(&$logged) {
                if ($message === "Failed to modify ChartRankinItem.") {
                    $logged = true;
                }
            }
        );
        $artistMusicResolvedListener->handle(
            new ArtistMusicResolved(
                '3123456789abcdef0123456789abcdef',
                'itunes',
                'Camila Cabello',
                'Havana',
                '000040a1b2c3d4e5f6a7b8c9d',
                null
            )
        );
        $this->assertTrue($logged);
    }

    public function testHandle()
    {
        $artistMusicResolvedListener = new ArtistMusicResolvedListener(
            app($this->artistApplicationInterfaceName),
            app($this->musicApplicationInterfaceName),
            app($this->chartRankingItemApplicationInterfaceName)
        );
        $entityIdValue = '4123456789abcdef0123456789abcdef';
        $artistIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $musicIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $result = $artistMusicResolvedListener->handle(
            new ArtistMusicResolved(
                $entityIdValue,
                'itunes',
                'Lil Pump',
                'Gucci Gang',
                $iTunesArtistIdValue,
                null
            )
        );
        $this->assertTrue($result);
        $chartRankingItemRepository = app('App\Domain\ChartRankingItem\ChartRankingItemRepositoryInterface');
        $chartRankingItemEntity = $chartRankingItemRepository->find(new EntityId($entityIdValue));
        $this->assertEquals($chartRankingItemEntity->id()->value(), $entityIdValue);
        $this->assertEquals($chartRankingItemEntity->artistId()->value(), $artistIdValue);
        $this->assertEquals($chartRankingItemEntity->musicId()->value(), $musicIdValue);
    }

}
