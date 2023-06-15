<?php

namespace Tests\Unit\Application\Music;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mockery;
use Event;
use DB;
use App\Infrastructure\Eloquents\Music;
use App\Infrastructure\Eloquents\ProvisionedMusic;
use App\Infrastructure\Eloquents\PromotionVideo;
use App\Infrastructure\Eloquents\PromotionVideoBrokenLink;
use App\Application\Music\MusicApplication;
use App\Application\DXO\MusicDXO;
use App\Domain\EntityId;
use App\Domain\ValueObjects\Phase;
use App\Domain\ValueObjects\ITunesArtistId;
use App\Domain\Music\MusicEntity;
use App\Domain\Music\MusicException;
use App\Domain\Music\MusicSpecification;

class MusicApplicationTest extends TestCase
{

    use DatabaseMigrations;

    private $redisDAOInterfaceName = 'App\Infrastructure\RedisDAO\RedisDAOInterface';
    private $musicApplicationInterfaceName = 'App\Application\Music\MusicApplicationInterface';
    private $musicFactoryInterfaceName = 'App\Domain\Music\MusicFactoryInterface';
    private $musicRepositoryInterfaceName = 'App\Domain\Music\MusicRepositoryInterface';
    private $musicServiceInterfaceName = 'App\Domain\Music\MusicServiceInterface';

    private function musicFactoryMock()
    {
        return Mockery::mock('App\Domain\Music\MusicFactory')->makePartial();
    }

    private function musicRepositoryMock()
    {
        return Mockery::mock(
            'App\Infrastructure\Repositories\MusicRepository',
            [
                app($this->redisDAOInterfaceName),
                app($this->musicFactoryInterfaceName)
            ]
        )->makePartial();
    }

    public function musicServiceMock()
    {
        return Mockery::mock(
            'App\Domain\Music\MusicService',
            [
                app('App\Infrastructure\Remote\RemoteInterface')
            ]
        )->makePartial();
    }

    public function musicApplicationMock()
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

    public function setUp()
    {
        parent::setUp();

        factory(Music::class, 10)->create();
        factory(ProvisionedMusic::class, 10)->create();
        factory(PromotionVideo::class, 10)->create();
    }

    public function tearDown()
    {
        $redisDAO = app($this->redisDAOInterfaceName);
        $redisDAO->clear('*');
        $redisDAO->resetIsCache();

        Mockery::close();

        Music::truncate();
        ProvisionedMusic::truncate();
        PromotionVideo::truncate();
        PromotionVideoBrokenLink::truncate();

        DB::disconnect();
    }

    public function testProvider()
    {
        $musicApplication = app($this->musicApplicationInterfaceName);
        $this->assertEquals(get_class($musicApplication), MusicApplication::class);
    }

    public function testFindEmptyParameters()
    {
        $musicApplication = app($this->musicApplicationInterfaceName);

        $phaseValue = '';
        $idValue = '000010a1b2c3d4e5f6a7b8c9d';
        $musicDXO = new MusicDXO();
        $musicDXO->find($phaseValue, $idValue);
        $musicEntity = $musicApplication->find($musicDXO);
        $this->assertNull($musicEntity);

        $phaseValue = Phase::released;
        $idValue = '';
        $musicDXO = new MusicDXO();
        $musicDXO->find($phaseValue, $idValue);
        $musicEntity = $musicApplication->find($musicDXO);
        $this->assertNull($musicEntity);
    }

    public function testFind()
    {
        $musicApplication = app($this->musicApplicationInterfaceName);

        $phaseValue = Phase::released;
        $idValue = '000050a1b2c3d4e5f6a7b8c9d';
        $musicDXO = new MusicDXO();
        $musicDXO->find($phaseValue, $idValue);
        $musicEntity = $musicApplication->find($musicDXO);
        $this->assertNull($musicEntity);

        $phaseValue = Phase::released;
        $idValue = '000010a1b2c3d4e5f6a7b8c9d';
        $musicDXO = new MusicDXO();
        $musicDXO->find($phaseValue, $idValue);
        $musicEntity = $musicApplication->find($musicDXO);
        $this->assertEquals($musicEntity->id()->value(), $idValue);

        $phaseValue = Phase::provisioned;
        $idValue = '000010a1b2c3d4e5f6a7b8c9d';
        $musicDXO = new MusicDXO();
        $musicDXO->find($phaseValue, $idValue);
        $musicEntity = $musicApplication->find($musicDXO);
        $this->assertNull($musicEntity);

        $phaseValue = Phase::provisioned;
        $idValue = '000050a1b2c3d4e5f6a7b8c9d';
        $musicDXO = new MusicDXO();
        $musicDXO->find($phaseValue, $idValue);
        $musicEntity = $musicApplication->find($musicDXO);
        $this->assertEquals($musicEntity->id()->value(), $idValue);
    }

    public function testGetEmptyParameters()
    {
        $musicApplication = app($this->musicApplicationInterfaceName);

        $phaseValue = '';
        $iTunesArtistIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $musicTitleValue = 'Shape Of You';
        $musicDXO = new MusicDXO();
        $musicDXO->get($phaseValue, $iTunesArtistIdValue, $musicTitleValue);
        $musicEntity = $musicApplication->get($musicDXO);
        $this->assertNull($musicEntity);

        $phaseValue = Phase::released;
        $iTunesArtistIdValue = '';
        $musicTitleValue = 'Shape Of You';
        $musicDXO = new MusicDXO();
        $musicDXO->get($phaseValue, $iTunesArtistIdValue, $musicTitleValue);
        $musicEntity = $musicApplication->get($musicDXO);
        $this->assertNull($musicEntity);

        $phaseValue = Phase::released;
        $iTunesArtistIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $musicTitleValue = '';
        $musicDXO = new MusicDXO();
        $musicDXO->get($phaseValue, $iTunesArtistIdValue, $musicTitleValue);
        $musicEntity = $musicApplication->get($musicDXO);
        $this->assertNull($musicEntity);
    }

    public function testGet()
    {
        $musicApplication = app($this->musicApplicationInterfaceName);

        $phaseValue = Phase::released;
        $iTunesArtistIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $musicTitleValue = 'Gucci Gang';
        $musicDXO = new MusicDXO();
        $musicDXO->get($phaseValue, $iTunesArtistIdValue, $musicTitleValue);
        $musicEntity = $musicApplication->get($musicDXO);
        $this->assertNull($musicEntity);

        $phaseValue = Phase::released;
        $iTunesArtistIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $musicTitleValue = 'Shape Of You';
        $musicDXO = new MusicDXO();
        $musicDXO->get($phaseValue, $iTunesArtistIdValue, $musicTitleValue);
        $musicEntity = $musicApplication->get($musicDXO);
        $this->assertEquals($musicEntity->iTunesArtistId()->value(), $iTunesArtistIdValue);
        $this->assertEquals($musicEntity->musicTitle()->value(), $musicTitleValue);

        $phaseValue = Phase::provisioned;
        $iTunesArtistIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $musicTitleValue = 'Shape Of You';
        $musicDXO = new MusicDXO();
        $musicDXO->get($phaseValue, $iTunesArtistIdValue, $musicTitleValue);
        $musicEntity = $musicApplication->get($musicDXO);
        $this->assertNull($musicEntity);

        $phaseValue = Phase::provisioned;
        $iTunesArtistIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $musicTitleValue = 'Gucci Gang';
        $musicDXO = new MusicDXO();
        $musicDXO->get($phaseValue, $iTunesArtistIdValue, $musicTitleValue);
        $musicEntity = $musicApplication->get($musicDXO);
        $this->assertEquals($musicEntity->iTunesArtistId()->value(), $iTunesArtistIdValue);
        $this->assertEquals($musicEntity->musicTitle()->value(), $musicTitleValue);
    }

    public function testRegisterEmptyParameters()
    {
        $musicApplication = app($this->musicApplicationInterfaceName);

        $iTunesArtistIdValue = '';
        $musicTitleValue = 'Bad At Love';
        $promotionVideoUrlValue = 'https://www.youtube.com/watch?v=xdYFuCp3m9k';
        $thumbnailUrlValue = 'https://i.ytimg.com/vi/xdYFuCp3m9k/default.jpg';
        $musicDXO = new MusicDXO();
        $musicDXO->register($iTunesArtistIdValue, $musicTitleValue, $promotionVideoUrlValue, $thumbnailUrlValue);
        $result = $musicApplication->register($musicDXO);
        $this->assertFalse($result);

        $iTunesArtistIdValue = '000090a1b2c3d4e5f6a7b8c9d';
        $musicTitleValue = '';
        $promotionVideoUrlValue = 'https://www.youtube.com/watch?v=xdYFuCp3m9k';
        $thumbnailUrlValue = 'https://i.ytimg.com/vi/xdYFuCp3m9k/default.jpg';
        $musicDXO = new MusicDXO();
        $musicDXO->register($iTunesArtistIdValue, $musicTitleValue, $promotionVideoUrlValue, $thumbnailUrlValue);
        $result = $musicApplication->register($musicDXO);
        $this->assertFalse($result);
    }

