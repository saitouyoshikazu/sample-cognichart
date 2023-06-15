<?php

namespace Tests\Unit\Application\Artist;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mockery;
use Event;
use DB;
use App\Infrastructure\Eloquents\Artist;
use App\Infrastructure\Eloquents\ProvisionedArtist;
use App\Application\Artist\ArtistApplication;
use App\Application\DXO\ArtistDXO;
use App\Domain\ValueObjects\Phase;
use App\Domain\Artist\ArtistEntity;

class ArtistApplicationTest extends TestCase
{

    use DatabaseMigrations;

    private $redisDAOInterfaceName = 'App\Infrastructure\RedisDAO\RedisDAOInterface';
    private $artistApplicationInterfaceName = 'App\Application\Artist\ArtistApplicationInterface';
    private $artistFactoryInterfaceName = 'App\Domain\Artist\ArtistFactoryInterface';
    private $artistRepositoryInterfaceName = 'App\Domain\Artist\ArtistRepositoryInterface';

    private function artistFactoryMock()
    {
        return Mockery::mock('App\Domain\Artist\ArtistFactory')->makePartial();
    }

    private function artistRepositoryMock()
    {
        return Mockery::mock(
            'App\Infrastructure\Repositories\ArtistRepository',
            [
                app($this->redisDAOInterfaceName),
                app($this->artistFactoryInterfaceName)
            ]
        )->makePartial();
    }

    public function setUp()
    {
        parent::setUp();

        factory(Artist::class, 10)->create();
        factory(ProvisionedArtist::class, 10)->create();
    }

    public function tearDown()
    {
        $redisDAO = app($this->redisDAOInterfaceName);
        $redisDAO->clear('*');
        $redisDAO->resetIsCache();

        Mockery::close();

        Artist::truncate();
        ProvisionedArtist::truncate();

        DB::disconnect();
    }

    public function testProvider()
    {
        $artistApplication = app($this->artistApplicationInterfaceName);
        $this->assertEquals(get_class($artistApplication), ArtistApplication::class);
    }

    public function testFindEmptyParameters()
    {
        $artistApplication = app($this->artistApplicationInterfaceName);

        $phaseValue = '';
        $idValue = '000010a1b2c3d4e5f6a7b8c9d';
        $artistDXO = new ArtistDXO();
        $artistDXO->find($phaseValue, $idValue);
        $artistEntity = $artistApplication->find($artistDXO);
        $this->assertNull($artistEntity);

        $phaseValue = Phase::released;
        $idValue = '';
        $artistDXO = new ArtistDXO();
        $artistDXO->find($phaseValue, $idValue);
        $artistEntity = $artistApplication->find($artistDXO);
        $this->assertNull($artistEntity);
    }

    public function testFind()
    {
        $artistApplication = app($this->artistApplicationInterfaceName);

        $phaseValue = Phase::released;
        $idValue = '000050a1b2c3d4e5f6a7b8c9d';
        $artistDXO = new ArtistDXO();
        $artistDXO->find($phaseValue, $idValue);
        $artistEntity = $artistApplication->find($artistDXO);
        $this->assertNull($artistEntity);

        $phaseValue = Phase::released;
        $idValue = '000010a1b2c3d4e5f6a7b8c9d';
        $artistDXO = new ArtistDXO();
        $artistDXO->find($phaseValue, $idValue);
        $artistEntity = $artistApplication->find($artistDXO);
        $this->assertEquals($artistEntity->id()->value(), $idValue);

        $phaseValue = Phase::provisioned;
        $idValue = '000010a1b2c3d4e5f6a7b8c9d';
        $artistDXO = new ArtistDXO();
        $artistDXO->find($phaseValue, $idValue);
        $artistEntity = $artistApplication->find($artistDXO);
        $this->assertNull($artistEntity);

        $phaseValue = Phase::provisioned;
        $idValue = '000050a1b2c3d4e5f6a7b8c9d';
        $artistDXO = new ArtistDXO();
        $artistDXO->find($phaseValue, $idValue);
        $artistEntity = $artistApplication->find($artistDXO);
        $this->assertEquals($artistEntity->id()->value(), $idValue);
    }

    public function testGetEmptyParameters()
    {
        $artistApplication = app($this->artistApplicationInterfaceName);

        $phaseValue = '';
        $iTunesArtistIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $artistDXO = new ArtistDXO();
        $artistDXO->get($phaseValue, $iTunesArtistIdValue);
        $artistEntity = $artistApplication->get($artistDXO);
        $this->assertNull($artistEntity);

        $phaseValue = Phase::released;
        $iTunesArtistIdValue = '';
        $artistDXO = new ArtistDXO();
        $artistDXO->get($phaseValue, $iTunesArtistIdValue);
        $artistEntity = $artistApplication->get($artistDXO);
        $this->assertNull($artistEntity);
    }

