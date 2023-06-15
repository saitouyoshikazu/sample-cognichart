<?php

namespace Tests\Unit\Application\AbstractArtistMusic;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mockery;
use Config;
use Event;
use App\Application\DXO\AbstractArtistMusicDXO;

class AbstractArtistMusicApplicationTest extends TestCase
{

    use RefreshDatabase, DatabaseMigrations;

    private $redisDAOInterfaceName = 'App\Infrastructure\RedisDAO\RedisDAOInterface';
    private $abstractArtistMusicApplicationInterfaceName = 'App\Application\AbstractArtistMusic\AbstractArtistMusicApplicationInterface';

    public function setUp()
    {
        parent::setUp();
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testProvider()
    {
        $abstractArtistMusicApplication = app($this->abstractArtistMusicApplicationInterfaceName);
        $this->assertEquals(get_class($abstractArtistMusicApplication), 'App\Application\AbstractArtistMusic\AbstractArtistMusicApplication');
    }

    public function testPrepareEmptyParameters()
    {
        $abstractArtistMusicApplication = app($this->abstractArtistMusicApplicationInterfaceName);

        $entityIdValue = '';
        $chartArtistValue = 'Bebe Rexha & Florida Georgia Line';
        $chartMusicValue = 'Meant To Be';
        $abstractArtistMusicDXO = new AbstractArtistMusicDXO();
        $abstractArtistMusicDXO->prepare($entityIdValue, $chartArtistValue, $chartMusicValue);
        $result = $abstractArtistMusicApplication->prepare($abstractArtistMusicDXO);
        $this->assertFalse($result);

        $entityIdValue = 'ThisWillExist';
        $chartArtistValue = '';
        $chartMusicValue = 'Meant To Be';
        $abstractArtistMusicDXO = new AbstractArtistMusicDXO();
        $abstractArtistMusicDXO->prepare($entityIdValue, $chartArtistValue, $chartMusicValue);
        $result = $abstractArtistMusicApplication->prepare($abstractArtistMusicDXO);
        $this->assertFalse($result);

        $entityIdValue = 'ThisWillExist';
        $chartArtistValue = 'Bebe Rexha & Florida Georgia Line';
        $chartMusicValue = '';
        $abstractArtistMusicDXO = new AbstractArtistMusicDXO();
        $abstractArtistMusicDXO->prepare($entityIdValue, $chartArtistValue, $chartMusicValue);
        $result = $abstractArtistMusicApplication->prepare($abstractArtistMusicDXO);
        $this->assertFalse($result);
    }

    public function testPrepareFailedByConfig()
    {
        $abstractArtistMusicApplication = app($this->abstractArtistMusicApplicationInterfaceName);

        $entityIdValue = 'ThisWillExist';
        $chartArtistValue = 'Bebe Rexha & Florida Georgia Line';
        $chartMusicValue = 'Meant To Be';
        $abstractArtistMusicDXO = new AbstractArtistMusicDXO();
        $abstractArtistMusicDXO->prepare($entityIdValue, $chartArtistValue, $chartMusicValue);

        Config::shouldReceive('get')->with('app.artist_music_resolve_api')->andReturnUsing(
            function ($confName) {
                return null;
            }
        );
        $result = $abstractArtistMusicApplication->prepare($abstractArtistMusicDXO);
        $this->assertFalse($result);

        Config::shouldReceive('get')->with('app.artist_music_resolve_api')->andReturnUsing(
            function ($confName) {
                return [];
            }
        );
        Config::makePartial();
        $result = $abstractArtistMusicApplication->prepare($abstractArtistMusicDXO);
        $this->assertFalse($result);
    }

    public function testPrepare()
    {
        $abstractArtistMusicApplication = app($this->abstractArtistMusicApplicationInterfaceName);

        $entityIdValue = 'DoNotExist';
        $chartArtistValue = 'AAAAABBBBBCCCCC';
        $chartMusicValue = 'AAAAABBBBBCCCCC';
        $abstractArtistMusicDXO = new AbstractArtistMusicDXO();
        $abstractArtistMusicDXO->prepare($entityIdValue, $chartArtistValue, $chartMusicValue);
        $result = $abstractArtistMusicApplication->prepare($abstractArtistMusicDXO);
        $this->assertTrue($result);

        sleep(10);

        $entityIdValue = 'ThisWillExist';
        $chartArtistValue = 'Bebe Rexha & Florida Georgia Line';
        $chartMusicValue = 'Meant To Be';
        $abstractArtistMusicDXO = new AbstractArtistMusicDXO();
        $abstractArtistMusicDXO->prepare($entityIdValue, $chartArtistValue, $chartMusicValue);
        $result = $abstractArtistMusicApplication->prepare($abstractArtistMusicDXO);
        $this->assertTrue($result);

        $redisDAO = app($this->redisDAOInterfaceName);
        $redisDAO->clear('*');
        $redisDAO->resetIsCache();
    }

    public function testResolveEmptyParameters()
    {
        $abstractArtistMusicApplication = app($this->abstractArtistMusicApplicationInterfaceName);

        $entityIdValue = '';
        $chartArtistValue = 'Bebe Rexha & Florida Georgia Line';
        $chartMusicValue = 'Meant To Be';
        $abstractArtistMusicDXO = new AbstractArtistMusicDXO();
        $abstractArtistMusicDXO->resolve($entityIdValue, $chartArtistValue, $chartMusicValue);
        $result = $abstractArtistMusicApplication->resolve($abstractArtistMusicDXO);
        $this->assertFalse($result);

        $entityIdValue = 'ThisWillExist';
        $chartArtistValue = '';
        $chartMusicValue = 'Meant To Be';
        $abstractArtistMusicDXO = new AbstractArtistMusicDXO();
        $abstractArtistMusicDXO->resolve($entityIdValue, $chartArtistValue, $chartMusicValue);
        $result = $abstractArtistMusicApplication->resolve($abstractArtistMusicDXO);
        $this->assertFalse($result);

        $entityIdValue = 'ThisWillExist';
        $chartArtistValue = 'Bebe Rexha & Florida Georgia Line';
        $chartMusicValue = '';
        $abstractArtistMusicDXO = new AbstractArtistMusicDXO();
        $abstractArtistMusicDXO->resolve($entityIdValue, $chartArtistValue, $chartMusicValue);
        $result = $abstractArtistMusicApplication->resolve($abstractArtistMusicDXO);
        $this->assertFalse($result);
    }

    public function testResolveFailedByConfig()
    {
        $abstractArtistMusicApplication = app($this->abstractArtistMusicApplicationInterfaceName);

        $entityIdValue = 'ThisWillExist';
        $chartArtistValue = 'Bebe Rexha & Florida Georgia Line';
        $chartMusicValue = 'Meant To Be';
        $abstractArtistMusicDXO = new AbstractArtistMusicDXO();
        $abstractArtistMusicDXO->resolve($entityIdValue, $chartArtistValue, $chartMusicValue);

        Config::shouldReceive('get')->with('app.artist_music_resolve_api')->andReturnUsing(
            function ($confName) {
                return null;
            }
        );
        $result = $abstractArtistMusicApplication->resolve($abstractArtistMusicDXO);
        $this->assertFalse($result);

        Config::shouldReceive('get')->with('app.artist_music_resolve_api')->andReturnUsing(
            function ($confName) {
                return [];
            }
        );
        $result = $abstractArtistMusicApplication->resolve($abstractArtistMusicDXO);
        $this->assertFalse($result);
    }

    public function testResolve()
    {
        $abstractArtistMusicApplication = app($this->abstractArtistMusicApplicationInterfaceName);

        $eventPublished = false;
        Event::shouldReceive('dispatch')->with(Mockery::type('App\Events\ArtistMusicResolved'))->andReturnUsing(
            function ($event) use (&$eventPublished) {
                $eventPublished = false;
                $class = 'App\Events\ArtistMusicResolved';
                if ($event instanceof $class) {
                    $eventPublished = true;
                }
            }
        );

        $eventPublished = false;
        $entityIdValue = 'DoNotExist';
        $chartArtistValue = 'AAAAABBBBBCCCCC';
        $chartMusicValue = 'AAAAABBBBBCCCCC';
        $abstractArtistMusicDXO = new AbstractArtistMusicDXO();
        $abstractArtistMusicDXO->prepare($entityIdValue, $chartArtistValue, $chartMusicValue);
        $abstractArtistMusicApplication->prepare($abstractArtistMusicDXO);
        $abstractArtistMusicDXO->resolve($entityIdValue, $chartArtistValue, $chartMusicValue);
        $result = $abstractArtistMusicApplication->resolve($abstractArtistMusicDXO);
        $this->assertTrue($result);
        $this->assertFalse($eventPublished);

        $eventPublished = false;
        $entityIdValue = 'ThisWillExist';
        $chartArtistValue = 'Bebe Rexha & Florida Georgia Line';
        $chartMusicValue = 'Meant To Be';
        $abstractArtistMusicDXO = new AbstractArtistMusicDXO();
        $abstractArtistMusicDXO->prepare($entityIdValue, $chartArtistValue, $chartMusicValue);
        $abstractArtistMusicApplication->prepare($abstractArtistMusicDXO);
        $abstractArtistMusicDXO->resolve($entityIdValue, $chartArtistValue, $chartMusicValue);
        $result = $abstractArtistMusicApplication->resolve($abstractArtistMusicDXO);
        $this->assertTrue($result);
        $this->assertTrue($eventPublished);

        $redisDAO = app($this->redisDAOInterfaceName);
        $redisDAO->clear('*');
        $redisDAO->resetIsCache();
    }

}