    public function testRegisterFactoryCreateEmpty()
    {
        $musicFactoryMock = $this->musicFactoryMock();
        $musicFactoryMock->shouldReceive('create')->andReturn(null);
        $musicApplication = new MusicApplication(
            app($this->musicRepositoryInterfaceName),
            $musicFactoryMock,
            app($this->musicServiceInterfaceName)
        );

        $iTunesArtistIdValue = '000090a1b2c3d4e5f6a7b8c9d';
        $musicTitleValue = 'Bad At Love';
        $promotionVideoUrlValue = 'https://www.youtube.com/watch?v=xdYFuCp3m9k';
        $thumbnailUrlValue = 'https://i.ytimg.com/vi/xdYFuCp3m9k/default.jpg';
        $musicDXO = new MusicDXO();
        $musicDXO->register($iTunesArtistIdValue, $musicTitleValue, $promotionVideoUrlValue, $thumbnailUrlValue);
        $result = $musicApplication->register($musicDXO);
        $this->assertFalse($result);
    }

    public function testRegisterRepositoryReturnFalse()
    {
        $musicRepositoryMock = $this->musicRepositoryMock();
        $musicRepositoryMock->shouldReceive('register')->andReturn(false);
        $musicApplication = new MusicApplication(
            $musicRepositoryMock,
            app($this->musicFactoryInterfaceName),
            app($this->musicServiceInterfaceName)
        );

        $iTunesArtistIdValue = '000090a1b2c3d4e5f6a7b8c9d';
        $musicTitleValue = 'Bad At Love';
        $promotionVideoUrlValue = 'https://www.youtube.com/watch?v=xdYFuCp3m9k';
        $thumbnailUrlValue = 'https://i.ytimg.com/vi/xdYFuCp3m9k/default.jpg';
        $musicDXO = new MusicDXO();
        $musicDXO->register($iTunesArtistIdValue, $musicTitleValue, $promotionVideoUrlValue, $thumbnailUrlValue);
        $result = $musicApplication->register($musicDXO);
        $this->assertFalse($result);
    }

    /**
     * @expectedException App\Domain\Music\MusicException
     */
    public function testRegisterExceptionOccurred()
    {
        $musicApplication = app($this->musicApplicationInterfaceName);

        $iTunesArtistIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $musicTitleValue = 'Gucci Gang';
        $promotionVideoUrlValue = 'https://www.youtube.com/watch?v=4LfJnj66HVQ';
        $thumbnailUrlValue = 'https://i.ytimg.com/vi/4LfJnj66HVQ/default.jpg';
        $musicDXO = new MusicDXO();
        $musicDXO->register($iTunesArtistIdValue, $musicTitleValue, $promotionVideoUrlValue, $thumbnailUrlValue);
        $musicApplication->register($musicDXO);
    }

    public function testRegister()
    {
        $musicApplication = app($this->musicApplicationInterfaceName);
        $eventPublished = false;
        Event::shouldReceive('dispatch')->andReturnUsing(
            function ($event) use(&$eventPublished)
            {
                $eventName = 'App\Events\MusicRegistered';
                if ($event instanceOf $eventName) {
                    $eventPublished = true;
                }
            }
        );

        $iTunesArtistIdValue = '000090a1b2c3d4e5f6a7b8c9d';
        $musicTitleValue = 'Bad At Love';
        $promotionVideoUrlValue = 'https://www.youtube.com/watch?v=xdYFuCp3m9k';
        $thumbnailUrlValue = 'https://i.ytimg.com/vi/xdYFuCp3m9k/default.jpg';
        $musicDXO = new MusicDXO();
        $musicDXO->register($iTunesArtistIdValue, $musicTitleValue, $promotionVideoUrlValue, $thumbnailUrlValue);
        $result = $musicApplication->register($musicDXO);
        $this->assertTrue($result);
        $this->assertTrue($eventPublished);
        $musicDXO = new MusicDXO();
        $musicDXO->get(Phase::provisioned, $iTunesArtistIdValue, $musicTitleValue);
        $musicEntity = $musicApplication->get($musicDXO);
        $this->assertEquals($musicEntity->iTunesArtistId()->value(), $iTunesArtistIdValue);
        $this->assertEquals($musicEntity->musicTitle()->value(), $musicTitleValue);
        $this->assertEquals($musicEntity->promotionVideoUrl()->value(), $promotionVideoUrlValue);
        $this->assertEquals($musicEntity->thumbnailUrl()->value(), $thumbnailUrlValue);
    }

    public function testRegisterEmptyPromotionVideo()
    {
        $musicApplication = app($this->musicApplicationInterfaceName);
        $eventPublished = false;
        Event::shouldReceive('dispatch')->andReturnUsing(
            function ($event) use(&$eventPublished)
            {
                $eventName = 'App\Events\MusicRegistered';
                if ($event instanceOf $eventName) {
                    $eventPublished = true;
                }
            }
        );

        $iTunesArtistIdValue = '000090a1b2c3d4e5f6a7b8c9d';
        $musicTitleValue = 'Bad At Love';
        $promotionVideoUrlValue = null;
        $thumbnailUrlValue = 'https://i.ytimg.com/vi/xdYFuCp3m9k/default.jpg';
        $musicDXO = new MusicDXO();
        $musicDXO->register($iTunesArtistIdValue, $musicTitleValue, $promotionVideoUrlValue, $thumbnailUrlValue);
        $result = $musicApplication->register($musicDXO);
        $this->assertTrue($result);
        $this->assertTrue($eventPublished);
        $musicDXO = new MusicDXO();
        $musicDXO->get(Phase::provisioned, $iTunesArtistIdValue, $musicTitleValue);
        $musicEntity = $musicApplication->get($musicDXO);
        $this->assertEquals($musicEntity->iTunesArtistId()->value(), $iTunesArtistIdValue);
        $this->assertEquals($musicEntity->musicTitle()->value(), $musicTitleValue);
        $this->assertNull($musicEntity->promotionVideoUrl());
        $this->assertNull($musicEntity->thumbnailUrl());
    }

    public function testModifyEmptyParameters()
    {
        $musicApplication = app($this->musicApplicationInterfaceName);

        $phaseValue = '';
        $entityIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $musicTitleValue = 'Shape Of You';
        $promotionVideoUrlValue = 'https://www.youtube.com/watch?v=JGwWNGJdvx8';
        $thumbnailUrlValue = 'https://i.ytimg.com/vi/JGwWNGJdvx8/default.jpg';
        $musicDXO = new MusicDXO();
        $musicDXO->modify($phaseValue, $entityIdValue, $iTunesArtistIdValue, $musicTitleValue, $promotionVideoUrlValue, $thumbnailUrlValue);
        $result = $musicApplication->modify($musicDXO);
        $this->assertFalse($result);

        $phaseValue = Phase::released;
        $entityIdValue = '';
        $iTunesArtistIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $musicTitleValue = 'Shape Of You';
        $promotionVideoUrlValue = 'https://www.youtube.com/watch?v=JGwWNGJdvx8';
        $thumbnailUrlValue = 'https://i.ytimg.com/vi/JGwWNGJdvx8/default.jpg';
        $musicDXO = new MusicDXO();
        $musicDXO->modify($phaseValue, $entityIdValue, $iTunesArtistIdValue, $musicTitleValue, $promotionVideoUrlValue, $thumbnailUrlValue);
        $result = $musicApplication->modify($musicDXO);
        $this->assertFalse($result);

        $phaseValue = Phase::released;
        $entityIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistIdValue = '';
        $musicTitleValue = 'Shape Of You';
        $promotionVideoUrlValue = 'https://www.youtube.com/watch?v=JGwWNGJdvx8';
        $thumbnailUrlValue = 'https://i.ytimg.com/vi/JGwWNGJdvx8/default.jpg';
        $musicDXO = new MusicDXO();
        $musicDXO->modify($phaseValue, $entityIdValue, $iTunesArtistIdValue, $musicTitleValue, $promotionVideoUrlValue, $thumbnailUrlValue);
        $result = $musicApplication->modify($musicDXO);
        $this->assertFalse($result);

        $phaseValue = Phase::released;
        $entityIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $musicTitleValue = '';
        $promotionVideoUrlValue = 'https://www.youtube.com/watch?v=JGwWNGJdvx8';
        $thumbnailUrlValue = 'https://i.ytimg.com/vi/JGwWNGJdvx8/default.jpg';
        $musicDXO = new MusicDXO();
        $musicDXO->modify($phaseValue, $entityIdValue, $iTunesArtistIdValue, $musicTitleValue, $promotionVideoUrlValue, $thumbnailUrlValue);
        $result = $musicApplication->modify($musicDXO);
        $this->assertFalse($result);
    }