    public function testGet()
    {
        $artistApplication = app($this->artistApplicationInterfaceName);

        $phaseValue = Phase::released;
        $iTunesArtistIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $artistDXO = new ArtistDXO();
        $artistDXO->get($phaseValue, $iTunesArtistIdValue);
        $artistEntity = $artistApplication->get($artistDXO);
        $this->assertNull($artistEntity);

        $phaseValue = Phase::released;
        $iTunesArtistIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $artistDXO = new ArtistDXO();
        $artistDXO->get($phaseValue, $iTunesArtistIdValue);
        $artistEntity = $artistApplication->get($artistDXO);
        $this->assertEquals($artistEntity->iTunesArtistId()->value(), $iTunesArtistIdValue);

        $phaseValue = Phase::provisioned;
        $iTunesArtistIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $artistDXO = new ArtistDXO();
        $artistDXO->get($phaseValue, $iTunesArtistIdValue);
        $artistEntity = $artistApplication->get($artistDXO);
        $this->assertNull($artistEntity);

        $phaseValue = Phase::provisioned;
        $iTunesArtistIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $artistDXO = new ArtistDXO();
        $artistDXO->get($phaseValue, $iTunesArtistIdValue);
        $artistEntity = $artistApplication->get($artistDXO);
        $this->assertEquals($artistEntity->iTunesArtistId()->value(), $iTunesArtistIdValue);
    }

    public function testRegisterEmptyParameters()
    {
        $artistApplication = app($this->artistApplicationInterfaceName);

        $iTunesArtistIdValue = '';
        $artistNameValue = 'Ed Sheeran';
        $artistDXO = new ArtistDXO();
        $artistDXO->register($iTunesArtistIdValue, $artistNameValue);
        $result = $artistApplication->register($artistDXO);
        $this->assertFalse($result);

        $iTunesArtistIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $artistNameValue = '';
        $artistDXO = new ArtistDXO();
        $artistDXO->register($iTunesArtistIdValue, $artistNameValue);
        $result = $artistApplication->register($artistDXO);
        $this->assertFalse($result);
    }

    public function testRegisterFactoryCreateEmpty()
    {
        $artistFactoryMock = $this->artistFactoryMock();
        $artistFactoryMock->shouldReceive('create')->andReturn(null);
        $artistApplication = new ArtistApplication(
            app($this->artistRepositoryInterfaceName),
            $artistFactoryMock
        );

        $artistDXO = new ArtistDXO();
        $artistDXO->register('000090a1b2c3d4e5f6a7b8c9d', 'Halsey');
        $result = $artistApplication->register($artistDXO);
        $this->assertFalse($result);
    }

    public function testRegisterRepositoryReturnFalse()
    {
        $artistRepositoryMock = $this->artistRepositoryMock();
        $artistRepositoryMock->shouldReceive('register')->andReturn(false);
        $artistApplication = new ArtistApplication(
            $artistRepositoryMock,
            app($this->artistFactoryInterfaceName)
        );

        $artistDXO = new ArtistDXO();
        $artistDXO->register('000090a1b2c3d4e5f6a7b8c9d', 'Halsey');
        $result = $artistApplication->register($artistDXO);
        $this->assertFalse($result);
    }

    /**
     * @expectedException App\Domain\Artist\ArtistException
     */
    public function testRegisterExceptionOccurred()
    {
        $artistApplication = app($this->artistApplicationInterfaceName);

        $artistDXO = new ArtistDXO();
        $artistDXO->register('000010a1b2c3d4e5f6a7b8c9d', 'Ed Sheeran');
        $artistApplication->register($artistDXO);
    }

    public function testRegister()
    {
        $artistApplication = app($this->artistApplicationInterfaceName);

        $iTunesArtistIdValue = '000090a1b2c3d4e5f6a7b8c9d';
        $artistNameValue = 'Halsey';
        $artistDXO = new ArtistDXO();
        $artistDXO->register($iTunesArtistIdValue, $artistNameValue);
        $result = $artistApplication->register($artistDXO);
        $this->assertTrue($result);
        $artistDXO = new ArtistDXO();
        $artistDXO->get(Phase::provisioned, $iTunesArtistIdValue);
        $artistEntity = $artistApplication->get($artistDXO);
        $this->assertEquals($artistEntity->iTunesArtistId()->value(), $iTunesArtistIdValue);
        $this->assertEquals($artistEntity->artistName()->value(), $artistNameValue);
    }

