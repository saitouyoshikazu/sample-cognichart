<?php

namespace Tests\Unit\Infrastructure\Repositories;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mockery;
use DB;
use App\Infrastructure\Eloquents\Artist;
use App\Infrastructure\Eloquents\ProvisionedArtist;
use App\Infrastructure\Repositories\ArtistRepository;
use App\Domain\EntityId;
use App\Domain\ValueObjects\ITunesArtistId;
use App\Domain\ValueObjects\ArtistName;
use App\Domain\Artist\ArtistBusinessId;
use App\Domain\Artist\ArtistSpecification;
use App\Domain\Artist\ArtistEntity;
use App\Domain\Artist\ArtistException;

class ArtistRepositoryTest extends TestCase
{

    use DatabaseMigrations;

    private $redisDAOInterfaceName = 'App\Infrastructure\RedisDAO\RedisDAOInterface';
    private $artistFactoryInterfaceName = 'App\Domain\Artist\ArtistFactoryInterface';
    private $artistRepositoryInterfaceName = 'App\Domain\Artist\ArtistRepositoryInterface';

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
        $artistRepository = app($this->artistRepositoryInterfaceName);
        $this->assertEquals(get_class($artistRepository), ArtistRepository::class);
    }

    public function testCreateId()
    {
        $artistRepository = app($this->artistRepositoryInterfaceName);

        $id = $artistRepository->createId();
        $this->assertEquals(strlen($id->value()), 32);
    }

    public function testFindProvision()
    {
        $artistRepository = app($this->artistRepositoryInterfaceName);

        $idValue = '000010a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($idValue);
        $result = $artistRepository->findProvision($entityId);
        $this->assertNull($result);

        $idValue = '000050a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($idValue);
        $result = $artistRepository->findProvision($entityId);
        $this->assertEquals($result->id()->value(), $idValue);
    }

    public function testFindRelease()
    {
        $artistRepository = app($this->artistRepositoryInterfaceName);

        $idValue = '000050a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($idValue);
        $result = $artistRepository->findRelease($entityId);
        $this->assertNull($result);

        $idValue = '000010a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($idValue);
        $result = $artistRepository->findRelease($entityId);
        $this->assertEquals($result->id()->value(), $idValue);
    }

    public function testGetProvisione()
    {
        $artistRepository = app($this->artistRepositoryInterfaceName);

        $iTunesArtistIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistId = new ITunesArtistId($iTunesArtistIdValue);
        $artistBusinessId = new ArtistBusinessId($iTunesArtistId);
        $result = $artistRepository->getProvision($artistBusinessId);
        $this->assertNull($result);

        $iTunesArtistIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistId = new ITunesArtistId($iTunesArtistIdValue);
        $artistBusinessId = new ArtistBusinessId($iTunesArtistId);
        $result = $artistRepository->getProvision($artistBusinessId);
        $this->assertEquals($result->iTunesArtistId()->value(), $iTunesArtistIdValue);

        $iTunesArtistIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistId = new ITunesArtistId($iTunesArtistIdValue);
        $artistBusinessId = new ArtistBusinessId($iTunesArtistId);
        $excludeIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($excludeIdValue);
        $result = $artistRepository->getProvision($artistBusinessId, $entityId);
        $this->assertNull($result);
    }

    public function testGetRelease()
    {
        $artistRepository = app($this->artistRepositoryInterfaceName);

        $iTunesArtistIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistId = new ITunesArtistId($iTunesArtistIdValue);
        $artistBusinessId = new ArtistBusinessId($iTunesArtistId);
        $result = $artistRepository->getRelease($artistBusinessId);
        $this->assertNull($result);

        $iTunesArtistIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistId = new ITunesArtistId($iTunesArtistIdValue);
        $artistBusinessId = new ArtistBusinessId($iTunesArtistId);
        $result = $artistRepository->getRelease($artistBusinessId);
        $this->assertEquals($result->iTunesArtistId()->value(), $iTunesArtistIdValue);

        $iTunesArtistIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistId = new ITunesArtistId($iTunesArtistIdValue);
        $artistBusinessId = new ArtistBusinessId($iTunesArtistId);
        $excludeIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($excludeIdValue);
        $result = $artistRepository->getRelease($artistBusinessId, $entityId);
        $this->assertNull($result);
    }

    public function testFindWithCache()
    {
        $artistRepository = app($this->artistRepositoryInterfaceName);
        $redisDAO = app($this->redisDAOInterfaceName);
        $artistSpecification = new ArtistSpecification();

        $idValue = '000050a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($idValue);
        $result = $artistRepository->findWithCache($entityId, $artistSpecification);
        $this->assertNull($result);

        $redisDAO->clear('*');
        $redisDAO->resetIsCache();

        $idValue = '000010a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($idValue);
        $result = $artistRepository->findWithCache($entityId, $artistSpecification);
        $this->assertEquals($result->id()->value(), $idValue);
        $this->assertFalse($redisDAO->isCache());
        $result = $artistRepository->findWithCache($entityId, $artistSpecification);
        $this->assertTrue($redisDAO->isCache());
    }

    public function testRefreshCachedEntity()
    {
        $artistRepository = app($this->artistRepositoryInterfaceName);
        $redisDAO = app($this->redisDAOInterfaceName);
        $artistSpecification = new ArtistSpecification();

        $entityIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($entityIdValue);
        $cacheKey = $artistRepository->cacheKeyById($entityId, ArtistEntity::class);
        $redisDAO->set($cacheKey, '1');
        $artistRepository->refreshCachedEntity($entityId, $artistSpecification);
        $cache = $redisDAO->get($cacheKey);
        $this->assertNull($cache);

        $entityIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($entityIdValue);
        $cacheKey = $artistRepository->cacheKeyById($entityId, ArtistEntity::class);
        $redisDAO->set($cacheKey, '1');
        $artistRepository->refreshCachedEntity($entityId, $artistSpecification);
        $cache = $redisDAO->get($cacheKey);
        $artistEntity = unserialize($cache);
        $this->assertEquals($artistEntity->id()->value(), $entityIdValue);
    }

    public function testRegisterProvisionedArtistAlreadyExist()
    {
        $artistFactory = app($this->artistFactoryInterfaceName);
        $artistRepository = app($this->artistRepositoryInterfaceName);
        $artistSpecification = new ArtistSpecification();

        $idValue = '000050a1b2c3d4e5f6a7b8c9d';
        $artistNameValue = 'Halsey';
        $iTunesArtistIdValue = '000090a1b2c3d4e5f6a7b8c9d';
        $artistEntity = $artistFactory->create($idValue, $iTunesArtistIdValue, $artistNameValue);
        $validated = false;
        try {
            $artistRepository->register($artistEntity, $artistSpecification);
        } catch (ArtistException $e) {
            if ($e->getMessage() === "Couldn't register to provision ArtistEntity because provisioned Artist is already existing.") {
                $validated = true;
            }
        }
        $this->assertTrue($validated);

        $idValue = '000090a1b2c3d4e5f6a7b8c9d';
        $artistNameValue = 'Halsey';
        $iTunesArtistIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $artistEntity = $artistFactory->create($idValue, $iTunesArtistIdValue, $artistNameValue);
        $validated = false;
        try {
            $artistRepository->register($artistEntity, $artistSpecification);
        } catch (ArtistException $e) {
            if ($e->getMessage() === "Couldn't register to provision ArtistEntity because provisioned Artist is already existing.") {
                $validated = true;
            }
        }
        $this->assertTrue($validated);
    }

    public function testRegisterReleasedArtistAlreadyExist()
    {
        $artistFactory = app($this->artistFactoryInterfaceName);
        $artistRepository = app($this->artistRepositoryInterfaceName);
        $artistSpecification = new ArtistSpecification();

        $idValue = '000010a1b2c3d4e5f6a7b8c9d';
        $artistNameValue = 'Halsey';
        $iTunesArtistIdValue = '000090a1b2c3d4e5f6a7b8c9d';
        $artistEntity = $artistFactory->create($idValue, $iTunesArtistIdValue, $artistNameValue);
        $validated = false;
        try {
            $artistRepository->register($artistEntity, $artistSpecification);
        } catch (ArtistException $e) {
            if ($e->getMessage() === "Couldn't register to provision ArtistEntity because released Artist is already existing.") {
                $validated = true;
            }
        }
        $this->assertTrue($validated);

        $idValue = '000090a1b2c3d4e5f6a7b8c9d';
        $artistNameValue = 'Halsey';
        $iTunesArtistIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $artistEntity = $artistFactory->create($idValue, $iTunesArtistIdValue, $artistNameValue);
        $validated = false;
        try {
            $artistRepository->register($artistEntity, $artistSpecification);
        } catch (ArtistException $e) {
            if ($e->getMessage() === "Couldn't register to provision ArtistEntity because released Artist is already existing.") {
                $validated = true;
            }
        }
        $this->assertTrue($validated);
    }

    public function testRegister()
    {
        $artistFactory = app($this->artistFactoryInterfaceName);
        $artistRepository = app($this->artistRepositoryInterfaceName);
        $artistSpecification = new ArtistSpecification();

        $idValue = $artistRepository->createId()->value();
        $artistNameValue = 'Halsey';
        $iTunesArtistIdValue = '000090a1b2c3d4e5f6a7b8c9d';
        $artistEntity = $artistFactory->create($idValue, $iTunesArtistIdValue, $artistNameValue);
        $result = $artistRepository->register($artistEntity, $artistSpecification);
        $this->assertTrue($result);
    }

    public function testModifyProvisionReleasedAlreadyExist()
    {
        $artistFactory = app($this->artistFactoryInterfaceName);
        $artistRepository = app($this->artistRepositoryInterfaceName);
        $artistSpecification = new ArtistSpecification();

        $idValue = '000050a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistIdValue = '000090a1b2c3d4e5f6a7b8c9d';
        $artistNameValue = 'Halsey';
        $parameters = [
            'id'                =>  $idValue,
            'itunes_artist_id'  =>  $iTunesArtistIdValue,
            'artist_name'       =>  $artistNameValue
        ];
        $artist = new Artist();
        $artist->fill($parameters)->save();
        $entityId = new EntityId($idValue);
        $modifiedEntity = $artistRepository->findProvision($entityId);
        $artistNameValue = 'Lil Pump+';
        $artistName = new ArtistName($artistNameValue);
        $modifiedEntity->setArtistName($artistName);
        $validated = false;
        try {
            $artistRepository->modifyProvision($modifiedEntity, $artistSpecification);
        } catch (ArtistException $e) {
            if ($e->getMessage() === "Couldn't modify provisioned ArtistEntity because released Artist is already existing.") {
                $validated = true;
            }
        }
        $this->assertTrue($validated);
        Artist::destroy($idValue);

        $idValue = '000050a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($idValue);
        $modifiedEntity = $artistRepository->findProvision($entityId);
        $iTunesArtistIdValue = '000040a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistId = new ITunesArtistId($iTunesArtistIdValue);
        $modifiedEntity->setITunesArtistId($iTunesArtistId);
        $validated = false;
        try {
            $artistRepository->modifyProvision($modifiedEntity, $artistSpecification);
        } catch (ArtistException $e) {
            if ($e->getMessage() === "Couldn't modify provisioned ArtistEntity because released Artist is already existing.") {
                $validated = true;
            }
        }
        $this->assertTrue($validated);
    }

    public function testModifyProvisionProvisioedAlreadyExist()
    {
        $artistRepository = app($this->artistRepositoryInterfaceName);
        $artistSpecification = new ArtistSpecification();

        $idValue = '000050a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($idValue);
        $modifiedEntity = $artistRepository->findProvision($entityId);
        $iTunesArtistIdValue = '000060a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistId = new ITunesArtistId($iTunesArtistIdValue);
        $modifiedEntity->setITunesArtistId($iTunesArtistId);
        $validated = false;
        try {
            $artistRepository->modifyProvision($modifiedEntity, $artistSpecification);
        } catch (ArtistException $e) {
            if ($e->getMessage() === "Couldn't modify provisioned ArtistEntity because provisioned Artist is already existing.") {
                $validated = true;
            }
        }
        $this->assertTrue($validated);
    }

    public function testModifyProvisionProvisioedDoesNotExist()
    {
        $artistFactory = app($this->artistFactoryInterfaceName);
        $artistRepository = app($this->artistRepositoryInterfaceName);
        $artistSpecification = new ArtistSpecification();

        $idValue = '000090a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistIdValue = '000090a1b2c3d4e5f6a7b8c9d';
        $artistNameValue = 'Halsey';
        $modifiedEntity = $artistFactory->create($idValue, $iTunesArtistIdValue, $artistNameValue);
        $validated = false;
        try {
            $artistRepository->modifyProvision($modifiedEntity, $artistSpecification);
        } catch (ArtistException $e) {
            if ($e->getMessage() === "Couldn't modify provisioned ArtistEntity because provisioned Artist doesn't exist.") {
                $validated = true;
            }
        }
        $this->assertTrue($validated);
    }

    public function testModifyProvision()
    {
        $artistRepository = app($this->artistRepositoryInterfaceName);
        $artistSpecification = new ArtistSpecification();

        $idValue = '000050a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($idValue);
        $modifiedEntity = $artistRepository->findProvision($entityId);
        $artistNameValue = 'Halsey';
        $artistName = new ArtistName($artistNameValue);
        $modifiedEntity->setArtistName($artistName);
        $result = $artistRepository->modifyProvision($modifiedEntity, $artistSpecification);
        $this->assertTrue($result);
        $artistEntity = $artistRepository->findProvision($entityId);
        $this->assertEquals($artistEntity->artistName()->value(), $artistNameValue);
    }

    public function testDeleteProvisionedDoesNotExist()
    {
        $artistRepository = app($this->artistRepositoryInterfaceName);
        $artistSpecification = new ArtistSpecification();

        $idValue = '000090a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($idValue);
        $validated = false;
        try {
            $artistRepository->delete($entityId, $artistSpecification);
        } catch (ArtistException $e) {
            if ($e->getMessage() === "Couldn't delete provisioned ArtistEntity because provisioned Artist doesn't exist.") {
                $validated = true;
            }
        }
        $this->assertTrue($validated);
    }

    public function testDelete()
    {
        $artistRepository = app($this->artistRepositoryInterfaceName);
        $artistSpecification = new ArtistSpecification();

        $idValue = '000050a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($idValue);
        $result = $artistRepository->delete($entityId, $artistSpecification);
        $this->assertTrue($result);

        $artistEntity = $artistRepository->findProvision($entityId);
        $this->assertNull($artistEntity);
    }

    public function testReleaseReleasedAlreadyExist()
    {
        $artistRepository = app($this->artistRepositoryInterfaceName);
        $artistSpecification = new ArtistSpecification();

        $idValue = '000050a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistIdValue = '000090a1b2c3d4e5f6a7b8c9d';
        $artistNameValue = 'Lil Pump';
        $parameters = [
            'id'                =>  $idValue,
            'itunes_artist_id'  =>  $iTunesArtistIdValue,
            'artist_name'       =>  $artistNameValue,
        ];
        $artist = new Artist();
        $artist->fill($parameters)->save();
        $entityId = new EntityId($idValue);
        $validated = false;
        try {
            $artistRepository->release($entityId, $artistSpecification);
        } catch (ArtistException $e) {
            if ($e->getMessage() === "Couldn't release provisioned ArtistEntity because released Artist is already existing.") {
                $validated = true;
            }
        }
        $this->assertTrue($validated);
        Artist::destroy($idValue);

        $idValue = '000090a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $artistNameValue = 'Halsey';
        $parameters = [
            'id'                =>  $idValue,
            'itunes_artist_id'  =>  $iTunesArtistIdValue,
            'artist_name'       =>  $artistNameValue,
        ];
        $artist = new Artist();
        $artist->fill($parameters)->save();
        $entityId = new EntityId($idValue);
        $validated = false;
        try {
            $artistRepository->release($entityId, $artistSpecification);
        } catch (ArtistException $e) {
            if ($e->getMessage() === "Couldn't release provisioned ArtistEntity because released Artist is already existing.") {
                $validated = true;
            }
        }
        $this->assertTrue($validated);
        Artist::destroy($idValue);
    }

    public function testReleaseProvisionedDoesNotExist()
    {
        $artistRepository = app($this->artistRepositoryInterfaceName);
        $artistSpecification = new ArtistSpecification();

        $idValue = '000090a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($idValue);
        $validated = false;
        try {
            $artistRepository->release($entityId, $artistSpecification);
        } catch (ArtistException $e) {
            if ($e->getMessage() === "Couldn't release provisioned ArtistEntity because provisioned Artist doesn't exist.") {
                $validated = true;
            }
        }
        $this->assertTrue($validated);
    }

    public function testReleaseProvisionedAlreadyExist()
    {
        $artistRepository = app($this->artistRepositoryInterfaceName);
        $artistSpecification = new ArtistSpecification();

        $idValue = '000050a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistIdValue = '000060a1b2c3d4e5f6a7b8c9d';
        $parameters = [
            'itunes_artist_id'  =>  $iTunesArtistIdValue,
        ];
        ProvisionedArtist::find($idValue)->fill($parameters)->save();
        $entityId = new EntityId($idValue);
        $validated = false;
        try {
            $artistRepository->release($entityId, $artistSpecification);
        } catch (ArtistException $e) {
            if ($e->getMessage() === "Couldn't release provisioned ArtistEntity because same provisioned Artist is already existing.") {
                $validated = true;
            }
        }
        $this->assertTrue($validated);
    }

    public function testRelease()
    {
        $artistRepository = app($this->artistRepositoryInterfaceName);
        $artistSpecification = new ArtistSpecification();

        $idValue = '000050a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($idValue);
        $result = $artistRepository->release($entityId, $artistSpecification);
        $this->assertTrue($result);
        $artistEntity = $artistRepository->findRelease($entityId);
        $this->assertEquals($artistEntity->id()->value(), $idValue);
        $artistEntity = $artistRepository->findProvision($entityId);
        $this->assertNull($artistEntity);
    }

    public function testModifyReleaseReleasedAlreadyExist()
    {
        $artistRepository = app($this->artistRepositoryInterfaceName);
        $artistSpecification = new ArtistSpecification();

        $idValue = '000010a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistIdValue = '000020a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($idValue);
        $modifiedEntity = $artistRepository->findRelease($entityId);
        $iTunesArtistId = new ITunesArtistId($iTunesArtistIdValue);
        $modifiedEntity->setITunesArtistId($iTunesArtistId);
        $validated = false;
        try {
            $artistRepository->modifyRelease($modifiedEntity, $artistSpecification);
        } catch (ArtistException $e) {
            if ($e->getMessage() === "Couldn't modify released ArtistEntity because released Artist is already existing.") {
                $validated = true;
            }
        }
        $this->assertTrue($validated);
    }

    public function testModifyReleaseReleasedDoesNotExist()
    {
        $artistFactory = app($this->artistFactoryInterfaceName);
        $artistRepository = app($this->artistRepositoryInterfaceName);
        $artistSpecification = new ArtistSpecification();

        $idValue = '000090a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistIdValue = '000090a1b2c3d4e5f6a7b8c9d';
        $artistNameValue = 'Halsey';
        $artistEntity = $artistFactory->create($idValue, $iTunesArtistIdValue, $artistNameValue);
        $validated = false;
        try {
            $artistRepository->modifyRelease($artistEntity, $artistSpecification);
        } catch (ArtistException $e) {
            if ($e->getMessage() === "Couldn't modify released ArtistEntity because released Artist doesn't exist.") {
                $validated = true;
            }
        }
        $this->assertTrue($validated);
    }

    public function testModifyReleaseProvisionedAlreadyExist()
    {
        $artistRepository = app($this->artistRepositoryInterfaceName);
        $artistSpecification = new ArtistSpecification();

        $idValue = '000010a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistIdValue = '000090a1b2c3d4e5f6a7b8c9d';
        $artistNameValue = 'Halsey';
        $parameters = [
            'id'                =>  $idValue,
            'itunes_artist_id'  =>  $iTunesArtistIdValue,
            'artist_name'       =>  $artistNameValue
        ];
        $provisionedArtist = new ProvisionedArtist();
        $provisionedArtist->fill($parameters)->save();
        $entityId = new EntityId($idValue);
        $modifiedEntity = $artistRepository->findRelease($entityId);
        $artistNameValue = 'Ed Sheeran+';
        $artistName = new ArtistName($artistNameValue);
        $modifiedEntity->setArtistName($artistName);
        $validated = false;
        try {
            $artistRepository->modifyRelease($modifiedEntity, $artistSpecification);
        } catch (ArtistException $e) {
            if ($e->getMessage() === "Couldn't modify released ArtistEntity because provisioned Artist is already existing.") {
                $validated = true;
            }
        }
        $this->assertTrue($validated);
        ProvisionedArtist::destroy($idValue);

        $idValue = '000010a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($idValue);
        $modifiedEntity = $artistRepository->findRelease($entityId);
        $iTunesArtistIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistId = new ITunesArtistId($iTunesArtistIdValue);
        $modifiedEntity->setITunesArtistId($iTunesArtistId);
        $validated = false;
        try {
            $artistRepository->modifyRelease($modifiedEntity, $artistSpecification);
        } catch (ArtistException $e) {
            if ($e->getMessage() === "Couldn't modify released ArtistEntity because provisioned Artist is already existing.") {
                $validated = true;
            }
        }
        $this->assertTrue($validated);
    }

    public function testModifyRelease()
    {
        $artistRepository = app($this->artistRepositoryInterfaceName);
        $artistSpecification = new ArtistSpecification();

        $idValue = '000010a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($idValue);
        $modifiedEntity = $artistRepository->findRelease($entityId);
        $artistNameValue = 'Ed Sheeran+';
        $artistName = new ArtistName($artistNameValue);
        $modifiedEntity->setArtistName($artistName);
        $result = $artistRepository->modifyRelease($modifiedEntity, $artistSpecification);
        $this->assertTrue($result);
        $artistEntity = $artistRepository->findRelease($entityId);
        $this->assertEquals($artistEntity->artistName()->value(), $artistNameValue);
    }

    public function testRollbackProvisionedAlreadyExist()
    {
        $artistRepository = app($this->artistRepositoryInterfaceName);
        $artistSpecification = new ArtistSpecification();

        $idValue = '000010a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistIdValue = '000090a1b2c3d4e5f6a7b8c9d';
        $artistNameValue = 'Halsey';
        $parameters = [
            'id'                =>  $idValue,
            'itunes_artist_id'  =>  $iTunesArtistIdValue,
            'artist_name'       =>  $artistNameValue
        ];
        $provisionedArtist = new ProvisionedArtist();
        $provisionedArtist->fill($parameters)->save();
        $entityId = new EntityId($idValue);
        $validated = false;
        try {
            $artistRepository->rollback($entityId, $artistSpecification);
        } catch (ArtistException $e) {
            if ($e->getMessage() === "Couldn't rollback ArtistEntity because provisioned Artist is already existing.") {
                $validated = true;
            }
        }
        $this->assertTrue($validated);
        ProvisionedArtist::destroy($idValue);

        $idValue = '000090a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $artistNameValue = 'Halsey';
        $parameters = [
            'id'                =>  $idValue,
            'itunes_artist_id'  =>  $iTunesArtistIdValue,
            'artist_name'       =>  $artistNameValue,
        ];
        $provisionedArtist = new ProvisionedArtist();
        $provisionedArtist->fill($parameters)->save();
        $idValue = '000010a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($idValue);
        $validated = false;
        try {
            $artistRepository->rollback($entityId, $artistSpecification);
        } catch (ArtistException $e) {
            if ($e->getMessage() === "Couldn't rollback ArtistEntity because provisioned Artist is already existing.") {
                $validated = true;
            }
        }
        $this->assertTrue($validated);
        ProvisionedArtist::destroy('000090a1b2c3d4e5f6a7b8c9d');
    }

    public function testRollbackReleasedDoesNotExist()
    {
        $artistRepository = app($this->artistRepositoryInterfaceName);
        $artistSpecification = new ArtistSpecification();

        $idValue = '000090a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($idValue);
        $validated = false;
        try {
            $artistRepository->rollback($entityId, $artistSpecification);
        } catch (ArtistException $e) {
            if ($e->getMessage() === "Couldn't rollback ArtistEntity because released Artist doesn't exist.") {
                $validated = true;
            }
        }
        $this->assertTrue($validated);
    }

    public function testRollbackReleasedAlreadyExist()
    {
        $artistRepository = app($this->artistRepositoryInterfaceName);
        $artistSpecification = new ArtistSpecification();

        $parameters = [
            'id'                =>  '000090a1b2c3d4e5f6a7b8c9d',
            'itunes_artist_id'  =>  '000010a1b2c3d4e5f6a7b8c9d',
            'artist_name'       =>  'Halsey'
        ];
        $artist = new Artist();
        $artist->fill($parameters)->save();

        $idValue = '000010a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($idValue);
        $validated = false;
        try {
            $artistRepository->rollback($entityId, $artistSpecification);
        } catch (ArtistException $e) {
            if ($e->getMessage() === "Couldn't rollback ArtistEntity because same released Artist is existing.") {
                $validated = true;
            }
        }
        $this->assertTrue($validated);
        Artist::destroy('000090a1b2c3d4e5f6a7b8c9d');
    }

    public function testRollback()
    {
        $artistRepository = app($this->artistRepositoryInterfaceName);
        $artistSpecification = new ArtistSpecification();

        $idValue = '000010a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($idValue);
        $result = $artistRepository->rollback($entityId, $artistSpecification);
        $this->assertTrue($result);
        $artistEntity = $artistRepository->findRelease($entityId);
        $this->assertNull($artistEntity);
        $artistEntity = $artistRepository->findProvision($entityId);
        $this->assertEquals($artistEntity->id()->value(), $idValue);
    }

    public function testProvisionedEntitiesDoesNotMatch()
    {
        $artistRepository = app($this->artistRepositoryInterfaceName);

        $iTunesArtistIdValue = '000060a1b2c3d4e5f6a7b8c9d';
        $artistNameValue = 'Lil Pump';
        $iTunesArtistId = new ITunesArtistId($iTunesArtistIdValue);
        $artistName = new ArtistName($artistNameValue);
        $artistEntities = $artistRepository->provisionedEntities($iTunesArtistId, $artistName, new ArtistSpecification());
        $this->assertEquals($artistEntities, []);
    }

    public function testProvisionedEntities1()
    {
        $artistRepository = app($this->artistRepositoryInterfaceName);

        $verify = [
            '000050a1b2c3d4e5f6a7b8c9d' => true,
            '000060a1b2c3d4e5f6a7b8c9d' => true,
            '000070a1b2c3d4e5f6a7b8c9d' => true,
            '000080a1b2c3d4e5f6a7b8c9d' => true,
        ];
        $artistEntities = $artistRepository->provisionedEntities(null, null, new ArtistSpecification());
        $artistIds = [];
        foreach ($artistEntities AS $artistEntity) {
            $artistIdValue = $artistEntity->id()->value();
            if (isset($verify[$artistIdValue])) {
                $artistIds[$artistIdValue] = true;
            }
        }
        sort($verify);
        sort($artistIds);
        $this->assertEquals($verify, $artistIds);
        $this->assertEquals(count($artistEntities), 10);
    }

    public function testProvisionedEntities2()
    {
        $artistRepository = app($this->artistRepositoryInterfaceName);

        $verify = ['000050a1b2c3d4e5f6a7b8c9d'];
        $iTunesArtistIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistId = new ITunesArtistId($iTunesArtistIdValue);
        $artistEntities = $artistRepository->provisionedEntities($iTunesArtistId, null, new ArtistSpecification());
        $artistIds = [];
        foreach ($artistEntities AS $artistEntity) {
            $artistIds[] = $artistEntity->id()->value();
        }
        sort($verify);
        sort($artistIds);
        $this->assertEquals($verify, $artistIds);
    }

    public function testProvisionedEntities3()
    {
        $artistRepository = app($this->artistRepositoryInterfaceName);

        $verify = ['000050a1b2c3d4e5f6a7b8c9d'];
        $artistNameValue = 'Pump';
        $artistName = new ArtistName($artistNameValue);
        $artistEntities = $artistRepository->provisionedEntities(null, $artistName, new ArtistSpecification());
        $artistIds = [];
        foreach ($artistEntities AS $artistEntity) {
            $artistIds[] = $artistEntity->id()->value();
        }
        sort($verify);
        sort($artistIds);
        $this->assertEquals($verify, $artistIds);
    }

    public function testProvisionedEntities4()
    {
        $artistRepository = app($this->artistRepositoryInterfaceName);

        $verify = ['000050a1b2c3d4e5f6a7b8c9d'];
        $iTunesArtistIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $artistNameValue = 'Pump';
        $iTunesArtistId = new ITunesArtistId($iTunesArtistIdValue);
        $artistName = new ArtistName($artistNameValue);
        $artistEntities = $artistRepository->provisionedEntities($iTunesArtistId, $artistName, new ArtistSpecification());
        $artistIds = [];
        foreach ($artistEntities AS $artistEntity) {
            $artistIds[] = $artistEntity->id()->value();
        }
        sort($verify);
        sort($artistIds);
        $this->assertEquals($verify, $artistIds);
    }

    public function testReleasedEntitiesDoesNotMatch()
    {
        $artistRepository = app($this->artistRepositoryInterfaceName);

        $iTunesArtistIdValue = '000020a1b2c3d4e5f6a7b8c9d';
        $artistNameValue = 'Ed Sheeran';
        $iTunesArtistId = new ITunesArtistId($iTunesArtistIdValue);
        $artistName = new ArtistName($artistNameValue);
        $artistEntities = $artistRepository->releasedEntities($iTunesArtistId, $artistName, new ArtistSpecification());
        $this->assertEquals($artistEntities, []);
    }

    public function testReleasedEntities1()
    {
        $artistRepository = app($this->artistRepositoryInterfaceName);

        $verify = [
            '000010a1b2c3d4e5f6a7b8c9d' => true,
            '000020a1b2c3d4e5f6a7b8c9d' => true,
            '000030a1b2c3d4e5f6a7b8c9d' => true,
            '000040a1b2c3d4e5f6a7b8c9d' => true,
        ];
        $artistEntities = $artistRepository->releasedEntities(null, null, new ArtistSpecification());
        $artistIds = [];
        foreach ($artistEntities AS $artistEntity) {
            $artistIdValue = $artistEntity->id()->value();
            if (isset($verify[$artistIdValue])) {
                $artistIds[$artistIdValue] = true;
            }
        }
        sort($verify);
        sort($artistIds);
        $this->assertEquals($verify, $artistIds);
        $this->assertEquals(count($artistEntities), 10);
    }

    public function testReleasedEntities2()
    {
        $artistRepository = app($this->artistRepositoryInterfaceName);

        $verify = ['000010a1b2c3d4e5f6a7b8c9d'];
        $iTunesArtistIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistId = new ITunesArtistId($iTunesArtistIdValue);
        $artistEntities = $artistRepository->releasedEntities($iTunesArtistId, null, new ArtistSpecification());
        $artistIds = [];
        foreach ($artistEntities AS $artistEntity) {
            $artistIds[] = $artistEntity->id()->value();
        }
        sort($verify);
        sort($artistIds);
        $this->assertEquals($verify, $artistIds);
    }

    public function testReleasedEntities3()
    {
        $artistRepository = app($this->artistRepositoryInterfaceName);

        $verify = ['000010a1b2c3d4e5f6a7b8c9d'];
        $artistNameValue = 'Ed';
        $artistName = new ArtistName($artistNameValue);
        $artistEntities = $artistRepository->releasedEntities(null, $artistName, new ArtistSpecification());
        $artistIds = [];
        foreach ($artistEntities AS $artistEntity) {
            $artistIds[] = $artistEntity->id()->value();
        }
        sort($verify);
        sort($artistIds);
        $this->assertEquals($verify, $artistIds);
    }

    public function testReleasedEntities4()
    {
        $artistRepository = app($this->artistRepositoryInterfaceName);

        $verify = ['000010a1b2c3d4e5f6a7b8c9d'];
        $iTunesArtistIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $artistNameValue = 'Ed';
        $iTunesArtistId = new ITunesArtistId($iTunesArtistIdValue);
        $artistName = new ArtistName($artistNameValue);
        $artistEntities = $artistRepository->releasedEntities($iTunesArtistId, $artistName, new ArtistSpecification());
        $artistIds = [];
        foreach ($artistEntities AS $artistEntity) {
            $artistIds[] = $artistEntity->id()->value();
        }
        sort($verify);
        sort($artistIds);
        $this->assertEquals($verify, $artistIds);
    }

    public function testProvisionedPaginatorDoesNotMatch()
    {
        $artistRepository = app($this->artistRepositoryInterfaceName);

        $iTunesArtistIdValue = '000060a1b2c3d4e5f6a7b8c9d';
        $artistNameValue = 'Lil Pump';
        $iTunesArtistId = new ITunesArtistId($iTunesArtistIdValue);
        $artistName = new ArtistName($artistNameValue);
        $domainPaginator = $artistRepository->provisionedPaginator($iTunesArtistId, $artistName, new ArtistSpecification());
        $this->assertEquals($domainPaginator->getEntities(), []);
        $this->assertEquals($domainPaginator->getPaginator()->total(), 0);
    }

    public function testProvisionedPaginator1()
    {
        $artistRepository = app($this->artistRepositoryInterfaceName);

        $verify = [
            '000050a1b2c3d4e5f6a7b8c9d' => true,
            '000060a1b2c3d4e5f6a7b8c9d' => true,
            '000070a1b2c3d4e5f6a7b8c9d' => true,
            '000080a1b2c3d4e5f6a7b8c9d' => true,
        ];
        $domainPaginator = $artistRepository->provisionedPaginator(null, null, new ArtistSpecification());
        $artistEntities = $domainPaginator->getEntities();
        $artistIds = [];
        foreach ($artistEntities AS $artistEntity) {
            $artistIdValue = $artistEntity->id()->value();
            if (isset($verify[$artistIdValue])) {
                $artistIds[$artistIdValue] = true;
            }
        }
        sort($verify);
        sort($artistIds);
        $this->assertEquals($verify, $artistIds);
        $this->assertEquals($domainPaginator->getPaginator()->total(), 10);
    }

    public function testProvisionedPaginator2()
    {
        $artistRepository = app($this->artistRepositoryInterfaceName);

        $verify = ['000050a1b2c3d4e5f6a7b8c9d'];
        $iTunesArtistIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistId = new ITunesArtistId($iTunesArtistIdValue);
        $domainPaginator = $artistRepository->provisionedPaginator($iTunesArtistId, null, new ArtistSpecification());
        $artistEntities = $domainPaginator->getEntities();
        $artistIds = [];
        foreach ($artistEntities AS $artistEntity) {
            $artistIds[] = $artistEntity->id()->value();
        }
        sort($verify);
        sort($artistIds);
        $this->assertEquals($verify, $artistIds);
        $this->assertEquals($domainPaginator->getPaginator()->total(), 1);
    }

    public function testProvisionedPaginator3()
    {
        $artistRepository = app($this->artistRepositoryInterfaceName);

        $verify = ['000050a1b2c3d4e5f6a7b8c9d'];
        $artistNameValue = 'Pump';
        $artistName = new ArtistName($artistNameValue);
        $domainPaginator = $artistRepository->provisionedPaginator(null, $artistName, new ArtistSpecification());
        $artistEntities = $domainPaginator->getEntities();
        $artistIds = [];
        foreach ($artistEntities AS $artistEntity) {
            $artistIds[] = $artistEntity->id()->value();
        }
        sort($verify);
        sort($artistIds);
        $this->assertEquals($verify, $artistIds);
        $this->assertEquals($domainPaginator->getPaginator()->total(), 1);
    }

    public function testProvisionedPaginator4()
    {
        $artistRepository = app($this->artistRepositoryInterfaceName);

        $verify = ['000050a1b2c3d4e5f6a7b8c9d'];
        $iTunesArtistIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $artistNameValue = 'Pump';
        $iTunesArtistId = new ITunesArtistId($iTunesArtistIdValue);
        $artistName = new ArtistName($artistNameValue);
        $domainPaginator = $artistRepository->provisionedPaginator($iTunesArtistId, $artistName, new ArtistSpecification());
        $artistEntities = $domainPaginator->getEntities();
        $artistIds = [];
        foreach ($artistEntities AS $artistEntity) {
            $artistIds[] = $artistEntity->id()->value();
        }
        sort($verify);
        sort($artistIds);
        $this->assertEquals($verify, $artistIds);
        $this->assertEquals($domainPaginator->getPaginator()->total(), 1);
    }

    public function testReleasedPaginatorDoesNotMatch()
    {
        $artistRepository = app($this->artistRepositoryInterfaceName);

        $iTunesArtistIdValue = '000020a1b2c3d4e5f6a7b8c9d';
        $artistNameValue = 'Ed Sheeran';
        $iTunesArtistId = new ITunesArtistId($iTunesArtistIdValue);
        $artistName = new ArtistName($artistNameValue);
        $domainPaginator = $artistRepository->releasedPaginator($iTunesArtistId, $artistName, new ArtistSpecification());
        $this->assertEquals($domainPaginator->getEntities(), []);
        $this->assertEquals($domainPaginator->getPaginator()->total(), 0);
    }

    public function testReleasedPaginator1()
    {
        $artistRepository = app($this->artistRepositoryInterfaceName);

        $verify = [
            '000010a1b2c3d4e5f6a7b8c9d' => true,
            '000020a1b2c3d4e5f6a7b8c9d' => true,
            '000030a1b2c3d4e5f6a7b8c9d' => true,
            '000040a1b2c3d4e5f6a7b8c9d' => true,
        ];
        $domainPaginator = $artistRepository->releasedPaginator(null, null, new ArtistSpecification());
        $artistEntities = $domainPaginator->getEntities();
        $artistIds = [];
        foreach ($artistEntities AS $artistEntity) {
            $artistIdValue = $artistEntity->id()->value();
            if (isset($verify[$artistIdValue])) {
                $artistIds[$artistIdValue] = true;
            }
        }
        sort($verify);
        sort($artistIds);
        $this->assertEquals($verify, $artistIds);
        $this->assertEquals($domainPaginator->getPaginator()->total(), 10);
    }

    public function testReleasedPaginator2()
    {
        $artistRepository = app($this->artistRepositoryInterfaceName);

        $verify = ['000010a1b2c3d4e5f6a7b8c9d'];
        $iTunesArtistIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistId = new ITunesArtistId($iTunesArtistIdValue);
        $domainPaginator = $artistRepository->releasedPaginator($iTunesArtistId, null, new ArtistSpecification());
        $artistEntities = $domainPaginator->getEntities();
        $artistIds = [];
        foreach ($artistEntities AS $artistEntity) {
            $artistIds[] = $artistEntity->id()->value();
        }
        sort($verify);
        sort($artistIds);
        $this->assertEquals($verify, $artistIds);
        $this->assertEquals($domainPaginator->getPaginator()->total(), 1);
    }

    public function testReleasedPaginator3()
    {
        $artistRepository = app($this->artistRepositoryInterfaceName);

        $verify = ['000010a1b2c3d4e5f6a7b8c9d'];
        $artistNameValue = 'Ed';
        $artistName = new ArtistName($artistNameValue);
        $domainPaginator = $artistRepository->releasedPaginator(null, $artistName, new ArtistSpecification());
        $artistEntities = $domainPaginator->getEntities();
        $artistIds = [];
        foreach ($artistEntities AS $artistEntity) {
            $artistIds[] = $artistEntity->id()->value();
        }
        sort($verify);
        sort($artistIds);
        $this->assertEquals($verify, $artistIds);
        $this->assertEquals($domainPaginator->getPaginator()->total(), 1);
    }

    public function testReleasedPaginator4()
    {
        $artistRepository = app($this->artistRepositoryInterfaceName);

        $verify = ['000010a1b2c3d4e5f6a7b8c9d'];
        $iTunesArtistIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $artistNameValue = 'Ed';
        $iTunesArtistId = new ITunesArtistId($iTunesArtistIdValue);
        $artistName = new ArtistName($artistNameValue);
        $domainPaginator = $artistRepository->releasedPaginator($iTunesArtistId, $artistName, new ArtistSpecification());
        $artistEntities = $domainPaginator->getEntities();
        $artistIds = [];
        foreach ($artistEntities AS $artistEntity) {
            $artistIds[] = $artistEntity->id()->value();
        }
        sort($verify);
        sort($artistIds);
        $this->assertEquals($verify, $artistIds);
        $this->assertEquals($domainPaginator->getPaginator()->total(), 1);
    }

}