    public function testModifyEntityNotFound()
    {
        $musicRepositoryMock = $this->musicRepositoryMock();
        $musicRepositoryMock->shouldReceive('findProvision')->andReturn(null);
        $musicRepositoryMock->shouldReceive('findRelease')->andReturn(null);
        $musicApplication = new MusicApplication(
            $musicRepositoryMock,
            app($this->musicFactoryInterfaceName),
            app($this->musicServiceInterfaceName)
        );

        $phaseValue = Phase::released;
        $entityIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $musicTitleValue = 'Shape Of You';
        $promotionVideoUrlValue = 'https://www.youtube.com/watch?v=JGwWNGJdvx8';
        $thumbnailUrlValue = 'https://i.ytimg.com/vi/JGwWNGJdvx8/default.jpg';
        $musicDXO = new MusicDXO();
        $musicDXO->modify($phaseValue, $entityIdValue, $iTunesArtistIdValue, $musicTitleValue, $promotionVideoUrlValue, $thumbnailUrlValue);
        $result = $musicApplication->modify($musicDXO);
        $this->assertFalse($result);

        $phaseValue = Phase::provisioned;
        $entityIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $musicTitleValue = 'Gucci Gang';
        $promotionVideoUrlValue = 'https://www.youtube.com/watch?v=4LfJnj66HVQ';
        $thumbnailUrlValue = 'https://i.ytimg.com/vi/4LfJnj66HVQ/default.jpg';
        $musicDXO = new MusicDXO();
        $musicDXO->modify($phaseValue, $entityIdValue, $iTunesArtistIdValue, $musicTitleValue, $promotionVideoUrlValue, $thumbnailUrlValue);
        $result = $musicApplication->modify($musicDXO);
        $this->assertFalse($result);
    }

    public function testModifyRepositoryReturnFalse()
    {
        $musicRepositoryMock = $this->musicRepositoryMock();
        $musicRepositoryMock->shouldReceive('modifyProvision')->andReturn(false);
        $musicRepositoryMock->shouldReceive('modifyRelease')->andReturn(false);
        $musicApplication = new MusicApplication(
            $musicRepositoryMock,
            app($this->musicFactoryInterfaceName),
            app($this->musicServiceInterfaceName)
        );

        $phaseValue = Phase::released;
        $entityIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $musicTitleValue = 'Shape Of You';
        $promotionVideoUrlValue = 'https://www.youtube.com/watch?v=JGwWNGJdvx8';
        $thumbnailUrlValue = 'https://i.ytimg.com/vi/JGwWNGJdvx8/default.jpg';
        $musicDXO = new MusicDXO();
        $musicDXO->modify($phaseValue, $entityIdValue, $iTunesArtistIdValue, $musicTitleValue, $promotionVideoUrlValue, $thumbnailUrlValue);
        $result = $musicApplication->modify($musicDXO);
        $this->assertFalse($result);

        $phaseValue = Phase::provisioned;
        $entityIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $musicTitleValue = 'Gucci Gang';
        $promotionVideoUrlValue = 'https://www.youtube.com/watch?v=4LfJnj66HVQ';
        $thumbnailUrlValue = 'https://i.ytimg.com/vi/4LfJnj66HVQ/default.jpg';
        $musicDXO = new MusicDXO();
        $musicDXO->modify($phaseValue, $entityIdValue, $iTunesArtistIdValue, $musicTitleValue, $promotionVideoUrlValue, $thumbnailUrlValue);
        $result = $musicApplication->modify($musicDXO);
        $this->assertFalse($result);
    }

    /**
     * @expectedException App\Domain\Music\MusicException
     */
    public function testModifyReleaseExceptionOccurred()
    {
        $musicApplication = app($this->musicApplicationInterfaceName);

        $phaseValue = Phase::released;
        $entityIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistIdValue = '000020a1b2c3d4e5f6a7b8c9d';
        $musicTitleValue = 'Humble.';
        $promotionVideoUrlValue = 'https://www.youtube.com/watch?v=JGwWNGJdvx8';
        $thumbnailUrlValue = 'https://i.ytimg.com/vi/JGwWNGJdvx8/default.jpg';
        $musicDXO = new MusicDXO();
        $musicDXO->modify($phaseValue, $entityIdValue, $iTunesArtistIdValue, $musicTitleValue, $promotionVideoUrlValue, $thumbnailUrlValue);
        $musicApplication->modify($musicDXO);
    }

    /**
     * @expectedException App\Domain\Music\MusicException
     */
    public function testModifyProvisionedExceptionOccurred()
    {
        $musicApplication = app($this->musicApplicationInterfaceName);

        $phaseValue = Phase::provisioned;
        $entityIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistIdValue = '000060a1b2c3d4e5f6a7b8c9d';
        $musicTitleValue = 'Thunder';
        $promotionVideoUrlValue = 'https://www.youtube.com/watch?v=4LfJnj66HVQ';
        $thumbnailUrlValue = 'https://i.ytimg.com/vi/4LfJnj66HVQ/default.jpg';
        $musicDXO = new MusicDXO();
        $musicDXO->modify($phaseValue, $entityIdValue, $iTunesArtistIdValue, $musicTitleValue, $promotionVideoUrlValue, $thumbnailUrlValue);
        $musicApplication->modify($musicDXO);
    }

    public function testModify()
    {
        $eventPublished = false;
        Event::shouldReceive('dispatch')->andReturnUsing(
            function ($event) use (&$eventPublished) {
                $eventName = 'App\Events\MusicModified';
                if ($event instanceof $eventName) {
                    $eventPublished = true;
                }
            }
        );
        $musicApplication = app($this->musicApplicationInterfaceName);

        $eventPublished = false;
        $phaseValue = Phase::released;
        $entityIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $musicTitleValue = 'Shape Of You+';
        $promotionVideoUrlValue = 'https://www.youtube.com/watch?v=JGwWNGJdvx8';
        $thumbnailUrlValue = 'https://i.ytimg.com/vi/JGwWNGJdvx8/default.jpg';
        $musicDXO = new MusicDXO();
        $musicDXO->modify($phaseValue, $entityIdValue, $iTunesArtistIdValue, $musicTitleValue, $promotionVideoUrlValue, $thumbnailUrlValue);
        $result = $musicApplication->modify($musicDXO);
        $this->assertTrue($result);
        $this->assertTrue($eventPublished);
        $musicDXO = new MusicDXO();
        $musicDXO->find($phaseValue, $entityIdValue);
        $modifiedEntity = $musicApplication->find($musicDXO);
        $this->assertEquals($modifiedEntity->iTunesArtistId()->value(), $iTunesArtistIdValue);
        $this->assertEquals($modifiedEntity->musicTitle()->value(), $musicTitleValue);
        $this->assertEquals($modifiedEntity->promotionVideoUrl()->value(), $promotionVideoUrlValue);
        $this->assertEquals($modifiedEntity->thumbnailUrl()->value(), $thumbnailUrlValue);

        $eventPublished = false;
        $phaseValue = Phase::provisioned;
        $entityIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $musicTitleValue = 'Gucci Gang+';
        $promotionVideoUrlValue = 'https://www.youtube.com/watch?v=4LfJnj66HVQ';
        $thumbnailUrlValue = 'https://i.ytimg.com/vi/4LfJnj66HVQ/default.jpg';
        $musicDXO = new MusicDXO();
        $musicDXO->modify($phaseValue, $entityIdValue, $iTunesArtistIdValue, $musicTitleValue, $promotionVideoUrlValue, $thumbnailUrlValue);
        $result = $musicApplication->modify($musicDXO);
        $this->assertTrue($result);
        $this->assertTrue($eventPublished);
        $musicDXO = new MusicDXO();
        $musicDXO->find($phaseValue, $entityIdValue);
        $modifiedEntity = $musicApplication->find($musicDXO);
        $this->assertEquals($modifiedEntity->iTunesArtistId()->value(), $iTunesArtistIdValue);
        $this->assertEquals($modifiedEntity->musicTitle()->value(), $musicTitleValue);
        $this->assertEquals($modifiedEntity->promotionVideoUrl()->value(), $promotionVideoUrlValue);
        $this->assertEquals($modifiedEntity->thumbnailUrl()->value(), $thumbnailUrlValue);
    }

    public function testModifyEmptyPromotionVideo()
    {
        $eventPublished = false;
        Event::shouldReceive('dispatch')->andReturnUsing(
            function ($event) use (&$eventPublished) {
                $eventName = 'App\Events\MusicModified';
                if ($event instanceof $eventName) {
                    $eventPublished = true;
                }
            }
        );
        $musicApplication = app($this->musicApplicationInterfaceName);

        $eventPublished = false;
        $phaseValue = Phase::released;
        $entityIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $musicTitleValue = 'Shape Of You+';
        $promotionVideoUrlValue = null;
        $thumbnailUrlValue = 'https://i.ytimg.com/vi/JGwWNGJdvx8/default.jpg';
        $musicDXO = new MusicDXO();
        $musicDXO->modify($phaseValue, $entityIdValue, $iTunesArtistIdValue, $musicTitleValue, $promotionVideoUrlValue, $thumbnailUrlValue);
        $result = $musicApplication->modify($musicDXO);
        $this->assertTrue($result);
        $this->assertTrue($eventPublished);
        $musicDXO = new MusicDXO();
        $musicDXO->find($phaseValue, $entityIdValue);
        $modifiedEntity = $musicApplication->find($musicDXO);
        $this->assertEquals($modifiedEntity->iTunesArtistId()->value(), $iTunesArtistIdValue);
        $this->assertEquals($modifiedEntity->musicTitle()->value(), $musicTitleValue);
        $this->assertNull($modifiedEntity->promotionVideoUrl());
        $this->assertNull($modifiedEntity->thumbnailUrl());
    }

    public function testDeleteEmptyParameters()
    {
        $musicApplication = app($this->musicApplicationInterfaceName);

        $entityIdValue = '';
        $musicDXO = new MusicDXO();
        $musicDXO->delete($entityIdValue);
        $result = $musicApplication->delete($musicDXO);
        $this->assertFalse($result);
    }