    public function testModifyEmptyParameters()
    {
        $artistApplication = app($this->artistApplicationInterfaceName);

        $phaseValue = '';
        $entityIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $artistNameValue = 'Ed Sheeran';
        $artistDXO = new ArtistDXO();
        $artistDXO->modify($phaseValue, $entityIdValue, $iTunesArtistIdValue, $artistNameValue);
        $result = $artistApplication->modify($artistDXO);
        $this->assertFalse($result);

        $phaseValue = Phase::released;
        $entityIdValue = '';
        $iTunesArtistIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $artistNameValue = 'Ed Sheeran';
        $artistDXO = new ArtistDXO();
        $artistDXO->modify($phaseValue, $entityIdValue, $iTunesArtistIdValue, $artistNameValue);
        $result = $artistApplication->modify($artistDXO);
        $this->assertFalse($result);

        $phaseValue = Phase::released;
        $entityIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistIdValue = '';
        $artistNameValue = 'Ed Sheeran';
        $artistDXO = new ArtistDXO();
        $artistDXO->modify($phaseValue, $entityIdValue, $iTunesArtistIdValue, $artistNameValue);
        $result = $artistApplication->modify($artistDXO);
        $this->assertFalse($result);

        $phaseValue = Phase::released;
        $entityIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $artistNameValue = '';
        $artistDXO = new ArtistDXO();
        $artistDXO->modify($phaseValue, $entityIdValue, $iTunesArtistIdValue, $artistNameValue);
        $result = $artistApplication->modify($artistDXO);
        $this->assertFalse($result);
    }

    public function testModifyEntityNotFound()
    {
        $artistRepositoryMock = $this->artistRepositoryMock();
        $artistRepositoryMock->shouldReceive('findProvision')->andReturn(null);
        $artistRepositoryMock->shouldReceive('findRelease')->andReturn(null);
        $artistApplication = new ArtistApplication(
            $artistRepositoryMock,
            app($this->artistFactoryInterfaceName)
        );

        $phaseValue = Phase::released;
        $entityIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $artistNameValue = 'Ed Sheeran';
        $artistDXO = new ArtistDXO();
        $artistDXO->modify($phaseValue, $entityIdValue, $iTunesArtistIdValue, $artistNameValue);
        $result = $artistApplication->modify($artistDXO);
        $this->assertFalse($result);

        $phaseValue = Phase::provisioned;
        $entityIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $artistNameValue = 'Lil Pump';
        $artistDXO = new ArtistDXO();
        $artistDXO->modify($phaseValue, $entityIdValue, $iTunesArtistIdValue, $artistNameValue);
        $result = $artistApplication->modify($artistDXO);
        $this->assertFalse($result);
    }

    public function testModifyRepositoryReturnFalse()
    {
        $artistRepositoryMock = $this->artistRepositoryMock();
        $artistRepositoryMock->shouldReceive('modifyProvision')->andReturn(false);
        $artistRepositoryMock->shouldReceive('modifyRelease')->andReturn(false);
        $artistApplication = new ArtistApplication(
            $artistRepositoryMock,
            app($this->artistFactoryInterfaceName)
        );

        $phaseValue = Phase::released;
        $entityIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $artistNameValue = 'Ed Sheeran';
        $artistDXO = new ArtistDXO();
        $artistDXO->modify($phaseValue, $entityIdValue, $iTunesArtistIdValue, $artistNameValue);
        $result = $artistApplication->modify($artistDXO);
        $this->assertFalse($result);

        $phaseValue = Phase::provisioned;
        $entityIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $artistNameValue = 'Lil Pump';
        $artistDXO = new ArtistDXO();
        $artistDXO->modify($phaseValue, $entityIdValue, $iTunesArtistIdValue, $artistNameValue);
        $result = $artistApplication->modify($artistDXO);
        $this->assertFalse($result);
    }

    /**
     * @expectedException App\Domain\Artist\ArtistException
     */
    public function testModifyReleaseExceptionOccurred()
    {
        $artistApplication = app($this->artistApplicationInterfaceName);

        $phaseValue = Phase::released;
        $entityIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistIdValue = '000020a1b2c3d4e5f6a7b8c9d';
        $artistNameValue = 'Ed Sheeran';
        $artistDXO = new ArtistDXO();
        $artistDXO->modify($phaseValue, $entityIdValue, $iTunesArtistIdValue, $artistNameValue);
        $artistApplication->modify($artistDXO);
    }