    public function testDeleteRepositoryReturnFalse()
    {
        $musicRepositoryMock = $this->musicRepositoryMock();
        $musicRepositoryMock->shouldReceive('delete')->andReturn(false);
        $musicApplication = new MusicApplication(
            $musicRepositoryMock,
            app($this->musicFactoryInterfaceName),
            app($this->musicServiceInterfaceName)
        );

        $entityIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $musicDXO = new MusicDXO();
        $musicDXO->delete($entityIdValue);
        $result = $musicApplication->delete($musicDXO);
        $this->assertFalse($result);
    }

    /**
     * @expectedException App\Domain\Music\MusicException
     */
    public function testDeleteExceptionOccurred()
    {
        $musicApplication = app($this->musicApplicationInterfaceName);

        $entityIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $musicDXO = new MusicDXO();
        $musicDXO->delete($entityIdValue);
        $musicApplication->delete($musicDXO);
    }

    public function testDelete()
    {
        $musicApplication = app($this->musicApplicationInterfaceName);
        $eventPublished = false;
        Event::shouldReceive('dispatch')->andReturnUsing(
            function ($event) use (&$eventPublished) {
                $eventName = 'App\Events\MusicDeleted';
                if ($event instanceof $eventName) {
                    $eventPublished = true;
                }

            }
        );

        $eventPublished = false;
        $entityIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $musicDXO = new MusicDXO();
        $musicDXO->delete($entityIdValue);
        $result = $musicApplication->delete($musicDXO);
        $this->assertTrue($result);
        $this->assertTrue($eventPublished);
    }

    public function testReleaseEmptyParameters()
    {
        $musicApplication = app($this->musicApplicationInterfaceName);

        $entityIdValue = '';
        $musicDXO = new MusicDXO();
        $musicDXO->release($entityIdValue);
        $result = $musicApplication->release($musicDXO);
        $this->assertFalse($result);
    }

    public function testReleaseRepositoryReturnFalse()
    {
        $musicRepositoryMock = $this->musicRepositoryMock();
        $musicRepositoryMock->shouldReceive('release')->andReturn(false);
        $musicApplication = new MusicApplication(
            $musicRepositoryMock,
            app($this->musicFactoryInterfaceName),
            app($this->musicServiceInterfaceName)
        );

        $entityIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $musicDXO = new MusicDXO();
        $musicDXO->release($entityIdValue);
        $result = $musicApplication->release($musicDXO);
        $this->assertFalse($result);
    }

    /**
     * @expectedException App\Domain\Music\MusicException
     */
    public function testReleaseExceptionOccurred()
    {
        $musicApplication = app($this->musicApplicationInterfaceName);

        $entityIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $musicDXO = new MusicDXO();
        $musicDXO->release($entityIdValue);
        $musicApplication->release($musicDXO);
    }

    public function testRelease()
    {
        $musicApplication = app($this->musicApplicationInterfaceName);

        $entityIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $musicDXO = new MusicDXO();
        $musicDXO->release($entityIdValue);
        $result = $musicApplication->release($musicDXO);
        $this->assertTrue($result);
        $musicDXO = new MusicDXO();
        $musicDXO->find(Phase::provisioned, $entityIdValue);
        $provisionedMusicEntity = $musicApplication->find($musicDXO);
        $this->assertNull($provisionedMusicEntity);
        $musicDXO = new MusicDXO();
        $musicDXO->find(Phase::released, $entityIdValue);
        $releasedMusicEntity = $musicApplication->find($musicDXO);
        $this->assertEquals($releasedMusicEntity->id()->value(), $entityIdValue);
    }

    public function testRollbackEmptyParameters()
    {
        $musicApplication = app($this->musicApplicationInterfaceName);

        $entityIdValue = '';
        $musicDXO = new MusicDXO();
        $musicDXO->rollback($entityIdValue);
        $result = $musicApplication->rollback($musicDXO);
        $this->assertFalse($result);
    }

    public function testRollbackRepositoryReturnFalse()
    {
        $musicRepositoryMock = $this->musicRepositoryMock();
        $musicRepositoryMock->shouldReceive('rollback')->andReturn(false);
        $musicApplication = new MusicApplication(
            $musicRepositoryMock,
            app($this->musicFactoryInterfaceName),
            app($this->musicServiceInterfaceName)
        );

        $entityIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $musicDXO = new MusicDXO();
        $musicDXO->rollback($entityIdValue);
        $result = $musicApplication->rollback($musicDXO);
        $this->assertFalse($result);
    }

    /**
     * @expectedException App\Domain\Music\MusicException
     */
    public function testRollbackExceptionOccurred()
    {
        $musicApplication = app($this->musicApplicationInterfaceName);

        $entityIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $musicDXO = new MusicDXO();
        $musicDXO->rollback($entityIdValue);
        $musicApplication->rollback($musicDXO);
    }

    public function testRollbackRollbackedEntityNotFound()
    {
        $musicRepositoryMock = $this->musicRepositoryMock();
        $musicRepositoryMock->shouldReceive('findProvision')->andReturn(null);
        $musicApplication = new MusicApplication(
            $musicRepositoryMock,
            app($this->musicFactoryInterfaceName),
            app($this->musicServiceInterfaceName)
        );

        $entityIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $musicDXO = new MusicDXO();
        $musicDXO->rollback($entityIdValue);
        $result = $musicApplication->rollback($musicDXO);
        $this->assertFalse($result);
    }

    public function testRollback()
    {
        $eventPublished = false;
        Event::shouldReceive('dispatch')->andReturnUsing(
            function ($event) use (&$eventPublished) {
                $eventName = 'App\Events\MusicRollbacked';
                if ($event instanceof $eventName) {
                    $eventPublished = true;
                }
            }
        );
        $musicApplication = app($this->musicApplicationInterfaceName);

        $entityIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $musicDXO = new MusicDXO();
        $musicDXO->rollback($entityIdValue);
        $result = $musicApplication->rollback($musicDXO);
        $this->assertTrue($result);
        $this->assertTrue($eventPublished);
        $musicDXO = new MusicDXO();
        $musicDXO->find(Phase::released, $entityIdValue);
        $rollbacked = $musicApplication->find($musicDXO);
        $this->assertNull($rollbacked);
        $musicDXO = new MusicDXO();
        $musicDXO->find(Phase::provisioned, $entityIdValue);
        $rollbacked = $musicApplication->find($musicDXO);
        $this->assertEquals($rollbacked->id()->value(), $entityIdValue);
    }

    public function testRefreshCachedEntityEmptyParameters()
    {
        $musicApplication = app($this->musicApplicationInterfaceName);

        $idValue = '';
        $musicDXO = new MusicDXO();
        $musicDXO->refreshCachedEntity($idValue);
        $result = $musicApplication->refreshCachedEntity($musicDXO);
        $this->assertFalse($result);
    }

    public function testRefreshCachedEntity()
    {
        $musicApplication = app($this->musicApplicationInterfaceName);
        $musicRepository = app($this->musicRepositoryInterfaceName);
        $redisDAO = app($this->redisDAOInterfaceName);

        $idValue = '000010a1b2c3d4e5f6a7b8c9d';
        $musicDXO = new MusicDXO();
        $musicDXO->refreshCachedEntity($idValue);
        $entityId = $musicDXO->getEntityId();
        $cacheKey = $musicRepository->cacheKeyById($entityId, MusicEntity::class);
        $redisDAO->set($cacheKey, '1');

        $result = $musicApplication->refreshCachedEntity($musicDXO);
        $this->assertTrue($result);
        $cache = $redisDAO->get($cacheKey);
        $musicEntity = unserialize($cache);
        $this->assertEquals($musicEntity->id()->value(), $idValue);
    }

    public function testAssignPromotionVideoEmptyParameters()
    {
        $musicApplication = app($this->musicApplicationInterfaceName);

        $entityIdValue = '';
        $artistNameValue = 'Ed Sheeran';
        $musicTitleValue = 'Shape Of You';
        $musicDXO = new MusicDXO();
        $musicDXO->assignPromotionVideo($entityIdValue, $artistNameValue, $musicTitleValue);
        $result = $musicApplication->assignPromotionVideo($musicDXO);
        $this->assertFalse($result);

        $entityIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $artistNameValue = '';
        $musicTitleValue = 'Shape Of You';
        $musicDXO = new MusicDXO();
        $musicDXO->assignPromotionVideo($entityIdValue, $artistNameValue, $musicTitleValue);
        $result = $musicApplication->assignPromotionVideo($musicDXO);
        $this->assertFalse($result);

        $entityIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $artistNameValue = 'Ed Sheeran';
        $musicTitleValue = '';
        $musicDXO = new MusicDXO();
        $musicDXO->assignPromotionVideo($entityIdValue, $artistNameValue, $musicTitleValue);
        $result = $musicApplication->assignPromotionVideo($musicDXO);
        $this->assertFalse($result);
    }

    public function testAssignPromotionVideoEntityNotFound()
    {
        $musicRepositoryMock = $this->musicRepositoryMock();
        $musicRepositoryMock->shouldReceive('findProvision')->andReturn(null);
        $musicRepositoryMock->shouldReceive('findRelease')->andReturn(null);
        $musicApplication = new MusicApplication(
            $musicRepositoryMock,
            app($this->musicFactoryInterfaceName),
            app($this->musicServiceInterfaceName)
        );

        $entityIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $artistNameValue = 'Ed Sheeran';
        $musicTitleValue = 'Shape Of You';
        $musicDXO = new MusicDXO();
        $musicDXO->assignPromotionVideo($entityIdValue, $artistNameValue, $musicTitleValue);
        $result = $musicApplication->assignPromotionVideo($musicDXO);
        $this->assertFalse($result);
    }

    public function testAssignPromotionVideoSearviceReturnEmpty()
    {
        $musicServiceMock = $this->musicServiceMock();
        $musicServiceMock->shouldReceive('searchPromotionVideo')->andReturn(null);
        $musicApplication = new MusicApplication(
            app($this->musicRepositoryInterfaceName),
            app($this->musicFactoryInterfaceName),
            $musicServiceMock
        );

        $entityIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $artistNameValue = 'Ed Sheeran';
        $musicTitleValue = 'Shape Of You';
        $musicDXO = new MusicDXO();
        $musicDXO->assignPromotionVideo($entityIdValue, $artistNameValue, $musicTitleValue);
        $result = $musicApplication->assignPromotionVideo($musicDXO);
        $this->assertTrue($result);
    }

    public function testAssignPromotionVideo()
    {
        Event::shouldReceive('dispatch')->andReturnUsing(function ($event) {});
        $musicApplication = app($this->musicApplicationInterfaceName);

        $entityIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $artistNameValue = 'Ed Sheeran';
        $musicTitleValue = 'Shape Of You';
        $musicDXO = new MusicDXO();
        $musicDXO->assignPromotionVideo($entityIdValue, $artistNameValue, $musicTitleValue);
        $result = $musicApplication->assignPromotionVideo($musicDXO);
        $this->assertTrue($result);
        $musicDXO = new MusicDXO();
        $musicDXO->find(Phase::released, $entityIdValue);
        $musicEntity = $musicApplication->find($musicDXO);
        $this->assertEquals($musicEntity->id()->value(), $entityIdValue);
        $this->assertNotNull($musicEntity->promotionVideoUrl());
        $this->assertNotNull($musicEntity->thumbnailUrl());
    }

    public function testCheckPromotionVideoConditionsMusicDXO()
    {
        $today = new \DatetimeImmutable(date('Y-m-d'));
        $idLikeValue = '0';

        $musicDXO = new MusicDXO();
        $checkPromotionVideoConditions = $musicDXO->getCheckPromotionVideoConditions();
        $this->assertEmpty($checkPromotionVideoConditions);

        $musicDXO = new MusicDXO();
        $musicDXO->checkPromotionVideoAppendCreatedAtGTE($today->format('Y-m-d'));
        $checkPromotionVideoConditions = $musicDXO->getCheckPromotionVideoConditions();
        $verify = [
            [
                "scope" =>  "createdAtGTE",
                "param" =>  $today->format('Y-m-d')
            ]
        ];
        $this->assertEquals($checkPromotionVideoConditions->getConditions(), $verify);

        $musicDXO = new MusicDXO();
        $musicDXO->checkPromotionVideoAppendCreatedAtLT($today->format('Y-m-d'));
        $musicDXO->checkPromotionVideoAppendMusicIdLike($idLikeValue);
        $checkPromotionVideoConditions = $musicDXO->getCheckPromotionVideoConditions();
        $verify = [
            [
                "scope" =>  "createdAtLT",
                "param" =>  $today->format('Y-m-d')
            ],
            [
                "scope" =>  "musicIdLike",
                "param" =>  $idLikeValue
            ]
        ];
        $this->assertEquals($checkPromotionVideoConditions->getConditions(), $verify);
    }

    public function testCheckPromotionVideoConditionsDoesNotExist()
    {
        $musicApplication = app($this->musicApplicationInterfaceName);

        $musicDXO = new MusicDXO();
        $result = $musicApplication->checkPromotionVideo($musicDXO);
        $this->assertTrue($result);
    }

    public function testCheckPromotionVideoMusicEntitiesEmpty()
    {
        $musicRepositoryMock = $this->musicRepositoryMock();
        $musicRepositoryMock->shouldReceive('checkPromotionVideoList')->andReturn([]);
        $musicApplication = new MusicApplication(
            $musicRepositoryMock,
            app($this->musicFactoryInterfaceName),
            app($this->musicServiceInterfaceName)
        );

        $musicDXO = new MusicDXO();
        $musicDXO->checkPromotionVideoAppendCreatedAtGTE(date('Y-m-d'));
        $result = $musicApplication->checkPromotionVideo($musicDXO);
        $this->assertTrue($result);
    }

    public function testCheckPromotionVideoAllPromotionVideoIsOK()
    {
        $musicServiceMock = $this->musicServiceMock();
        $musicServiceMock->shouldReceive('checkPromotionVideo')->andReturn(true);
        $musicApplication = new MusicApplication(
            app($this->musicRepositoryInterfaceName),
            app($this->musicFactoryInterfaceName),
            $musicServiceMock
        );

        $today = new \DatetimeImmutable(date('Y-m-d'));
        $oneYearAgo = $today->sub(new \DateInterval('P1Y'));
        $oneYearAndOneDayAgo = $oneYearAgo->sub(new \DateInterval('P1D'));

        Music::query()->update(['created_at' => $oneYearAndOneDayAgo->format('Y-m-d')]);
        ProvisionedMusic::query()->update(['created_at' => $oneYearAndOneDayAgo->format('Y-m-d')]);
        $music = Music::find('000010a1b2c3d4e5f6a7b8c9d');
        $music->created_at = $oneYearAgo->format('Y-m-d');
        $music->save();
        $provisionedMusic = ProvisionedMusic::find('000050a1b2c3d4e5f6a7b8c9d');
        $provisionedMusic->created_at = $oneYearAgo->format('Y-m-d');
        $provisionedMusic->save();

        $musicDXO = new MusicDXO();
        $musicDXO->checkPromotionVideoAppendCreatedAtGTE($oneYearAgo->format('Y-m-d'));
        $result = $musicApplication->checkPromotionVideo($musicDXO);
        $this->assertTrue($result);
    }

    public function testCheckPromotionVideoPhaseEmpty()
    {
        $musicRepositoryMock = $this->musicRepositoryMock();
        $musicRepositoryMock->shouldReceive('getPhase')->andReturn(null);
        $musicServiceMock = $this->musicServiceMock();
        $musicServiceMock->shouldReceive('checkPromotionVideo')->andReturn(false);

        $musicApplication = new MusicApplication(
            $musicRepositoryMock,
            app($this->musicFactoryInterfaceName),
            $musicServiceMock
        );

        $today = new \DatetimeImmutable(date('Y-m-d'));
        $oneYearAgo = $today->sub(new \DateInterval('P1Y'));
        $oneYearAndOneDayAgo = $oneYearAgo->sub(new \DateInterval('P1D'));

        Music::query()->update(['created_at' => $oneYearAndOneDayAgo->format('Y-m-d')]);
        ProvisionedMusic::query()->update(['created_at' => $oneYearAndOneDayAgo->format('Y-m-d')]);
        $music = Music::find('000010a1b2c3d4e5f6a7b8c9d');
        $music->created_at = $oneYearAgo->format('Y-m-d');
        $music->save();
        $provisionedMusic = ProvisionedMusic::find('000050a1b2c3d4e5f6a7b8c9d');
        $provisionedMusic->created_at = $oneYearAgo->format('Y-m-d');
        $provisionedMusic->save();

        $musicDXO = new MusicDXO();
        $musicDXO->checkPromotionVideoAppendCreatedAtGTE($oneYearAgo->format('Y-m-d'));
        $result = $musicApplication->checkPromotionVideo($musicDXO);
        $this->assertTrue($result);
    }

    public function testCheckPromotionVideoModifyReleaseReturnFalse()
    {
        $musicRepositoryMock = $this->musicRepositoryMock();
        $musicRepositoryMock->shouldReceive('modifyRelease')->andReturn(false);
        $musicServiceMock = $this->musicServiceMock();
        $musicServiceMock->shouldReceive('checkPromotionVideo')->andReturn(false);

        $musicApplication = new MusicApplication(
            $musicRepositoryMock,
            app($this->musicFactoryInterfaceName),
            $musicServiceMock
        );

        $today = new \DatetimeImmutable(date('Y-m-d'));
        $oneYearAgo = $today->sub(new \DateInterval('P1Y'));
        $oneYearAndOneDayAgo = $oneYearAgo->sub(new \DateInterval('P1D'));

        Music::query()->update(['created_at' => $oneYearAndOneDayAgo->format('Y-m-d')]);
        ProvisionedMusic::query()->update(['created_at' => $oneYearAndOneDayAgo->format('Y-m-d')]);
        $music = Music::find('000010a1b2c3d4e5f6a7b8c9d');
        $music->created_at = $oneYearAgo->format('Y-m-d');
        $music->save();
        $provisionedMusic = ProvisionedMusic::find('000050a1b2c3d4e5f6a7b8c9d');
        $provisionedMusic->created_at = $oneYearAgo->format('Y-m-d');
        $provisionedMusic->save();

        $musicDXO = new MusicDXO();
        $musicDXO->checkPromotionVideoAppendCreatedAtGTE($oneYearAgo->format('Y-m-d'));
        $result = $musicApplication->checkPromotionVideo($musicDXO);
        $this->assertTrue($result);

        $promotionVideo = PromotionVideo::musicId('000010a1b2c3d4e5f6a7b8c9d')->first();
        $this->assertEquals($promotionVideo->music_id, '000010a1b2c3d4e5f6a7b8c9d');
    }