    /**
     * @expectedException App\Domain\Artist\ArtistException
     */
    public function testModifyProvisionedExceptionOccurred()
    {
        $artistApplication = app($this->artistApplicationInterfaceName);

        $phaseValue = Phase::provisioned;
        $entityIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistIdValue = '000060a1b2c3d4e5f6a7b8c9d';
        $artistNameValue = 'Lil Pump';
        $artistDXO = new ArtistDXO();
        $artistDXO->modify($phaseValue, $entityIdValue, $iTunesArtistIdValue, $artistNameValue);
        $artistApplication->modify($artistDXO);
    }

    public function testModify()
    {
        $eventPublished = false;
        Event::shouldReceive('dispatch')->andReturnUsing(
            function ($event) use (&$eventPublished) {
                $eventName = 'App\Events\ArtistModified';
                if ($event instanceof $eventName) {
                    $eventPublished = true;
                }
            }
        );
        $artistApplication = app($this->artistApplicationInterfaceName);

        $eventPublished = false;
        $phaseValue = Phase::released;
        $entityIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $artistNameValue = 'Ed Sheeran+';
        $artistDXO = new ArtistDXO();
        $artistDXO->modify($phaseValue, $entityIdValue, $iTunesArtistIdValue, $artistNameValue);
        $result = $artistApplication->modify($artistDXO);
        $this->assertTrue($result);
        $this->assertTrue($eventPublished);

        $eventPublished = false;
        $phaseValue = Phase::provisioned;
        $entityIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $artistNameValue = 'Lil Pump+';
        $artistDXO = new ArtistDXO();
        $artistDXO->modify($phaseValue, $entityIdValue, $iTunesArtistIdValue, $artistNameValue);
        $result = $artistApplication->modify($artistDXO);
        $this->assertTrue($result);
        $this->assertTrue($eventPublished);
    }

    public function testDeleteEmptyParameters()
    {
        $artistApplication = app($this->artistApplicationInterfaceName);

        $entityIdValue = '';
        $artistDXO = new ArtistDXO();
        $artistDXO->delete($entityIdValue);
        $result = $artistApplication->delete($artistDXO);
        $this->assertFalse($result);
    }

    public function testDeleteRepositoryReturnFalse()
    {
        $artistRepositoryMock = $this->artistRepositoryMock();
        $artistRepositoryMock->shouldReceive('delete')->andReturn(false);
        $artistApplication = new ArtistApplication(
            $artistRepositoryMock,
            app($this->artistFactoryInterfaceName)
        );

        $entityIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $artistDXO = new ArtistDXO();
        $artistDXO->delete($entityIdValue);
        $result = $artistApplication->delete($artistDXO);
        $this->assertFalse($result);
    }

    /**
     * @expectedException App\Domain\Artist\ArtistException
     */
    public function testDeleteExceptionOccurred()
    {
        $artistApplication = app($this->artistApplicationInterfaceName);

        $entityIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $artistDXO = new ArtistDXO();
        $artistDXO->delete($entityIdValue);
        $artistApplication->delete($artistDXO);
    }

    public function testDelete()
    {
        $artistApplication = app($this->artistApplicationInterfaceName);
        $eventPublished = false;
        Event::shouldReceive('dispatch')->andReturnUsing(
            function ($event) use (&$eventPublished) {
                $eventName = 'App\Events\ArtistDeleted';
                if ($event instanceof $eventName) {
                    $eventPublished = true;
                }
            }
        );

        $eventPublished = false;
        $entityIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $artistDXO = new ArtistDXO();
        $artistDXO->delete($entityIdValue);
        $result = $artistApplication->delete($artistDXO);
        $this->assertTrue($result);
        $this->assertTrue($eventPublished);
    }

    public function testReleaseEmptyParameters()
    {
        $artistApplication = app($this->artistApplicationInterfaceName);

        $entityIdValue = '';
        $artistDXO = new ArtistDXO();
        $artistDXO->release($entityIdValue);
        $result = $artistApplication->release($artistDXO);
        $this->assertFalse($result);
    }

    public function testReleaseRepositoryReturnFalse()
    {
        $artistRepositoryMock = $this->artistRepositoryMock();
        $artistRepositoryMock->shouldReceive('release')->andReturn(false);
        $artistApplication = new ArtistApplication(
            $artistRepositoryMock,
            app($this->artistFactoryInterfaceName)
        );

        $entityIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $artistDXO = new ArtistDXO();
        $artistDXO->release($entityIdValue);
        $result = $artistApplication->release($artistDXO);
        $this->assertFalse($result);
    }