    public function testCheckPromotionVideoModifyProvisionReturnFalse()
    {
        $musicRepositoryMock = $this->musicRepositoryMock();
        $musicRepositoryMock->shouldReceive('modifyProvision')->andReturn(false);
        $musicServiceMock = $this->musicServiceMock();
        $musicServiceMock->shouldReceive('checkPromotionVideo')->andReturn(false);

        $musicApplication = new MusicApplication(
            $musicRepositoryMock,
            app($this->musicFactoryInterfaceName),
            $musicServiceMock
        );

        $today = new \DatetimeImmutable(date('Y-m-d'));
        $oneYearAgo = $today->sub(new \DateInterval('P1Y'));
        $oneYearAndOneDayAgo = $oneYearAgo->sub(new \DateInterval('P1D'));

        Music::query()->update(['created_at' => $oneYearAndOneDayAgo->format('Y-m-d')]);
        ProvisionedMusic::query()->update(['created_at' => $oneYearAndOneDayAgo->format('Y-m-d')]);
        $music = Music::find('000010a1b2c3d4e5f6a7b8c9d');
        $music->created_at = $oneYearAgo->format('Y-m-d');
        $music->save();
        $provisionedMusic = ProvisionedMusic::find('000050a1b2c3d4e5f6a7b8c9d');
        $provisionedMusic->created_at = $oneYearAgo->format('Y-m-d');
        $provisionedMusic->save();

        $musicDXO = new MusicDXO();
        $musicDXO->checkPromotionVideoAppendCreatedAtGTE($oneYearAgo->format('Y-m-d'));
        $result = $musicApplication->checkPromotionVideo($musicDXO);
        $this->assertTrue($result);

        $promotionVideo = PromotionVideo::musicId('000050a1b2c3d4e5f6a7b8c9d')->first();
        $this->assertEquals($promotionVideo->music_id, '000050a1b2c3d4e5f6a7b8c9d');
    }

    public function testCheckPromotionVideoRegisterPromotionVideoBrokenLinkReturnFalse()
    {
        $musicRepositoryMock = $this->musicRepositoryMock();
        $musicRepositoryMock->shouldReceive('registerPromotionVideoBrokenLink')->andReturn(false);
        $musicServiceMock = $this->musicServiceMock();
        $musicServiceMock->shouldReceive('checkPromotionVideo')->andReturn(false);

        $musicApplication = new MusicApplication(
            $musicRepositoryMock,
            app($this->musicFactoryInterfaceName),
            $musicServiceMock
        );

        $today = new \DatetimeImmutable(date('Y-m-d'));
        $oneYearAgo = $today->sub(new \DateInterval('P1Y'));
        $oneYearAndOneDayAgo = $oneYearAgo->sub(new \DateInterval('P1D'));

        Music::query()->update(['created_at' => $oneYearAndOneDayAgo->format('Y-m-d')]);
        ProvisionedMusic::query()->update(['created_at' => $oneYearAndOneDayAgo->format('Y-m-d')]);
        $music = Music::find('000010a1b2c3d4e5f6a7b8c9d');
        $music->created_at = $oneYearAgo->format('Y-m-d');
        $music->save();
        $provisionedMusic = ProvisionedMusic::find('000050a1b2c3d4e5f6a7b8c9d');
        $provisionedMusic->created_at = $oneYearAgo->format('Y-m-d');
        $provisionedMusic->save();

        $musicDXO = new MusicDXO();
        $musicDXO->checkPromotionVideoAppendCreatedAtGTE($oneYearAgo->format('Y-m-d'));
        $result = $musicApplication->checkPromotionVideo($musicDXO);
        $this->assertTrue($result);

        $promotionVideo = PromotionVideo::musicId('000010a1b2c3d4e5f6a7b8c9d')->first();
        $this->assertEquals($promotionVideo->music_id, '000010a1b2c3d4e5f6a7b8c9d');
        $promotionVideo = PromotionVideo::musicId('000050a1b2c3d4e5f6a7b8c9d')->first();
        $this->assertEquals($promotionVideo->music_id, '000050a1b2c3d4e5f6a7b8c9d');
        $promotionVideoBrokenLink = PromotionVideoBrokenLink::musicId('000010a1b2c3d4e5f6a7b8c9d')->first();
        $this->assertEmpty($promotionVideoBrokenLink);
        $promotionVideoBrokenLink = PromotionVideoBrokenLink::musicId('000050a1b2c3d4e5f6a7b8c9d')->first();
        $this->assertEmpty($promotionVideoBrokenLink);
    }

    public function testCheckPromotionVideoCreatedAtGTE()
    {
        $musicRepository = app($this->musicRepositoryInterfaceName);
        $musicServiceMock = $this->musicServiceMock();
        $musicServiceMock->shouldReceive('checkPromotionVideo')->andReturn(false);
        $musicApplication = new MusicApplication(
            $musicRepository,
            app($this->musicFactoryInterfaceName),
            $musicServiceMock
        );
        $redisDAO = app($this->redisDAOInterfaceName);

        $today = new \DatetimeImmutable(date('Y-m-d'));
        $oneYearAgo = $today->sub(new \DateInterval('P1Y'));
        $oneYearAndOneDayAgo = $oneYearAgo->sub(new \DateInterval('P1D'));

        Music::query()->update(['created_at' => $oneYearAndOneDayAgo->format('Y-m-d')]);
        ProvisionedMusic::query()->update(['created_at' => $oneYearAndOneDayAgo->format('Y-m-d')]);
        $music = Music::find('000010a1b2c3d4e5f6a7b8c9d');
        $music->created_at = $oneYearAgo->format('Y-m-d');
        $music->save();
        $provisionedMusic = ProvisionedMusic::find('000050a1b2c3d4e5f6a7b8c9d');
        $provisionedMusic->created_at = $oneYearAgo->format('Y-m-d');
        $provisionedMusic->save();

        $cacheKey = $musicRepository->cacheKeyById(new EntityId('000010a1b2c3d4e5f6a7b8c9d'), MusicEntity::class);
        $redisDAO->set($cacheKey, '1');
        $cacheKey = $musicRepository->cacheKeyById(new EntityId('000050a1b2c3d4e5f6a7b8c9d'), MusicEntity::class);
        $redisDAO->set($cacheKey, '1');

        $musicDXO = new MusicDXO();
        $musicDXO->checkPromotionVideoAppendCreatedAtGTE($oneYearAgo->format('Y-m-d'));
        $result = $musicApplication->checkPromotionVideo($musicDXO);
        $this->assertTrue($result);

        $idValue = '000010a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($idValue);
        $musicEntity = $musicRepository->findRelease($entityId);
        $this->assertEmpty($musicEntity->promotionVideoUrl());
        $cacheKey = $musicRepository->cacheKeyById($entityId, MusicEntity::class);
        $cache = $redisDAO->get($cacheKey);
        $this->assertEmpty($cache);
        $promotionVideoBrokenLink = PromotionVideoBrokenLink::musicId($idValue)->first();
        $this->assertEquals($promotionVideoBrokenLink->music_id, $idValue);

        $idValue = '000050a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($idValue);
        $musicEntity = $musicRepository->findProvision($entityId);
        $this->assertEmpty($musicEntity->promotionVideoUrl());
        $cacheKey = $musicRepository->cacheKeyById($entityId, MusicEntity::class);
        $cache = $redisDAO->get($cacheKey);
        $this->assertEmpty($cache);
        $promotionVideoBrokenLink = PromotionVideoBrokenLink::musicId($idValue)->first();
        $this->assertEquals($promotionVideoBrokenLink->music_id, $idValue);
    }