    /**
     * @expectedException App\Domain\Artist\ArtistException
     */
    public function testReleaseExceptionOccurred()
    {
        $artistApplication = app($this->artistApplicationInterfaceName);

        $entityIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $artistDXO = new ArtistDXO();
        $artistDXO->release($entityIdValue);
        $artistApplication->release($artistDXO);
    }

    public function testRelease()
    {
        $artistApplication = app($this->artistApplicationInterfaceName);

        $entityIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $artistDXO = new ArtistDXO();
        $artistDXO->release($entityIdValue);
        $result = $artistApplication->release($artistDXO);
        $this->assertTrue($result);
        $artistDXO = new ArtistDXO();
        $artistDXO->find(Phase::provisioned, $entityIdValue);
        $provisionedArtistEntity = $artistApplication->find($artistDXO);
        $this->assertNull($provisionedArtistEntity);
        $artistDXO = new ArtistDXO();
        $artistDXO->find(Phase::released, $entityIdValue);
        $releasedArtistEntity = $artistApplication->find($artistDXO);
        $this->assertEquals($releasedArtistEntity->id()->value(), $entityIdValue);
    }

    public function testRollbackEmptyParameters()
    {
        $artistApplication = app($this->artistApplicationInterfaceName);

        $entityIdValue = '';
        $artistDXO = new ArtistDXO();
        $artistDXO->rollback($entityIdValue);
        $result = $artistApplication->rollback($artistDXO);
        $this->assertFalse($result);
    }

    public function testRollbackRepositoryReturnFalse()
    {
        $artistRepositoryMock = $this->artistRepositoryMock();
        $artistRepositoryMock->shouldReceive('rollback')->andReturn(false);
        $artistApplication = new ArtistApplication(
            $artistRepositoryMock,
            app($this->artistFactoryInterfaceName)
        );

        $entityIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $artistDXO = new ArtistDXO();
        $artistDXO->rollback($entityIdValue);
        $result = $artistApplication->rollback($artistDXO);
        $this->assertFalse($result);
    }

    /**
     * @expectedException App\Domain\Artist\ArtistException
     */
    public function testRollbackExceptionOccurred()
    {
        $artistApplication = app($this->artistApplicationInterfaceName);

        $entityIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $artistDXO = new ArtistDXO();
        $artistDXO->rollback($entityIdValue);
        $artistApplication->rollback($artistDXO);
    }

    public function testRollbackRollbackedEntityNotFound()
    {
        $artistRepositoryMock = $this->artistRepositoryMock();
        $artistRepositoryMock->shouldReceive('findProvision')->andReturn(null);
        $artistApplication = new ArtistApplication(
            $artistRepositoryMock,
            app($this->artistFactoryInterfaceName)
        );

        $entityIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $artistDXO = new ArtistDXO();
        $artistDXO->rollback($entityIdValue);
        $result = $artistApplication->rollback($artistDXO);
        $this->assertFalse($result);
    }

    public function testRollback()
    {
        $eventPublished = false;
        Event::shouldReceive('dispatch')->andReturnUsing(
            function ($event) use (&$eventPublished) {
                $eventName = 'App\Events\ArtistRollbacked';
                if ($event instanceof $eventName) {
                    $eventPublished = true;
                }
            }
        );
        $artistApplication = app($this->artistApplicationInterfaceName);

        $entityIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $artistDXO = new ArtistDXO();
        $artistDXO->rollback($entityIdValue);
        $result = $artistApplication->rollback($artistDXO);
        $this->assertTrue($result);
        $this->assertTrue($eventPublished);
    }

    public function testRefreshCachedEntityEmptyParameters()
    {
        $artistApplication = app($this->artistApplicationInterfaceName);

        $idValue = '';
        $artistDXO = new ArtistDXO();
        $artistDXO->refreshCachedEntity($idValue);
        $result = $artistApplication->refreshCachedEntity($artistDXO);
        $this->assertFalse($result);
    }

    public function testRefreshCachedEntity()
    {
        $artistApplication = app($this->artistApplicationInterfaceName);
        $artistRepository = app($this->artistRepositoryInterfaceName);
        $redisDAO = app($this->redisDAOInterfaceName);

        $idValue = '000010a1b2c3d4e5f6a7b8c9d';
        $artistDXO = new ArtistDXO();
        $artistDXO->refreshCachedEntity($idValue);
        $entityId = $artistDXO->getEntityId();
        $cacheKey = $artistRepository->cacheKeyById($entityId, ArtistEntity::class);
        $redisDAO->set($cacheKey, '1');

        $result = $artistApplication->refreshCachedEntity($artistDXO);
        $this->assertTrue($result);
        $cache = $redisDAO->get($cacheKey);
        $artistEntity = unserialize($cache);
        $this->assertEquals($artistEntity->id()->value(), $idValue);
    }

    public function testProvisionedEntitiesParametersEmpty()
    {
        $artistApplication = app($this->artistApplicationInterfaceName);

        $artistDXO = new ArtistDXO();
        $result = $artistApplication->provisionedEntities($artistDXO);
        $this->assertEquals($result, []);
    }

    public function testProvisionedEntities1()
    {
        $artistRepositoryMock = $this->artistRepositoryMock();
        $iTunesArtistIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $provisionedEntitiesCalled = false;
        $artistRepositoryMock->shouldReceive('provisionedEntities')->andReturnUsing(
            function ($iTunesArtistId, $artistName, $artistSpecification) use ($iTunesArtistIdValue, &$provisionedEntitiesCalled) {
                if ($iTunesArtistId->value() === $iTunesArtistIdValue) {
                    $provisionedEntitiesCalled = true;
                }
            }
        );
        $artistApplication = new ArtistApplication(
            $artistRepositoryMock,
            app($this->artistFactoryInterfaceName)
        );

        $artistDXO = new ArtistDXO();
        $artistDXO->provisionedEntities($iTunesArtistIdValue, null);
        $artistEntities = $artistApplication->provisionedEntities($artistDXO);
        $this->assertTrue($provisionedEntitiesCalled);
    }

    public function testProvisionedEntities2()
    {
        $artistRepositoryMock = $this->artistRepositoryMock();
        $artistNameValue = 'Lil Pump';
        $provisionedEntitiesCalled = false;
        $artistRepositoryMock->shouldReceive('provisionedEntities')->andReturnUsing(
            function ($iTunesArtistId, $artistName, $artistSpecification) use ($artistNameValue, &$provisionedEntitiesCalled) {
                if ($artistName->value() === $artistNameValue) {
                    $provisionedEntitiesCalled = true;
                }
            }
        );
        $artistApplication = new ArtistApplication(
            $artistRepositoryMock,
            app($this->artistFactoryInterfaceName)
        );

        $artistDXO = new ArtistDXO();
        $artistDXO->provisionedEntities(null, $artistNameValue);
        $artistEntities = $artistApplication->provisionedEntities($artistDXO);
        $this->assertTrue($provisionedEntitiesCalled);
    }

    public function testProvisionedEntities3()
    {
        $artistRepositoryMock = $this->artistRepositoryMock();
        $iTunesArtistIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $artistNameValue = 'Lil Pump';
        $provisionedEntitiesCalled = false;
        $artistRepositoryMock->shouldReceive('provisionedEntities')->andReturnUsing(
            function ($iTunesArtistId, $artistName, $artistSpecification) use ($iTunesArtistIdValue, $artistNameValue, &$provisionedEntitiesCalled) {
                if ($iTunesArtistId->value() === $iTunesArtistIdValue && $artistName->value() === $artistNameValue) {
                    $provisionedEntitiesCalled = true;
                }
            }
        );
        $artistApplication = new ArtistApplication(
            $artistRepositoryMock,
            app($this->artistFactoryInterfaceName)
        );

        $artistDXO = new ArtistDXO();
        $artistDXO->provisionedEntities($iTunesArtistIdValue, $artistNameValue);
        $artistEntities = $artistApplication->provisionedEntities($artistDXO);
        $this->assertTrue($provisionedEntitiesCalled);
    }

    public function testReleasedEntitiesParametersEmpty()
    {
        $artistApplication = app($this->artistApplicationInterfaceName);

        $artistDXO = new ArtistDXO();
        $result = $artistApplication->releasedEntities($artistDXO);
        $this->assertEquals($result, []);
    }

    public function testReleasedEntities1()
    {
        $artistRepositoryMock = $this->artistRepositoryMock();
        $iTunesArtistIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $releasedEntitiesCalled = false;
        $artistRepositoryMock->shouldReceive('releasedEntities')->andReturnUsing(
            function ($iTunesArtistId, $artistName, $artistSpecification) use ($iTunesArtistIdValue, &$releasedEntitiesCalled) {
                if ($iTunesArtistId->value() === $iTunesArtistIdValue) {
                    $releasedEntitiesCalled = true;
                }
            }
        );
        $artistApplication = new ArtistApplication(
            $artistRepositoryMock,
            app($this->artistFactoryInterfaceName)
        );

        $artistDXO = new ArtistDXO();
        $artistDXO->releasedEntities($iTunesArtistIdValue, null);
        $artistEntities = $artistApplication->releasedEntities($artistDXO);
        $this->assertTrue($releasedEntitiesCalled);
    }