    public function testCheckPromotionVideoCreatedAtLTAndIdLike()
    {
        $musicRepository = app($this->musicRepositoryInterfaceName);
        $musicServiceMock = $this->musicServiceMock();
        $musicServiceMock->shouldReceive('checkPromotionVideo')->andReturn(false);
        $musicApplication = new MusicApplication(
            $musicRepository,
            app($this->musicFactoryInterfaceName),
            $musicServiceMock
        );
        $redisDAO = app($this->redisDAOInterfaceName);

        $today = new \DatetimeImmutable(date('Y-m-d'));
        $oneYearAgo = $today->sub(new \DateInterval('P1Y'));
        $oneYearAndOneDayAgo = $oneYearAgo->sub(new \DateInterval('P1D'));

        Music::query()->update(['created_at' => $oneYearAgo->format('Y-m-d')]);
        ProvisionedMusic::query()->update(['created_at' => $oneYearAgo->format('Y-m-d')]);
        $music = Music::find('000010a1b2c3d4e5f6a7b8c9d');
        $music->created_at = $oneYearAndOneDayAgo->format('Y-m-d');
        $music->save();
        $provisionedMusic = ProvisionedMusic::find('000050a1b2c3d4e5f6a7b8c9d');
        $provisionedMusic->created_at = $oneYearAndOneDayAgo->format('Y-m-d');
        $provisionedMusic->save();

        $cacheKey = $musicRepository->cacheKeyById(new EntityId('000010a1b2c3d4e5f6a7b8c9d'), MusicEntity::class);
        $redisDAO->set($cacheKey, '1');
        $cacheKey = $musicRepository->cacheKeyById(new EntityId('000050a1b2c3d4e5f6a7b8c9d'), MusicEntity::class);
        $redisDAO->set($cacheKey, '1');

        $musicDXO = new MusicDXO();
        $musicDXO->checkPromotionVideoAppendCreatedAtLT($oneYearAgo->format('Y-m-d'));
        $musicDXO->checkPromotionVideoAppendMusicIdLike('0000');
        $result = $musicApplication->checkPromotionVideo($musicDXO);
        $this->assertTrue($result);

        $idValue = '000010a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($idValue);
        $musicEntity = $musicRepository->findRelease($entityId);
        $this->assertEmpty($musicEntity->promotionVideoUrl());
        $cacheKey = $musicRepository->cacheKeyById($entityId, MusicEntity::class);
        $cache = $redisDAO->get($cacheKey);
        $this->assertEmpty($cache);
        $promotionVideoBrokenLink = PromotionVideoBrokenLink::musicId($idValue)->first();
        $this->assertEquals($promotionVideoBrokenLink->music_id, $idValue);

        $idValue = '000050a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($idValue);
        $musicEntity = $musicRepository->findProvision($entityId);
        $this->assertEmpty($musicEntity->promotionVideoUrl());
        $cacheKey = $musicRepository->cacheKeyById($entityId, MusicEntity::class);
        $cache = $redisDAO->get($cacheKey);
        $this->assertEmpty($cache);
        $promotionVideoBrokenLink = PromotionVideoBrokenLink::musicId($idValue)->first();
        $this->assertEquals($promotionVideoBrokenLink->music_id, $idValue);
    }

    public function testPromotionVideoBrokenLinksMusicDXO()
    {
        $verify = [
            '000010a1b2c3d4e5f6a7b8c9d',
            '000020a1b2c3d4e5f6a7b8c9d',
            '000050a1b2c3d4e5f6a7b8c9d',
            '000060a1b2c3d4e5f6a7b8c9d',
        ];

        $musicDXO = new MusicDXO();
        foreach ($verify AS $iTunesArtistIdValue) {
            $musicDXO->promotionVideoBrokenLinksAppendItunesArtistId($iTunesArtistIdValue);
        }
        $result = $musicDXO->getItunesArtistIds();
        $iTunesArtistIds = [];
        foreach ($result AS $iTunesArtistId) {
            $iTunesArtistIds[] = $iTunesArtistId->value();
        }
        sort($verify);
        sort($iTunesArtistIds);
        $this->assertEquals($verify, $iTunesArtistIds);
    }

    public function testPromotionVideoBrokenLinks1()
    {
        $verify = [
            '000010a1b2c3d4e5f6a7b8c9d',
            '000020a1b2c3d4e5f6a7b8c9d',
            '000050a1b2c3d4e5f6a7b8c9d',
            '000060a1b2c3d4e5f6a7b8c9d',
        ];
        foreach ($verify AS $musicIdValue) {
            $promotionVideoBrokenLink = new PromotionVideoBrokenLink();
            $promotionVideoBrokenLink->fill(['music_id' => $musicIdValue])->save();
        }

        $musicApplication = app($this->musicApplicationInterfaceName);
        $musicDXO = new MusicDXO();
        $domainPaginator = $musicApplication->promotionVideoBrokenLinks($musicDXO);
        $musicEitities = $domainPaginator->getEntities();
        $musicIds = [];
        foreach ($musicEitities AS $musicEntity) {
            $musicIds[] = $musicEntity->id()->value();
        }
        sort($verify);
        sort($musicIds);
        $this->assertEquals($verify, $musicIds);
        $this->assertEquals($domainPaginator->getPaginator()->total(), 4);
    }

    public function testPromotionVideoBrokenLinks2()
    {
        $data = [
            '000010a1b2c3d4e5f6a7b8c9d',
            '000020a1b2c3d4e5f6a7b8c9d',
            '000050a1b2c3d4e5f6a7b8c9d',
            '000060a1b2c3d4e5f6a7b8c9d',
        ];
        foreach ($data AS $musicIdValue) {
            $promotionVideoBrokenLink = new PromotionVideoBrokenLink();
            $promotionVideoBrokenLink->fill(['music_id' => $musicIdValue])->save();
        }
        $verify = [
            '000010a1b2c3d4e5f6a7b8c9d',
            '000050a1b2c3d4e5f6a7b8c9d',
        ];

        $musicApplication = app($this->musicApplicationInterfaceName);
        $musicDXO = new MusicDXO();
        foreach ($verify AS $iTunesArtistIdValue) {
            $musicDXO->promotionVideoBrokenLinksAppendItunesArtistId($iTunesArtistIdValue);
        }
        $domainPaginator = $musicApplication->promotionVideoBrokenLinks($musicDXO);
        $musicEitities = $domainPaginator->getEntities();
        $musicIds = [];
        foreach ($musicEitities AS $musicEntity) {
            $musicIds[] = $musicEntity->id()->value();
        }
        sort($verify);
        sort($musicIds);
        $this->assertEquals($verify, $musicIds);
        $this->assertEquals($domainPaginator->getPaginator()->total(), 2);
    }

    public function testDeletePromotionVideoBrokenLinkIdEmpty()
    {
        $musicApplication = app($this->musicApplicationInterfaceName);

        $musicDXO = new MusicDXO();
        $result = $musicApplication->deletePromotionVideoBrokenLink($musicDXO);
        $this->assertFalse($result);
    }

    public function testDeletePromotionVideoBrokenLinkRepositoryReturnFalse()
    {
        $musicRepositoryMock = $this->musicRepositoryMock();
        $musicRepositoryMock->shouldReceive('deletePromotionVideoBrokenLink')->andReturn(false);
        $musicApplication = new MusicApplication(
            $musicRepositoryMock,
            app($this->musicFactoryInterfaceName),
            app($this->musicServiceInterfaceName)
        );

        $idValue = '000010a1b2c3d4e5f6a7b8c9d';
        $promotionVideoBrokenLink = new PromotionVideoBrokenLink();
        $promotionVideoBrokenLink->fill(['music_id' => $idValue])->save();

        $musicDXO = new MusicDXO();
        $musicDXO->deletePromotionVideoBrokenLink($idValue);
        $result = $musicApplication->deletePromotionVideoBrokenLink($musicDXO);
        $this->assertFalse($result);
        $promotionVideoBrokenLink = PromotionVideoBrokenLink::musicId($idValue)->first();
        $this->assertEquals($promotionVideoBrokenLink->music_id, $idValue);
    }

    /**
     * @expectedException App\Domain\Music\MusicException
     */
    public function testDeletePromotionVideoBrokenLinkExceptionOccurred()
    {
        $musicRepositoryMock = $this->musicRepositoryMock();
        $musicRepositoryMock->shouldReceive('deletePromotionVideoBrokenLink')->andReturnUsing(
            function ($musicId) {
                throw new MusicException();
            }
        );
        $musicApplication = new MusicApplication(
            $musicRepositoryMock,
            app($this->musicFactoryInterfaceName),
            app($this->musicServiceInterfaceName)
        );

        $idValue = '000010a1b2c3d4e5f6a7b8c9d';
        $promotionVideoBrokenLink = new PromotionVideoBrokenLink();
        $promotionVideoBrokenLink->fill(['music_id' => $idValue])->save();

        $musicDXO = new MusicDXO();
        $musicDXO->deletePromotionVideoBrokenLink($idValue);
        $musicApplication->deletePromotionVideoBrokenLink($musicDXO);
        $promotionVideoBrokenLink = PromotionVideoBrokenLink::musicId($idValue)->first();
        $this->assertEquals($promotionVideoBrokenLink->music_id, $idValue);
    }

    public function testDeletePromotionVideoBrokenLinkId()
    {
        $musicApplication = app($this->musicApplicationInterfaceName);

        $idValue = '000010a1b2c3d4e5f6a7b8c9d';
        $promotionVideoBrokenLink = new PromotionVideoBrokenLink();
        $promotionVideoBrokenLink->fill(['music_id' => $idValue])->save();

        $musicDXO = new MusicDXO();
        $musicDXO->deletePromotionVideoBrokenLink($idValue);
        $result = $musicApplication->deletePromotionVideoBrokenLink($musicDXO);
        $this->assertTrue($result);
        $promotionVideoBrokenLink = PromotionVideoBrokenLink::musicId($idValue)->first();
        $this->assertEmpty($promotionVideoBrokenLink);
    }

    public function testDeleteWithITunesArtistIdParametersEmpty()
    {
        $musicApplication = app($this->musicApplicationInterfaceName);

        $musicDXO = new MusicDXO();
        $result = $musicApplication->deleteWithITunesArtistId($musicDXO);
        $this->assertFalse($result);
    }

    /**
     * @expectedException App\Domain\Music\MusicException
     */
    public function testDeleteWithITunesArtistIdFailedRollback()
    {
        $musicApplication = $this->musicApplicationMock();
        $musicApplication->shouldReceive('rollback')->andReturn(false);

        $iTunesArtistIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $musicDXO = new MusicDXO();
        $musicDXO->deleteWithITunesArtistId($iTunesArtistIdValue);
        $musicApplication->deleteWithITunesArtistId($musicDXO);
    }