    public function testReleasedEntities2()
    {
        $artistRepositoryMock = $this->artistRepositoryMock();
        $artistNameValue = 'Ed Sheeran';
        $releasedEntitiesCalled = false;
        $artistRepositoryMock->shouldReceive('releasedEntities')->andReturnUsing(
            function ($iTunesArtistId, $artistName, $artistSpecification) use ($artistNameValue, &$releasedEntitiesCalled) {
                if ($artistName->value() === $artistNameValue) {
                    $releasedEntitiesCalled = true;
                }
            }
        );
        $artistApplication = new ArtistApplication(
            $artistRepositoryMock,
            app($this->artistFactoryInterfaceName)
        );

        $artistDXO = new ArtistDXO();
        $artistDXO->releasedEntities(null, $artistNameValue);
        $artistEntities = $artistApplication->releasedEntities($artistDXO);
        $this->assertTrue($releasedEntitiesCalled);
    }

    public function testReleasedEntities3()
    {
        $artistRepositoryMock = $this->artistRepositoryMock();
        $iTunesArtistIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $artistNameValue = 'Ed Sheeran';
        $releasedEntitiesCalled = false;
        $artistRepositoryMock->shouldReceive('releasedEntities')->andReturnUsing(
            function ($iTunesArtistId, $artistName, $artistSpecification) use ($iTunesArtistIdValue, $artistNameValue, &$releasedEntitiesCalled) {
                if ($iTunesArtistId->value() === $iTunesArtistIdValue && $artistName->value() === $artistNameValue) {
                    $releasedEntitiesCalled = true;
                }
            }
        );
        $artistApplication = new ArtistApplication(
            $artistRepositoryMock,
            app($this->artistFactoryInterfaceName)
        );

        $artistDXO = new ArtistDXO();
        $artistDXO->releasedEntities($iTunesArtistIdValue, $artistNameValue);
        $artistEntities = $artistApplication->releasedEntities($artistDXO);
        $this->assertTrue($releasedEntitiesCalled);
    }

    public function testProvisionedPaginatorParametersEmpty()
    {
        $artistApplication = app($this->artistApplicationInterfaceName);

        $artistDXO = new ArtistDXO();
        $result = $artistApplication->provisionedPaginator($artistDXO);
        $this->assertNull($result);
    }

    public function testProvisionedPaginator1()
    {
        $artistRepositoryMock = $this->artistRepositoryMock();
        $iTunesArtistIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $provisionedPaginatorCalled = false;
        $artistRepositoryMock->shouldReceive('provisionedPaginator')->andReturnUsing(
            function ($iTunesArtistId, $artistName, $artistSpecification) use ($iTunesArtistIdValue, &$provisionedPaginatorCalled) {
                if ($iTunesArtistId->value() === $iTunesArtistIdValue) {
                    $provisionedPaginatorCalled = true;
                }
            }
        );
        $artistApplication = new ArtistApplication(
            $artistRepositoryMock,
            app($this->artistFactoryInterfaceName)
        );

        $artistDXO = new ArtistDXO();
        $artistDXO->provisionedPaginator($iTunesArtistIdValue, null);
        $artistEntities = $artistApplication->provisionedPaginator($artistDXO);
        $this->assertTrue($provisionedPaginatorCalled);
    }

    public function testProvisionedPaginator2()
    {
        $artistRepositoryMock = $this->artistRepositoryMock();
        $artistNameValue = 'Lil Pump';
        $provisionedPaginatorCalled = false;
        $artistRepositoryMock->shouldReceive('provisionedPaginator')->andReturnUsing(
            function ($iTunesArtistId, $artistName, $artistSpecification) use ($artistNameValue, &$provisionedPaginatorCalled) {
                if ($artistName->value() === $artistNameValue) {
                    $provisionedPaginatorCalled = true;
                }
            }
        );
        $artistApplication = new ArtistApplication(
            $artistRepositoryMock,
            app($this->artistFactoryInterfaceName)
        );

        $artistDXO = new ArtistDXO();
        $artistDXO->provisionedPaginator(null, $artistNameValue);
        $artistEntities = $artistApplication->provisionedPaginator($artistDXO);
        $this->assertTrue($provisionedPaginatorCalled);
    }