    /**
     * @expectedException App\Domain\Music\MusicException
     */
    public function testDeleteWithITunesArtistIdFailedDelete()
    {
        $musicApplication = $this->musicApplicationMock();
        $musicApplication->shouldReceive('delete')->andReturn(false);

        $iTunesArtistIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $musicDXO = new MusicDXO();
        $musicDXO->deleteWithITunesArtistId($iTunesArtistIdValue);
        $musicApplication->deleteWithITunesArtistId($musicDXO);
    }

    public function testDeleteWithITunesArtistId()
    {
        $musicApplication = app($this->musicApplicationInterfaceName);
        $musicRepository = app($this->musicRepositoryInterfaceName);

        $iTunesArtistIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $musicDXO = new MusicDXO();
        $musicDXO->deleteWithITunesArtistId($iTunesArtistIdValue);
        $result = $musicApplication->deleteWithITunesArtistId($musicDXO);
        $this->assertTrue($result);
        $iTunesArtistId = new ITunesArtistId($iTunesArtistIdValue);
        $provisioned = $musicRepository->provisionedEntities($iTunesArtistId, null, new MusicSpecification());
        $this->assertEmpty($provisioned);
        $released = $musicRepository->releasedEntities($iTunesArtistId, null, new MusicSpecification());
        $this->assertEmpty($released);
    }

    public function testReplaceITunesArtistIdParametersEmpty()
    {
        $musicApplication = app($this->musicApplicationInterfaceName);

        $oldITunesArtistIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistIdValue = '000020a1b2c3d4e5f6a7b8c9d';

        $musicDXO = new MusicDXO();
        $result = $musicApplication->replaceITunesArtistId($musicDXO);
        $this->assertFalse($result);

        $musicDXO = new MusicDXO();
        $musicDXO->replaceITunesArtistId($oldITunesArtistIdValue, '');
        $result = $musicApplication->replaceITunesArtistId($musicDXO);
        $this->assertFalse($result);

        $musicDXO = new MusicDXO();
        $musicDXO->replaceITunesArtistId('', $iTunesArtistIdValue);
        $result = $musicApplication->replaceITunesArtistId($musicDXO);
        $this->assertFalse($result);
    }

    public function testReplaceITunesArtistIdITunesArtistIdNotChange()
    {
        $eventPublished = false;
        $musicApplication = app($this->musicApplicationInterfaceName);
        Event::shouldReceive('dispatch')->andReturnUsing(
            function ($event) use (&$eventPublished) {
                $eventName = 'App\Events\ArtistModified';
                if ($event instanceof $eventName) {
                    $eventPublished = true;
                }
            }
        );

        $oldITunesArtistIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistIdValue = '000010a1b2c3d4e5f6a7b8c9d';

        $eventPublished = false;
        $musicDXO = new MusicDXO();
        $musicDXO->replaceITunesArtistId($oldITunesArtistIdValue, $iTunesArtistIdValue);
        $result = $musicApplication->replaceITunesArtistId($musicDXO);
        $this->assertTrue($result);
        $this->assertFalse($eventPublished);
    }

    public function testReplaceITunesArtistIdModifyProvisionReturnFalse()
    {
        $musicRepositoryMock = $this->musicRepositoryMock();
        $musicRepositoryMock->shouldReceive('modifyProvision')->andReturn(false);
        $musicApplication = new MusicApplication(
            $musicRepositoryMock,
            app($this->musicFactoryInterfaceName),
            app($this->musicServiceInterfaceName)
        );

        $entityIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $oldITunesArtistIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistIdValue = '000060a1b2c3d4e5f6a7b8c9d';
        $musicDXO = new MusicDXO();
        $musicDXO->replaceITunesArtistId($oldITunesArtistIdValue, $iTunesArtistIdValue);
        $result = $musicApplication->replaceITunesArtistId($musicDXO);
        $this->assertFalse($result);
        $entityId = new EntityId($entityIdValue);
        $musicEntity = $musicRepositoryMock->findProvision($entityId);
        $this->assertEquals($musicEntity->iTunesArtistId()->value(), $oldITunesArtistIdValue);
    }

    public function testReplaceITunesArtistIdModifyReleaseReturnFalse()
    {
        $musicRepositoryMock = $this->musicRepositoryMock();
        $musicRepositoryMock->shouldReceive('modifyRelease')->andReturn(false);
        $musicApplication = new MusicApplication(
            $musicRepositoryMock,
            app($this->musicFactoryInterfaceName),
            app($this->musicServiceInterfaceName)
        );

        $entityIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $oldITunesArtistIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistIdValue = '000020a1b2c3d4e5f6a7b8c9d';
        $musicDXO = new MusicDXO();
        $musicDXO->replaceITunesArtistId($oldITunesArtistIdValue, $iTunesArtistIdValue);
        $result = $musicApplication->replaceITunesArtistId($musicDXO);
        $this->assertFalse($result);
        $entityId = new EntityId($entityIdValue);
        $musicEntity = $musicRepositoryMock->findRelease($entityId);
        $this->assertEquals($musicEntity->iTunesArtistId()->value(), $oldITunesArtistIdValue);
    }

    public function testReplaceITunesArtistId()
    {
        $dispatched = [];
        $entityIdValue1 = '000010a1b2c3d4e5f6a7b8c9d';
        $entityIdValue2 = '000050a1b2c3d4e5f6a7b8c9d';
        $oldITunesArtistIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistIdValue = '000020a1b2c3d4e5f6a7b8c9d';
        Event::shouldReceive('dispatch')->andReturnUsing(
            function ($event) use (&$dispatched) {
                $eventName = 'App\Events\MusicModified';
                if ($event instanceof $eventName) {
                    $dispatched[] = $event->entityIdValue();
                }
            }
        );
        $provisionedMusic = ProvisionedMusic::find($entityIdValue2);
        $provisionedMusic->itunes_artist_id = $oldITunesArtistIdValue;
        $provisionedMusic->save();
        $musicApplication = app($this->musicApplicationInterfaceName);
        $musicRepository = app($this->musicRepositoryInterfaceName);

        $verify = [$entityIdValue1, $entityIdValue2];
        $musicDXO = new MusicDXO();
        $musicDXO->replaceITunesArtistId($oldITunesArtistIdValue, $iTunesArtistIdValue);
        $result = $musicApplication->replaceITunesArtistId($musicDXO);
        $this->assertTrue($result);
        sort($verify);
        sort($dispatched);
        $this->assertEquals($verify, $dispatched);

        $entityId = new EntityId($entityIdValue1);
        $musicEntity = $musicRepository->findRelease($entityId);
        $this->assertEquals($musicEntity->iTunesArtistId()->value(), $iTunesArtistIdValue);

        $entityId = new EntityId($entityIdValue2);
        $musicEntity = $musicRepository->findProvision($entityId);
        $this->assertEquals($musicEntity->iTunesArtistId()->value(), $iTunesArtistIdValue);
    }

    public function testProvisionedPaginatorParametersEmpty()
    {
        $musicApplication = app($this->musicApplicationInterfaceName);

        $musicDXO = new MusicDXO();
        $result = $musicApplication->provisionedPaginator($musicDXO);
        $this->assertNull($result);
    }

    public function testProvisionedPaginator()
    {
        $provisionedPaginatorCalled = true;
        $musicRepositoryMock = $this->musicRepositoryMock();
        $musicRepositoryMock->shouldReceive('provisionedPaginator')->andReturnUsing(
            function ($iTunesArtistId, $musicTitle, $musicSpecification) use (&$provisionedPaginatorCalled) {
                $provisionedPaginatorCalled = true;
            }
        );
        $musicApplication = new MusicApplication(
            $musicRepositoryMock,
            app($this->musicFactoryInterfaceName),
            app($this->musicServiceInterfaceName)
        );

        $musicDXO = new MusicDXO();
        $musicDXO->provisionedPaginator('000050a1b2c3d4e5f6a7b8c9d', 'Not Found');
        $domainPaginator = $musicApplication->provisionedPaginator($musicDXO);
        $this->assertTrue($provisionedPaginatorCalled);
    }

    public function testReleasedPaginatorParametersEmpty()
    {
        $musicApplication = app($this->musicApplicationInterfaceName);

        $musicDXO = new MusicDXO();
        $result = $musicApplication->releasedPaginator($musicDXO);
        $this->assertNull($result);
    }

    public function testReleasedPaginator()
    {
        $releasedPaginatorCalled = true;
        $musicRepositoryMock = $this->musicRepositoryMock();
        $musicRepositoryMock->shouldReceive('releasedPaginator')->andReturnUsing(
            function ($iTunesArtistId, $musicTitle, $musicSpecification) use (&$releasedPaginatorCalled) {
                $releasedPaginatorCalled = true;
            }
        );
        $musicApplication = new MusicApplication(
            $musicRepositoryMock,
            app($this->musicFactoryInterfaceName),
            app($this->musicServiceInterfaceName)
        );

        $musicDXO = new MusicDXO();
        $musicDXO->releasedPaginator('000010a1b2c3d4e5f6a7b8c9d', 'Not Found');
        $domainPaginator = $musicApplication->releasedPaginator($musicDXO);
        $this->assertTrue($releasedPaginatorCalled);
    }

}