    public function testProvisionedPaginator3()
    {
        $artistRepositoryMock = $this->artistRepositoryMock();
        $iTunesArtistIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $artistNameValue = 'Lil Pump';
        $provisionedPaginatorCalled = false;
        $artistRepositoryMock->shouldReceive('provisionedPaginator')->andReturnUsing(
            function ($iTunesArtistId, $artistName, $artistSpecification) use ($iTunesArtistIdValue, $artistNameValue, &$provisionedPaginatorCalled) {
                if ($iTunesArtistId->value() === $iTunesArtistIdValue && $artistName->value() === $artistNameValue) {
                    $provisionedPaginatorCalled = true;
                }
            }
        );
        $artistApplication = new ArtistApplication(
            $artistRepositoryMock,
            app($this->artistFactoryInterfaceName)
        );

        $artistDXO = new ArtistDXO();
        $artistDXO->provisionedPaginator($iTunesArtistIdValue, $artistNameValue);
        $artistEntities = $artistApplication->provisionedPaginator($artistDXO);
        $this->assertTrue($provisionedPaginatorCalled);
    }

    public function testReleasedPaginatorParametersEmpty()
    {
        $artistApplication = app($this->artistApplicationInterfaceName);

        $artistDXO = new ArtistDXO();
        $result = $artistApplication->releasedPaginator($artistDXO);
        $this->assertNull($result);
    }

    public function testReleasedPaginator1()
    {
        $artistRepositoryMock = $this->artistRepositoryMock();
        $iTunesArtistIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $releasedPaginatorCalled = false;
        $artistRepositoryMock->shouldReceive('releasedPaginator')->andReturnUsing(
            function ($iTunesArtistId, $artistName, $artistSpecification) use ($iTunesArtistIdValue, &$releasedPaginatorCalled) {
                if ($iTunesArtistId->value() === $iTunesArtistIdValue) {
                    $releasedPaginatorCalled = true;
                }
            }
        );
        $artistApplication = new ArtistApplication(
            $artistRepositoryMock,
            app($this->artistFactoryInterfaceName)
        );

        $artistDXO = new ArtistDXO();
        $artistDXO->releasedPaginator($iTunesArtistIdValue, null);
        $artistEntities = $artistApplication->releasedPaginator($artistDXO);
        $this->assertTrue($releasedPaginatorCalled);
    }

    public function testReleasedPaginator2()
    {
        $artistRepositoryMock = $this->artistRepositoryMock();
        $artistNameValue = 'Ed Sheeran';
        $releasedPaginatorCalled = false;
        $artistRepositoryMock->shouldReceive('releasedPaginator')->andReturnUsing(
            function ($iTunesArtistId, $artistName, $artistSpecification) use ($artistNameValue, &$releasedPaginatorCalled) {
                if ($artistName->value() === $artistNameValue) {
                    $releasedPaginatorCalled = true;
                }
            }
        );
        $artistApplication = new ArtistApplication(
            $artistRepositoryMock,
            app($this->artistFactoryInterfaceName)
        );

        $artistDXO = new ArtistDXO();
        $artistDXO->releasedPaginator(null, $artistNameValue);
        $artistEntities = $artistApplication->releasedPaginator($artistDXO);
        $this->assertTrue($releasedPaginatorCalled);
    }

    public function testReleasedPaginator3()
    {
        $artistRepositoryMock = $this->artistRepositoryMock();
        $iTunesArtistIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $artistNameValue = 'Ed Sheeran';
        $releasedPaginatorCalled = false;
        $artistRepositoryMock->shouldReceive('releasedPaginator')->andReturnUsing(
            function ($iTunesArtistId, $artistName, $artistSpecification) use ($iTunesArtistIdValue, $artistNameValue, &$releasedPaginatorCalled) {
                if ($iTunesArtistId->value() === $iTunesArtistIdValue && $artistName->value() === $artistNameValue) {
                    $releasedPaginatorCalled = true;
                }
            }
        );
        $artistApplication = new ArtistApplication(
            $artistRepositoryMock,
            app($this->artistFactoryInterfaceName)
        );

        $artistDXO = new ArtistDXO();
        $artistDXO->releasedPaginator($iTunesArtistIdValue, $artistNameValue);
        $artistEntities = $artistApplication->releasedPaginator($artistDXO);
        $this->assertTrue($releasedPaginatorCalled);
    }

}
