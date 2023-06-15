<?php

namespace Tests\Unit\Infrastructure\Repositories;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mockery;
use DB;
use App\Infrastructure\Eloquents\Music;
use App\Infrastructure\Eloquents\ProvisionedMusic;
use App\Infrastructure\Eloquents\PromotionVideo;
use App\Infrastructure\Eloquents\PromotionVideoBrokenLink;
use App\Infrastructure\Repositories\MusicRepository;
use App\Domain\EntityId;
use App\Domain\ValueObjects\ITunesArtistId;
use App\Domain\ValueObjects\MusicTitle;
use App\Domain\ValueObjects\PromotionVideoUrl;
use App\Domain\ValueObjects\ThumbnailUrl;
use App\Domain\ValueObjects\CheckPromotionVideoConditions;
use App\Domain\Music\MusicBusinessId;
use App\Domain\Music\MusicSpecification;
use App\Domain\Music\MusicEntity;
use App\Domain\Music\MusicException;

class MusicRepositoryTest extends TestCase
{

    use DatabaseMigrations;

    private $redisDAOInterfaceName = 'App\Infrastructure\RedisDAO\RedisDAOInterface';
    private $musicFactoryInterfaceName = 'App\Domain\Music\MusicFactoryInterface';
    private $musicRepositoryInterfaceName = 'App\Domain\Music\MusicRepositoryInterface';

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
        $musicRepository = app($this->musicRepositoryInterfaceName);
        $this->assertEquals(get_class($musicRepository), MusicRepository::class);
    }

   public function testCreateId()
    {
        $musicRepository = app($this->musicRepositoryInterfaceName);

        $id = $musicRepository->createId();
        $this->assertEquals(strlen($id->value()), 32);
    }

    public function testFindProvision()
    {
        $musicRepository = app($this->musicRepositoryInterfaceName);

        $idValue = '000010a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($idValue);
        $result = $musicRepository->findProvision($entityId);
        $this->assertNull($result);

        $idValue = '000050a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($idValue);
        $result = $musicRepository->findProvision($entityId);
        $this->assertEquals($result->id()->value(), $idValue);
        $this->assertNotNull($result->promotionVideoUrl());
        $this->assertNotNull($result->thumbnailUrl());

        $idValue = '000060a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($idValue);
        $result = $musicRepository->findProvision($entityId);
        $this->assertNull($result->promotionVideoUrl());
        $this->assertNull($result->thumbnailUrl());
    }

    public function testFindRelease()
    {
        $musicRepository = app($this->musicRepositoryInterfaceName);

        $idValue = '000050a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($idValue);
        $result = $musicRepository->findRelease($entityId);
        $this->assertNull($result);

        $idValue = '000010a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($idValue);
        $result = $musicRepository->findRelease($entityId);
        $this->assertEquals($result->id()->value(), $idValue);
        $this->assertNotNull($result->promotionVideoUrl());
        $this->assertNotNull($result->thumbnailUrl());

        $idValue = '000020a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($idValue);
        $result = $musicRepository->findRelease($entityId);
        $this->assertNull($result->promotionVideoUrl());
        $this->assertNull($result->thumbnailUrl());
    }

    public function testGetProvision()
    {
        $musicRepository = app($this->musicRepositoryInterfaceName);

        $iTunesArtistIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistId = new ITunesArtistId($iTunesArtistIdValue);
        $musicTitleValue = 'Shape Of You';
        $musicTitle = new MusicTitle($musicTitleValue);
        $musicBusinessId = new MusicBusinessId($iTunesArtistId, $musicTitle);
        $result = $musicRepository->getProvision($musicBusinessId);
        $this->assertNull($result);

        $iTunesArtistIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistId = new ITunesArtistId($iTunesArtistIdValue);
        $musicTitleValue = 'Gucci Gang';
        $musicTitle = new MusicTitle($musicTitleValue);
        $musicBusinessId = new MusicBusinessId($iTunesArtistId, $musicTitle);
        $result = $musicRepository->getProvision($musicBusinessId);
        $this->assertEquals($result->iTunesArtistId()->value(), $iTunesArtistIdValue);
        $this->assertEquals($result->musicTitle()->value(), $musicTitleValue);
        $this->assertNotNull($result->promotionVideoUrl());
        $this->assertNotNull($result->thumbnailUrl());

        $iTunesArtistIdValue = '000060a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistId = new ITunesArtistId($iTunesArtistIdValue);
        $musicTitleValue = 'Thunder';
        $musicTitle = new MusicTitle($musicTitleValue);
        $musicBusinessId = new MusicBusinessId($iTunesArtistId, $musicTitle);
        $result = $musicRepository->getProvision($musicBusinessId);
        $this->assertNull($result->promotionVideoUrl());
        $this->assertNull($result->thumbnailUrl());

        $iTunesArtistIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistId = new ITunesArtistId($iTunesArtistIdValue);
        $musicTitleValue = 'Gucci Gang';
        $musicTitle = new MusicTitle($musicTitleValue);
        $musicBusinessId = new MusicBusinessId($iTunesArtistId, $musicTitle);
        $excludeIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($excludeIdValue);
        $result = $musicRepository->getProvision($musicBusinessId, $entityId);
        $this->assertNull($result);
    }

    public function testGetRelease()
    {
        $musicRepository = app($this->musicRepositoryInterfaceName);

        $iTunesArtistIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistId = new ITunesArtistId($iTunesArtistIdValue);
        $musicTitleValue = 'Gucci Gang';
        $musicTitle = new MusicTitle($musicTitleValue);
        $musicBusinessId = new MusicBusinessId($iTunesArtistId, $musicTitle);
        $result = $musicRepository->getRelease($musicBusinessId);
        $this->assertNull($result);

        $iTunesArtistIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistId = new ITunesArtistId($iTunesArtistIdValue);
        $musicTitleValue = 'Shape Of You';
        $musicTitle = new MusicTitle($musicTitleValue);
        $musicBusinessId = new MusicBusinessId($iTunesArtistId, $musicTitle);
        $result = $musicRepository->getRelease($musicBusinessId);
        $this->assertEquals($result->iTunesArtistId()->value(), $iTunesArtistIdValue);
        $this->assertEquals($result->musicTitle()->value(), $musicTitleValue);
        $this->assertNotNull($result->promotionVideoUrl());
        $this->assertNotNull($result->thumbnailUrl());

        $iTunesArtistIdValue = '000020a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistId = new ITunesArtistId($iTunesArtistIdValue);
        $musicTitleValue = 'Humble.';
        $musicTitle = new MusicTitle($musicTitleValue);
        $musicBusinessId = new MusicBusinessId($iTunesArtistId, $musicTitle);
        $result = $musicRepository->getRelease($musicBusinessId);
        $this->assertNull($result->promotionVideoUrl());
        $this->assertNull($result->thumbnailUrl());

        $iTunesArtistIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistId = new ITunesArtistId($iTunesArtistIdValue);
        $musicTitleValue = 'Shape Of You';
        $musicTitle = new MusicTitle($musicTitleValue);
        $musicBusinessId = new MusicBusinessId($iTunesArtistId, $musicTitle);
        $excludeIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($excludeIdValue);
        $result = $musicRepository->getRelease($musicBusinessId, $entityId);
        $this->assertNull($result);
    }

    public function testRefreshCachedEntity()
    {
        $musicRepository = app($this->musicRepositoryInterfaceName);
        $redisDAO = app($this->redisDAOInterfaceName);
        $musicSpecification = new MusicSpecification();

        $idValue = '000050a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($idValue);
        $cacheKey = $musicRepository->cacheKeyById($entityId, MusicEntity::class);
        $redisDAO->set($cacheKey, '1');
        $musicRepository->refreshCachedEntity($entityId, $musicSpecification);
        $cache = $redisDAO->get($cacheKey);
        $this->assertNull($cache);

        $idValue = '000010a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($idValue);
        $cacheKey = $musicRepository->cacheKeyById($entityId, MusicEntity::class);
        $redisDAO->set($cacheKey, '1');
        $musicRepository->refreshCachedEntity($entityId, $musicSpecification);
        $cache = $redisDAO->get($cacheKey);
        $musicEntity = unserialize($cache);
        $this->assertEquals($musicEntity->id()->value(), $idValue);
    }

    public function testRegisterProvisionedMusicAlreadyExist()
    {
        $musicFactory = app($this->musicFactoryInterfaceName);
        $musicRepository = app($this->musicRepositoryInterfaceName);
        $musicSpecification = new MusicSpecification();

        $idValue = '000050a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistIdValue = '000090a1b2c3d4e5f6a7b8c9d';
        $musicTitleValue = 'Bad At Love';
        $musicEntity = $musicFactory->create($idValue, $iTunesArtistIdValue, $musicTitleValue);
        $validated = false;
        try {
            $musicRepository->register($musicEntity, $musicSpecification);
        } catch (MusicException $e) {
            if ($e->getMessage() === "Couldn't register to provision MusicEntity because provisioned Music is already existing.") {
                $validated = true;
            }
        }
        $this->assertTrue($validated);

        $idValue = '000090a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $musicTitleValue = 'Gucci Gang';
        $musicEntity = $musicFactory->create($idValue, $iTunesArtistIdValue, $musicTitleValue);
        $validated = false;
        try {
            $musicRepository->register($musicEntity, $musicSpecification);
        } catch (MusicException $e) {
            if ($e->getMessage() === "Couldn't register to provision MusicEntity because provisioned Music is already existing.") {
                $validated = true;
            }
        }
        $this->assertTrue($validated);
    }

    public function testRegisterReleasedMusicAlreadyExist()
    {
        $musicFactory = app($this->musicFactoryInterfaceName);
        $musicRepository = app($this->musicRepositoryInterfaceName);
        $musicSpecification = new MusicSpecification();

        $idValue = '000010a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistIdValue = '000090a1b2c3d4e5f6a7b8c9d';
        $musicTitleValue = 'Bad At Love';
        $musicEntity = $musicFactory->create($idValue, $iTunesArtistIdValue, $musicTitleValue);
        $validated = false;
        try {
            $musicRepository->register($musicEntity, $musicSpecification);
        } catch (MusicException $e) {
            if ($e->getMessage() === "Couldn't register to provision MusicEntity because released Music is already existing.") {
                $validated = true;
            }
        }
        $this->assertTrue($validated);

        $idValue = '000090a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $musicTitleValue = 'Shape Of You';
        $musicEntity = $musicFactory->create($idValue, $iTunesArtistIdValue, $musicTitleValue);
        $validated = false;
        try {
            $musicRepository->register($musicEntity, $musicSpecification);
        } catch (MusicException $e) {
            if ($e->getMessage() === "Couldn't register to provision MusicEntity because released Music is already existing.") {
                $validated = true;
            }
        }
        $this->assertTrue($validated);
    }

    public function testRegister()
    {
        $musicFactory = app($this->musicFactoryInterfaceName);
        $musicRepository = app($this->musicRepositoryInterfaceName);
        $musicSpecification = new MusicSpecification();

        $idValue = $musicRepository->createId()->value();
        $iTunesArtistIdValue = '000090a1b2c3d4e5f6a7b8c9d';
        $musicTitleValue = 'Bad At Love';
        $musicEntity = $musicFactory->create($idValue, $iTunesArtistIdValue, $musicTitleValue);
        $result = $musicRepository->register($musicEntity, $musicSpecification);
        $this->assertTrue($result);
        $registered = $musicRepository->findProvision($musicEntity->id());
        $this->assertEquals($registered->id(), $musicEntity->id());
        $this->assertNull($registered->promotionVideoUrl());
        $this->assertNull($registered->thumbnailUrl());

        $idValue = $musicRepository->createId()->value();
        $iTunesArtistIdValue = '000100a1b2c3d4e5f6a7b8c9d';
        $musicTitleValue = 'Bad At Love';
        $promotionVideoUrlValue = 'https://www.youtube.com/watch?v=xdYFuCp3m9k';
        $thumbnailUrlValue = 'https://i.ytimg.com/vi/xdYFuCp3m9k/default.jpg';
        $musicEntity = $musicFactory->create($idValue, $iTunesArtistIdValue, $musicTitleValue, $promotionVideoUrlValue, $thumbnailUrlValue);
        $result = $musicRepository->register($musicEntity, $musicSpecification);
        $this->assertTrue($result);
        $registered = $musicRepository->findProvision($musicEntity->id());
        $this->assertEquals($registered->promotionVideoUrl()->value(), $promotionVideoUrlValue);
        $this->assertEquals($registered->thumbnailUrl()->value(), $thumbnailUrlValue);
    }

    public function testModifyProvisionReleasedAlreadyExist()
    {
        $musicFactory = app($this->musicFactoryInterfaceName);
        $musicRepository = app($this->musicRepositoryInterfaceName);
        $musicSpecification = new MusicSpecification();

        $idValue = '000050a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistIdValue = '000090a1b2c3d4e5f6a7b8c9d';
        $musicTitleValue = 'Bad At Love';
        $parameters = [
            'id'                =>  $idValue,
            'itunes_artist_id'  =>  $iTunesArtistIdValue,
            'music_title'       =>  $musicTitleValue
        ];
        $music = new Music();
        $music->fill($parameters)->save();
        $entityId = new EntityId($idValue);
        $modifiedEntity = $musicRepository->findProvision($entityId);
        $musicTitleValue = 'Gucci Gang+';
        $musicTitle = new MusicTitle($musicTitleValue);
        $modifiedEntity->setMusicTitle($musicTitle);
        $validated = false;
        try {
            $musicRepository->modifyProvision($modifiedEntity, $musicSpecification);
        } catch (MusicException $e) {
            if ($e->getMessage() === "Couldn't modify provisioned MusicEntity because released Music is already existing.") {
                $validated = true;
            }
        }
        $this->assertTrue($validated);
        Music::destroy($idValue);

        $idValue = '000050a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($idValue);
        $modifiedEntity = $musicRepository->findProvision($entityId);
        $iTunesArtistIdValue = '000040a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistId = new ITunesArtistId($iTunesArtistIdValue);
        $musicTitleValue = 'Havana';
        $musicTitle = new MusicTitle($musicTitleValue);
        $modifiedEntity
            ->setITunesArtistId($iTunesArtistId)
            ->setMusicTitle($musicTitle);
        $validated = false;
        try {
            $musicRepository->modifyProvision($modifiedEntity, $musicSpecification);
        } catch (MusicException $e) {
            if ($e->getMessage() === "Couldn't modify provisioned MusicEntity because released Music is already existing.") {
                $validated = true;
            }
        }
        $this->assertTrue($validated);
    }

    public function testModifyProvisionProvisioedAlreadyExist()
    {
        $musicRepository = app($this->musicRepositoryInterfaceName);
        $musicSpecification = new MusicSpecification();

        $idValue = '000050a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($idValue);
        $modifiedEntity = $musicRepository->findProvision($entityId);
        $iTunesArtistIdValue = '000060a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistId = new ITunesArtistId($iTunesArtistIdValue);
        $musicTitleValue = 'Thunder';
        $musicTitle = new MusicTitle($musicTitleValue);
        $modifiedEntity
            ->setITunesArtistId($iTunesArtistId)
            ->setMusicTitle($musicTitle);
        $validated = false;
        try {
            $musicRepository->modifyProvision($modifiedEntity, $musicSpecification);
        } catch (MusicException $e) {
            if ($e->getMessage() === "Couldn't modify provisioned MusicEntity because provisioned Music is already existing.") {
                $validated = true;
            }
        }
        $this->assertTrue($validated);
    }

    public function testModifyProvisionProvisioedDoesNotExist()
    {
        $musicFactory = app($this->musicFactoryInterfaceName);
        $musicRepository = app($this->musicRepositoryInterfaceName);
        $musicSpecification = new MusicSpecification();

        $idValue = '000090a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistIdValue = '000090a1b2c3d4e5f6a7b8c9d';
        $musicTitleValue = 'Bad At Love';
        $modifiedEntity = $musicFactory->create($idValue, $iTunesArtistIdValue, $musicTitleValue);
        $validated = false;
        try {
            $musicRepository->modifyProvision($modifiedEntity, $musicSpecification);
        } catch (MusicException $e) {
            if ($e->getMessage() === "Couldn't modify provisioned MusicEntity because provisioned Music doesn't exist.") {
                $validated = true;
            }
        }
        $this->assertTrue($validated);
    }

    public function testModifyProvision()
    {
        $musicRepository = app($this->musicRepositoryInterfaceName);
        $musicSpecification = new MusicSpecification();

        $idValue = '000050a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($idValue);
        $modifiedEntity = $musicRepository->findProvision($entityId);
        $musicTitleValue = 'Gucci Gang+';
        $musicTitle = new MusicTitle($musicTitleValue);
        $modifiedEntity->setMusicTitle($musicTitle);
        $result = $musicRepository->modifyProvision($modifiedEntity, $musicSpecification);
        $this->assertTrue($result);
        $musicEntity = $musicRepository->findProvision($entityId);
        $this->assertEquals($musicEntity->musicTitle()->value(), $musicTitleValue);

        $idValue = '000050a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($idValue);
        $modifiedEntity = $musicRepository->findProvision($entityId);
        $promotionVideoUrlValue = "https://www.youtube.com/watch?v=4LfJnj66HVQ";
        $promotionVideoUrl = new PromotionVideoUrl($promotionVideoUrlValue);
        $thumbnailUrlValue = "https://i.ytimg.com/vi/4LfJnj66HVQ/default.jpg";
        $thumbnailUrl = new ThumbnailUrl($thumbnailUrlValue);
        $modifiedEntity
            ->setPromotionVideoUrl($promotionVideoUrl)
            ->setThumbnailUrl($thumbnailUrl);
        $result = $musicRepository->modifyProvision($modifiedEntity, $musicSpecification);
        $this->assertTrue($result);
        $musicEntity = $musicRepository->findProvision($entityId);
        $this->assertEquals($musicEntity->promotionVideoUrl()->value(), $promotionVideoUrlValue);
        $this->assertEquals($musicEntity->thumbnailUrl()->value(), $thumbnailUrlValue);

        $idValue = '000050a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($idValue);
        $modifiedEntity = $musicRepository->findProvision($entityId);
        $modifiedEntity
            ->setPromotionVideoUrl(null)
            ->setThumbnailUrl(null);
        $result = $musicRepository->modifyProvision($modifiedEntity, $musicSpecification);
        $this->assertTrue($result);
        $musicEntity = $musicRepository->findProvision($entityId);
        $this->assertNull($musicEntity->promotionVideoUrl());
        $this->assertNull($musicEntity->thumbnailUrl());

        $idValue = '000060a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($idValue);
        $modifiedEntity = $musicRepository->findProvision($entityId);
        $promotionVideoUrlValue = "https://www.youtube.com/watch?v=fKopy74weus";
        $promotionVideoUrl = new PromotionVideoUrl($promotionVideoUrlValue);
        $thumbnailUrlValue = "https://i.ytimg.com/vi/fKopy74weus/default.jpg";
        $thumbnailUrl = new ThumbnailUrl($thumbnailUrlValue);
        $modifiedEntity
            ->setPromotionVideoUrl($promotionVideoUrl)
            ->setThumbnailUrl($thumbnailUrl);
        $result = $musicRepository->modifyProvision($modifiedEntity, $musicSpecification);
        $this->assertTrue($result);
        $musicEntity = $musicRepository->findProvision($entityId);
        $this->assertEquals($musicEntity->promotionVideoUrl()->value(), $promotionVideoUrlValue);
        $this->assertEquals($musicEntity->thumbnailUrl()->value(), $thumbnailUrlValue);
    }

    public function testDeleteProvisionedDoesNotExist()
    {
        $musicRepository = app($this->musicRepositoryInterfaceName);
        $musicSpecification = new MusicSpecification();

        $idValue = '000090a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($idValue);
        $validated = false;
        try {
            $musicRepository->delete($entityId, $musicSpecification);
        } catch (MusicException $e) {
            if ($e->getMessage() === "Couldn't delete provisioned MusicEntity because provisioned Music doesn't exist.") {
                $validated = true;
            }
        }
        $this->assertTrue($validated);
    }

    public function testDelete()
    {
        $musicRepository = app($this->musicRepositoryInterfaceName);
        $musicSpecification = new MusicSpecification();

        $idValue = '000050a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($idValue);
        $result = $musicRepository->delete($entityId, $musicSpecification);
        $this->assertTrue($result);
        $musicEntity = $musicRepository->findProvision($entityId);
        $this->assertNull($musicEntity);

        $idValue = '000060a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($idValue);
        $result = $musicRepository->delete($entityId, $musicSpecification);
        $this->assertTrue($result);
        $musicEntity = $musicRepository->findProvision($entityId);
        $this->assertNull($musicEntity);
        $row = PromotionVideo::where('music_id', $idValue)->get();
        $this->assertEmpty($row);
    }

    public function testReleaseReleasedAlreadyExist()
    {
        $musicRepository = app($this->musicRepositoryInterfaceName);
        $musicSpecification = new MusicSpecification();

        $idValue = '000050a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistIdValue = '000090a1b2c3d4e5f6a7b8c9d';
        $musicTitleValue = 'Bad At Love';
        $parameters = [
            'id'                =>  $idValue,
            'itunes_artist_id'  =>  $iTunesArtistIdValue,
            'music_title'       =>  $musicTitleValue,
        ];
        $music = new Music();
        $music->fill($parameters)->save();
        $entityId = new EntityId($idValue);
        $validated = false;
        try {
            $musicRepository->release($entityId, $musicSpecification);
        } catch (MusicException $e) {
            if ($e->getMessage() === "Couldn't release provisioned MusicEntity because released Music is already existing.") {
                $validated = true;
            }
        }
        $this->assertTrue($validated);
        Music::destroy($idValue);

        $idValue = '000090a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $musicTitleValue = 'Gucci Gang';
        $parameters = [
            'id'                =>  $idValue,
            'itunes_artist_id'  =>  $iTunesArtistIdValue,
            'music_title'       =>  $musicTitleValue,
        ];
        $music = new Music();
        $music->fill($parameters)->save();
        $entityId = new EntityId($idValue);
        $validated = false;
        try {
            $musicRepository->release($entityId, $musicSpecification);
        } catch (MusicException $e) {
            if ($e->getMessage() === "Couldn't release provisioned MusicEntity because released Music is already existing.") {
                $validated = true;
            }
        }
        $this->assertTrue($validated);
        Music::destroy($idValue);
    }

    public function testReleaseProvisionedDoesNotExist()
    {
        $musicRepository = app($this->musicRepositoryInterfaceName);
        $musicSpecification = new MusicSpecification();

        $idValue = '000090a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($idValue);
        $validated = false;
        try {
            $musicRepository->release($entityId, $musicSpecification);
        } catch (MusicException $e) {
            if ($e->getMessage() === "Couldn't release provisioned MusicEntity because provisioned Music doesn't exist.") {
                $validated = true;
            }
        }
        $this->assertTrue($validated);
    }

    public function testReleaseProvisionedAlreadyExist()
    {
        $musicRepository = app($this->musicRepositoryInterfaceName);
        $musicSpecification = new MusicSpecification();

        $idValue = '000050a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistIdValue = '000060a1b2c3d4e5f6a7b8c9d';
        $musicTitleValue = 'Thunder';
        $parameters = [
            'itunes_artist_id'  =>  $iTunesArtistIdValue,
            'music_title'       =>  $musicTitleValue
        ];
        ProvisionedMusic::find($idValue)->fill($parameters)->save();
        $entityId = new EntityId($idValue);
        $validated = false;
        try {
            $musicRepository->release($entityId, $musicSpecification);
        } catch (MusicException $e) {
            if ($e->getMessage() === "Couldn't release provisioned MusicEntity because same provisioned Music is already existing.") {
                $validated = true;
            }
        }
        $this->assertTrue($validated);
    }

    public function testRelease()
    {
        $musicRepository = app($this->musicRepositoryInterfaceName);
        $musicSpecification = new MusicSpecification();

        $idValue = '000050a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($idValue);
        $result = $musicRepository->release($entityId, $musicSpecification);
        $this->assertTrue($result);
        $musicEntity = $musicRepository->findRelease($entityId);
        $this->assertEquals($musicEntity->id()->value(), $idValue);
        $musicEntity = $musicRepository->findProvision($entityId);
        $this->assertNull($musicEntity);
    }

    public function testModifyReleaseReleasedAlreadyExist()
    {
        $musicRepository = app($this->musicRepositoryInterfaceName);
        $musicSpecification = new MusicSpecification();

        $idValue = '000010a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistIdValue = '000020a1b2c3d4e5f6a7b8c9d';
        $musicTitleValue = 'Humble.';
        $entityId = new EntityId($idValue);
        $modifiedEntity = $musicRepository->findRelease($entityId);
        $iTunesArtistId = new ITunesArtistId($iTunesArtistIdValue);
        $musicTitle = new MusicTitle($musicTitleValue);
        $modifiedEntity
            ->setITunesArtistId($iTunesArtistId)
            ->setMusicTitle($musicTitle);
        $validated = false;
        try {
            $musicRepository->modifyRelease($modifiedEntity, $musicSpecification);
        } catch (MusicException $e) {
            if ($e->getMessage() === "Couldn't modify released MusicEntity because released Music is already existing.") {
                $validated = true;
            }
        }
        $this->assertTrue($validated);
    }

    public function testModifyReleaseReleasedDoesNotExist()
    {
        $musicFactory = app($this->musicFactoryInterfaceName);
        $musicRepository = app($this->musicRepositoryInterfaceName);
        $musicSpecification = new MusicSpecification();

        $idValue = '000090a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistIdValue = '000090a1b2c3d4e5f6a7b8c9d';
        $musicTitleValue = 'Bad At Love';
        $musicEntity = $musicFactory->create($idValue, $iTunesArtistIdValue, $musicTitleValue);
        $validated = false;
        try {
            $musicRepository->modifyRelease($musicEntity, $musicSpecification);
        } catch (MusicException $e) {
            if ($e->getMessage() === "Couldn't modify released MusicEntity because released Music doesn't exist.") {
                $validated = true;
            }
        }
        $this->assertTrue($validated);
    }

    public function testModifyReleaseProvisionedAlreadyExist()
    {
        $musicRepository = app($this->musicRepositoryInterfaceName);
        $musicSpecification = new MusicSpecification();

        $idValue = '000010a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistIdValue = '000090a1b2c3d4e5f6a7b8c9d';
        $musicTitleValue = 'Bad At Love';
        $parameters = [
            'id'                =>  $idValue,
            'itunes_artist_id'  =>  $iTunesArtistIdValue,
            'music_title'       =>  $musicTitleValue
        ];
        $provisionedMusic = new ProvisionedMusic();
        $provisionedMusic->fill($parameters)->save();
        $entityId = new EntityId($idValue);
        $modifiedEntity = $musicRepository->findRelease($entityId);
        $musicTitleValue = 'Ed Sheeran+';
        $musicTitle = new MusicTitle($musicTitleValue);
        $modifiedEntity->setMusicTitle($musicTitle);
        $validated = false;
        try {
            $musicRepository->modifyRelease($modifiedEntity, $musicSpecification);
        } catch (MusicException $e) {
            if ($e->getMessage() === "Couldn't modify released MusicEntity because provisioned Music is already existing.") {
                $validated = true;
            }
        }
        $this->assertTrue($validated);
        ProvisionedMusic::destroy($idValue);

        $idValue = '000010a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($idValue);
        $modifiedEntity = $musicRepository->findRelease($entityId);
        $iTunesArtistIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $musicTitleValue = 'Gucci Gang';
        $iTunesArtistId = new ITunesArtistId($iTunesArtistIdValue);
        $musicTitle = new MusicTitle($musicTitleValue);
        $modifiedEntity
            ->setMusicTitle($musicTitle)
            ->setITunesArtistId($iTunesArtistId);
        $validated = false;
        try {
            $musicRepository->modifyRelease($modifiedEntity, $musicSpecification);
        } catch (MusicException $e) {
            if ($e->getMessage() === "Couldn't modify released MusicEntity because provisioned Music is already existing.") {
                $validated = true;
            }
        }
        $this->assertTrue($validated);
    }

    public function testModifyRelease()
    {
        $musicRepository = app($this->musicRepositoryInterfaceName);
        $musicSpecification = new MusicSpecification();

        $idValue = '000010a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($idValue);
        $modifiedEntity = $musicRepository->findRelease($entityId);
        $musicTitleValue = 'Shape Of You+';
        $musicTitle = new MusicTitle($musicTitleValue);
        $modifiedEntity->setMusicTitle($musicTitle);
        $result = $musicRepository->modifyRelease($modifiedEntity, $musicSpecification);
        $this->assertTrue($result);
        $musicEntity = $musicRepository->findRelease($entityId);
        $this->assertEquals($musicEntity->musicTitle()->value(), $musicTitleValue);

        $idValue = '000010a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($idValue);
        $modifiedEntity = $musicRepository->findRelease($entityId);
        $promotionVideoUrlValue = "https://www.youtube.com/watch?v=JGwWNGJdvx8";
        $promotionVideoUrl = new PromotionVideoUrl($promotionVideoUrlValue);
        $thumbnailUrlValue = 'https://i.ytimg.com/vi/JGwWNGJdvx8/default.jpg';
        $thumbnailUrl = new ThumbnailUrl($thumbnailUrlValue);
        $modifiedEntity
            ->setPromotionVideoUrl($promotionVideoUrl)
            ->setThumbnailUrl($thumbnailUrl);
        $result = $musicRepository->modifyRelease($modifiedEntity, $musicSpecification);
        $this->assertTrue($result);
        $musicEntity = $musicRepository->findRelease($entityId);
        $this->assertEquals($musicEntity->promotionVideoUrl()->value(), $promotionVideoUrlValue);
        $this->assertEquals($musicEntity->thumbnailUrl()->value(), $thumbnailUrlValue);

        $idValue = '000010a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($idValue);
        $modifiedEntity = $musicRepository->findRelease($entityId);
        $modifiedEntity->setPromotionVideoUrl(null);
        $result = $musicRepository->modifyRelease($modifiedEntity, $musicSpecification);
        $this->assertTrue($result);
        $musicEntity = $musicRepository->findRelease($entityId);
        $this->assertNull($musicEntity->promotionVideoUrl());

        $idValue = '000020a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($idValue);
        $modifiedEntity = $musicRepository->findRelease($entityId);
        $promotionVideoUrlValue = "https://www.youtube.com/watch?v=tvTRZJ-4EyI";
        $promotionVideoUrl = new PromotionVideoUrl($promotionVideoUrlValue);
        $thumbnailUrlValue = 'https://i.ytimg.com/vi/tvTRZJ-4EyI/default.jpg';
        $thumbnailUrl = new ThumbnailUrl($thumbnailUrlValue);
        $modifiedEntity
            ->setPromotionVideoUrl($promotionVideoUrl)
            ->setThumbnailUrl($thumbnailUrl);
        $result = $musicRepository->modifyRelease($modifiedEntity, $musicSpecification);
        $this->assertTrue($result);
        $musicEntity = $musicRepository->findRelease($entityId);
        $this->assertEquals($musicEntity->promotionVideoUrl()->value(), $promotionVideoUrlValue);
        $this->assertEquals($musicEntity->thumbnailUrl()->value(), $thumbnailUrlValue);
    }

    public function testRollbackProvisionedAlreadyExist()
    {
        $musicRepository = app($this->musicRepositoryInterfaceName);
        $musicSpecification = new MusicSpecification();

        $idValue = '000010a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistIdValue = '000090a1b2c3d4e5f6a7b8c9d';
        $musicTitleValue = 'Bad At Love';
        $parameters = [
            'id'                =>  $idValue,
            'itunes_artist_id'  =>  $iTunesArtistIdValue,
            'music_title'       =>  $musicTitleValue
        ];
        $provisionedMusic = new ProvisionedMusic();
        $provisionedMusic->fill($parameters)->save();
        $entityId = new EntityId($idValue);
        $validated = false;
        try {
            $musicRepository->rollback($entityId, $musicSpecification);
        } catch (MusicException $e) {
            if ($e->getMessage() === "Couldn't rollback MusicEntity because provisioned Music is already existing.") {
                $validated = true;
            }
        }
        $this->assertTrue($validated);
        ProvisionedMusic::destroy($idValue);

        $idValue = '000090a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $musicTitleValue = 'Shape Of You';
        $parameters = [
            'id'                =>  $idValue,
            'itunes_artist_id'  =>  $iTunesArtistIdValue,
            'music_title'       =>  $musicTitleValue,
        ];
        $provisionedMusic = new ProvisionedMusic();
        $provisionedMusic->fill($parameters)->save();
        $idValue = '000010a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($idValue);
        $validated = false;
        try {
            $musicRepository->rollback($entityId, $musicSpecification);
        } catch (MusicException $e) {
            if ($e->getMessage() === "Couldn't rollback MusicEntity because provisioned Music is already existing.") {
                $validated = true;
            }
        }
        $this->assertTrue($validated);
        ProvisionedMusic::destroy('000090a1b2c3d4e5f6a7b8c9d');
    }

    public function testRollbackReleasedDoesNotExist()
    {
        $musicRepository = app($this->musicRepositoryInterfaceName);
        $musicSpecification = new MusicSpecification();

        $idValue = '000090a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($idValue);
        $validated = false;
        try {
            $musicRepository->rollback($entityId, $musicSpecification);
        } catch (MusicException $e) {
            if ($e->getMessage() === "Couldn't rollback MusicEntity because released Music doesn't exist.") {
                $validated = true;
            }
        }
        $this->assertTrue($validated);
    }

    public function testRollbackReleasedAlreadyExist()
    {
        $musicRepository = app($this->musicRepositoryInterfaceName);
        $musicSpecification = new MusicSpecification();

        $parameters = [
            'id'                =>  '000090a1b2c3d4e5f6a7b8c9d',
            'itunes_artist_id'  =>  '000010a1b2c3d4e5f6a7b8c9d',
            'music_title'       =>  'Shape Of You'
        ];
        $music = new Music();
        $music->fill($parameters)->save();

        $idValue = '000010a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($idValue);
        $validated = false;
        try {
            $musicRepository->rollback($entityId, $musicSpecification);
        } catch (MusicException $e) {
            if ($e->getMessage() === "Couldn't rollback MusicEntity because same released Music is existing.") {
                $validated = true;
            }
        }
        $this->assertTrue($validated);
        Music::destroy('000090a1b2c3d4e5f6a7b8c9d');
    }

    public function testRollback()
    {
        $musicRepository = app($this->musicRepositoryInterfaceName);
        $musicSpecification = new MusicSpecification();

        $idValue = '000010a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($idValue);
        $result = $musicRepository->rollback($entityId, $musicSpecification);
        $this->assertTrue($result);
        $musicEntity = $musicRepository->findRelease($entityId);
        $this->assertNull($musicEntity);
        $musicEntity = $musicRepository->findProvision($entityId);
        $this->assertEquals($musicEntity->id()->value(), $idValue);
    }

    public function testCheckPromotionVideoListConditionsEmpty()
    {
        $musicRepository = app($this->musicRepositoryInterfaceName);

        $checkPromotionVideoConditions = new CheckPromotionVideoConditions();
        $list = $musicRepository->checkPromotionVideoList($checkPromotionVideoConditions);
        $this->assertEmpty($list);
    }

    public function testCheckPromotionVideoListMatchedEmpty()
    {
        $musicRepository = app($this->musicRepositoryInterfaceName);

        $today = new \DatetimeImmutable(date('Y-m-d'));
        $oneYearAgo = $today->sub(new \DateInterval('P1Y'));
        $oneYearAndOneDayAgo = $oneYearAgo->sub(new \DateInterval('P1D'));

        Music::query()->update(['created_at' => $oneYearAndOneDayAgo->format('Y-m-d')]);
        ProvisionedMusic::query()->update(['created_at' => $oneYearAndOneDayAgo->format('Y-m-d')]);

        $checkPromotionVideoConditions = new CheckPromotionVideoConditions();
        $checkPromotionVideoConditions->appendCreatedAtGTE($oneYearAgo->format('Y-m-d'));
        $list = $musicRepository->checkPromotionVideoList($checkPromotionVideoConditions);
        $this->assertEmpty($list);
    }

    public function testCheckPromotionVideoListCreatedAtGTE()
    {
        $musicRepository = app($this->musicRepositoryInterfaceName);

        $today = new \DatetimeImmutable(date('Y-m-d'));
        $oneYearAgo = $today->sub(new \DateInterval('P1Y'));
        $oneYearAndOneDayAgo = $oneYearAgo->sub(new \DateInterval('P1D'));

        Music::query()->update(['created_at' => $oneYearAndOneDayAgo->format('Y-m-d')]);
        ProvisionedMusic::query()->update(['created_at' => $oneYearAndOneDayAgo->format('Y-m-d')]);
        $verifyMusic = Music::find('000010a1b2c3d4e5f6a7b8c9d');
        $verifyMusic->created_at = $oneYearAgo->format('Y-m-d');
        $verifyMusic->save();
        $verfyPrivisionedMusic = ProvisionedMusic::find('000050a1b2c3d4e5f6a7b8c9d');
        $verfyPrivisionedMusic->created_at = $oneYearAgo->format('Y-m-d');
        $verfyPrivisionedMusic->save();

        $verify = ['000010a1b2c3d4e5f6a7b8c9d', '000050a1b2c3d4e5f6a7b8c9d'];

        $checkPromotionVideoConditions = new CheckPromotionVideoConditions();
        $checkPromotionVideoConditions->appendCreatedAtGTE($oneYearAgo->format('Y-m-d'));
        $list = $musicRepository->checkPromotionVideoList($checkPromotionVideoConditions);
        $listIds = [];
        foreach ($list AS $musicEntity) {
            $listIds[] = $musicEntity->id()->value();
        }
        sort($verify);
        sort($listIds);
        $this->assertEquals($verify, $listIds);
    }

    public function testCheckPromotionVideoListCreatedAtLTAndIdLike1()
    {
        $musicRepository = app($this->musicRepositoryInterfaceName);

        $today = new \DatetimeImmutable(date('Y-m-d'));
        $oneYearAgo = $today->sub(new \DateInterval('P1Y'));
        $oneYearAndOneDayAgo = $oneYearAgo->sub(new \DateInterval('P1D'));

        Music::query()->update(['created_at' => $oneYearAgo->format('Y-m-d')]);
        ProvisionedMusic::query()->update(['created_at' => $oneYearAgo->format('Y-m-d')]);
        $verifyMusic = Music::find('000010a1b2c3d4e5f6a7b8c9d');
        $verifyMusic->created_at = $oneYearAndOneDayAgo->format('Y-m-d');
        $verifyMusic->save();

        $verify = ['000010a1b2c3d4e5f6a7b8c9d'];

        $checkPromotionVideoConditions = new CheckPromotionVideoConditions();
        $checkPromotionVideoConditions->appendCreatedAtLt($oneYearAgo->format('Y-m-d'));
        $checkPromotionVideoConditions->appendMusicIdLike('00001');
        $list = $musicRepository->checkPromotionVideoList($checkPromotionVideoConditions);
        $listIds = [];
        foreach ($list AS $musicEntity) {
            $listIds[] = $musicEntity->id()->value();
        }
        sort($verify);
        sort($listIds);
        $this->assertEquals($verify, $listIds);
    }

    public function testCheckPromotionVideoListCreatedAtLTAndIdLike2()
    {
        $musicRepository = app($this->musicRepositoryInterfaceName);

        $today = new \DatetimeImmutable(date('Y-m-d'));
        $oneYearAgo = $today->sub(new \DateInterval('P1Y'));
        $oneYearAndOneDayAgo = $oneYearAgo->sub(new \DateInterval('P1D'));

        Music::query()->update(['created_at' => $oneYearAgo->format('Y-m-d')]);
        ProvisionedMusic::query()->update(['created_at' => $oneYearAgo->format('Y-m-d')]);
        $verifyProvisionedMusic = ProvisionedMusic::find('000050a1b2c3d4e5f6a7b8c9d');
        $verifyProvisionedMusic->created_at = $oneYearAndOneDayAgo->format('Y-m-d');
        $verifyProvisionedMusic->save();

        $verify = ['000050a1b2c3d4e5f6a7b8c9d'];

        $checkPromotionVideoConditions = new CheckPromotionVideoConditions();
        $checkPromotionVideoConditions->appendCreatedAtLt($oneYearAgo->format('Y-m-d'));
        $checkPromotionVideoConditions->appendMusicIdLike('00005');
        $list = $musicRepository->checkPromotionVideoList($checkPromotionVideoConditions);
        $listIds = [];
        foreach ($list AS $musicEntity) {
            $listIds[] = $musicEntity->id()->value();
        }
        sort($verify);
        sort($listIds);
        $this->assertEquals($verify, $listIds);
    }

    public function testCheckPromotionVideoListCreatedAtLTAndIdLike3()
    {
        $musicRepository = app($this->musicRepositoryInterfaceName);

        $today = new \DatetimeImmutable(date('Y-m-d'));
        $oneYearAgo = $today->sub(new \DateInterval('P1Y'));
        $oneYearAndOneDayAgo = $oneYearAgo->sub(new \DateInterval('P1D'));

        Music::query()->update(['created_at' => $oneYearAgo->format('Y-m-d')]);
        ProvisionedMusic::query()->update(['created_at' => $oneYearAgo->format('Y-m-d')]);
        $verifyMusic = Music::find('000010a1b2c3d4e5f6a7b8c9d');
        $verifyMusic->created_at = $oneYearAndOneDayAgo->format('Y-m-d');
        $verifyMusic->save();
        $verifyProvisionedMusic = ProvisionedMusic::find('000050a1b2c3d4e5f6a7b8c9d');
        $verifyProvisionedMusic->created_at = $oneYearAndOneDayAgo->format('Y-m-d');
        $verifyProvisionedMusic->save();

        $verify = ['000010a1b2c3d4e5f6a7b8c9d', '000050a1b2c3d4e5f6a7b8c9d'];

        $checkPromotionVideoConditions = new CheckPromotionVideoConditions();
        $checkPromotionVideoConditions->appendCreatedAtLt($oneYearAgo->format('Y-m-d'));
        $checkPromotionVideoConditions->appendMusicIdLike('0000');
        $list = $musicRepository->checkPromotionVideoList($checkPromotionVideoConditions);
        $listIds = [];
        foreach ($list AS $musicEntity) {
            $listIds[] = $musicEntity->id()->value();
        }
        sort($verify);
        sort($listIds);
        $this->assertEquals($verify, $listIds);
    }

    public function testRegisterPromotionVideoBrokenLink()
    {
        $musicRepository = app($this->musicRepositoryInterfaceName);

        $idValue = '000010a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($idValue);
        $musicEntity = $musicRepository->findRelease($entityId);
        $verify = [$idValue];
        $result = $musicRepository->registerPromotionVideoBrokenLink($musicEntity);
        $this->assertTrue($result);
        $rows = PromotionVideoBrokenLink::musicId($idValue)->get();
        $lists = [];
        foreach ($rows AS $row) {
            $lists[] = $row->music_id;
        }
        sort($verify);
        sort($lists);
        $this->assertEquals($verify, $lists);
    }

    public function testRegisterPromotionVideoBrokenLink2()
    {
        $musicRepository = app($this->musicRepositoryInterfaceName);

        $idValue = '000010a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($idValue);
        $musicEntity = $musicRepository->findRelease($entityId);
        $verify = [$idValue];
        $result = $musicRepository->registerPromotionVideoBrokenLink($musicEntity);
        $this->assertTrue($result);
        $result = $musicRepository->registerPromotionVideoBrokenLink($musicEntity);
        $this->assertTrue($result);
        $rows = PromotionVideoBrokenLink::musicId($idValue)->get();
        $lists = [];
        foreach ($rows AS $row) {
            $lists[] = $row->music_id;
        }
        sort($verify);
        sort($lists);
        $this->assertEquals($verify, $lists);
    }

    public function testDeletePromotionVideoBrokenLinkTargetEmpty()
    {
        $musicRepository = app($this->musicRepositoryInterfaceName);

        $idValue = '000010a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($idValue);
        $result = $musicRepository->deletePromotionVideoBrokenLink($entityId);
        $this->assertTrue($result);
    }

    public function testDeletePromotionVideoBrokenLink()
    {
        $musicRepository = app($this->musicRepositoryInterfaceName);

        $idValue = '000010a1b2c3d4e5f6a7b8c9d';
        $promotionVideoBrokenLink = new PromotionVideoBrokenLink();
        $promotionVideoBrokenLink->fill(['music_id' => $idValue])->save();

        $entityId = new EntityId($idValue);
        $result = $musicRepository->deletePromotionVideoBrokenLink($entityId);
        $this->assertTrue($result);
        $rows = PromotionVideoBrokenLink::musicId($idValue)->get();
        $lists = [];
        foreach ($rows AS $row) {
            $lists[] = $row->music_id;
        }
        $this->assertEmpty($lists);
    }

    public function testDeletePromotionVideoBrokenLink2()
    {
        $musicRepository = app($this->musicRepositoryInterfaceName);

        $idValue = '000010a1b2c3d4e5f6a7b8c9d';
        $promotionVideoBrokenLink = new PromotionVideoBrokenLink();
        $promotionVideoBrokenLink->fill(['music_id' => $idValue])->save();
        $promotionVideoBrokenLink = new PromotionVideoBrokenLink();
        $promotionVideoBrokenLink->fill(['music_id' => $idValue])->save();

        $entityId = new EntityId($idValue);
        $result = $musicRepository->deletePromotionVideoBrokenLink($entityId);
        $this->assertTrue($result);
        $rows = PromotionVideoBrokenLink::musicId($idValue)->get();
        $lists = [];
        foreach ($rows AS $row) {
            $lists[] = $row->music_id;
        }
        $this->assertEmpty($lists);
    }

    public function testGetPhase()
    {
        $musicRepository = app($this->musicRepositoryInterfaceName);

        $idValue = '000090a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($idValue);
        $result = $musicRepository->getPhase($entityId);
        $this->assertNull($result);

        $idValue = '000010a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($idValue);
        $result = $musicRepository->getPhase($entityId);
        $this->assertTrue($result->isReleased());

        $idValue = '000050a1b2c3d4e5f6a7b8c9d';
        $entityId = new EntityId($idValue);
        $result = $musicRepository->getPhase($entityId);
        $this->assertTrue($result->isProvisioned());
    }

    public function testPromotionVideoBrokenLinksNotFound()
    {
        $musicRepository = app($this->musicRepositoryInterfaceName);

        $domainPaginator = $musicRepository->promotionVideoBrokenLinks();
        $this->assertEmpty($domainPaginator->getEntities());
        $this->assertEmpty($domainPaginator->getPaginator());
    }

    public function testPromotionVideoBrokenLinks1()
    {
        $musicRepository = app($this->musicRepositoryInterfaceName);

        $promotionVideoBrokenLink = new PromotionVideoBrokenLink();
        $promotionVideoBrokenLink->fill(['music_id' => '000010a1b2c3d4e5f6a7b8c9d'])->save();
        $promotionVideoBrokenLink = new PromotionVideoBrokenLink();
        $promotionVideoBrokenLink->fill(['music_id' => '000020a1b2c3d4e5f6a7b8c9d'])->save();
        $promotionVideoBrokenLink = new PromotionVideoBrokenLink();
        $promotionVideoBrokenLink->fill(['music_id' => '000050a1b2c3d4e5f6a7b8c9d'])->save();
        $promotionVideoBrokenLink = new PromotionVideoBrokenLink();
        $promotionVideoBrokenLink->fill(['music_id' => '000060a1b2c3d4e5f6a7b8c9d'])->save();

        $verify = ['000010a1b2c3d4e5f6a7b8c9d', '000020a1b2c3d4e5f6a7b8c9d', '000050a1b2c3d4e5f6a7b8c9d', '000060a1b2c3d4e5f6a7b8c9d'];
        $domainPaginator = $musicRepository->promotionVideoBrokenLinks();
        $musicEntities = $domainPaginator->getEntities();
        $musicIds = [];
        foreach ($musicEntities AS $musicEntity) {
            $musicIds[] = $musicEntity->id()->value();
        }
        sort($verify);
        sort($musicIds);
        $this->assertEquals($verify, $musicIds);
        $this->assertEquals($domainPaginator->getPaginator()->total(), 4);
    }

    public function testPromotionVideoBrokenLinks2()
    {
        $musicRepository = app($this->musicRepositoryInterfaceName);

        $promotionVideoBrokenLink = new PromotionVideoBrokenLink();
        $promotionVideoBrokenLink->fill(['music_id' => '000010a1b2c3d4e5f6a7b8c9d'])->save();
        $promotionVideoBrokenLink = new PromotionVideoBrokenLink();
        $promotionVideoBrokenLink->fill(['music_id' => '000020a1b2c3d4e5f6a7b8c9d'])->save();
        $promotionVideoBrokenLink = new PromotionVideoBrokenLink();
        $promotionVideoBrokenLink->fill(['music_id' => '000050a1b2c3d4e5f6a7b8c9d'])->save();
        $promotionVideoBrokenLink = new PromotionVideoBrokenLink();
        $promotionVideoBrokenLink->fill(['music_id' => '000060a1b2c3d4e5f6a7b8c9d'])->save();

        $verify = ['000010a1b2c3d4e5f6a7b8c9d', '000050a1b2c3d4e5f6a7b8c9d'];
        $entityIds = [new EntityId('000010a1b2c3d4e5f6a7b8c9d'), new EntityId('000050a1b2c3d4e5f6a7b8c9d')];
        $domainPaginator = $musicRepository->promotionVideoBrokenLinks($entityIds);
        $musicEntities = $domainPaginator->getEntities();
        $musicIds = [];
        foreach ($musicEntities AS $musicEntity) {
            $musicIds[] = $musicEntity->id()->value();
        }
        sort($verify);
        sort($musicIds);
        $this->assertEquals($verify, $musicIds);
        $this->assertEquals($domainPaginator->getPaginator()->total(), 2);
    }

    public function testProvisionedEntitiesNotFound()
    {
        $musicRepository = app($this->musicRepositoryInterfaceName);

        $iTunesArtistIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $musicTitleValue = 'Thunder';
        $iTunesArtistId = new ITunesArtistId($iTunesArtistIdValue);
        $musicTitle = new MusicTitle($musicTitleValue);
        $musicEntities = $musicRepository->provisionedEntities($iTunesArtistId, $musicTitle, new MusicSpecification());
        $this->assertEquals($musicEntities, []);
    }

    public function testProvisionedEntities1()
    {
        $musicRepository = app($this->musicRepositoryInterfaceName);

        $verify = [
            '000050a1b2c3d4e5f6a7b8c9d' =>  true    ,
            '000060a1b2c3d4e5f6a7b8c9d' =>  true    ,
            '000070a1b2c3d4e5f6a7b8c9d' =>  true    ,
            '000080a1b2c3d4e5f6a7b8c9d' =>  true    ,
        ];
        $musicEntities = $musicRepository->provisionedEntities(null, null, new MusicSpecification());
        $musicIds = [];
        foreach ($musicEntities AS $musicEntity) {
            $idValue = $musicEntity->id()->value();
            if (isset($verify[$idValue])) {
                $musicIds[$idValue] = true;
            }
        }
        sort($verify);
        sort($musicIds);
        $this->assertEquals($verify, $musicIds);
        $this->assertEquals(count($musicEntities), 10);
    }

    public function testProvisionedEntities2()
    {
        $musicRepository = app($this->musicRepositoryInterfaceName);

        $verify = ['000050a1b2c3d4e5f6a7b8c9d'];
        $iTunesArtistIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistId = new ITunesArtistId($iTunesArtistIdValue);
        $musicEntities = $musicRepository->provisionedEntities($iTunesArtistId, null, new MusicSpecification());
        $musicIds = [];
        foreach ($musicEntities AS $musicEntity) {
            $musicIds[] = $musicEntity->id()->value();
        }
        sort($verify);
        sort($musicIds);
        $this->assertEquals($verify, $musicIds);
        $this->assertEquals(count($musicEntities), 1);
    }

    public function testProvisionedEntities3()
    {
        $musicRepository = app($this->musicRepositoryInterfaceName);

        $verify = ['000050a1b2c3d4e5f6a7b8c9d'];
        $musicTitleValue = 'Gucci';
        $musicTitle = new MusicTitle($musicTitleValue);
        $musicEntities = $musicRepository->provisionedEntities(null, $musicTitle, new MusicSpecification());
        $musicIds = [];
        foreach ($musicEntities AS $musicEntity) {
            $musicIds[] = $musicEntity->id()->value();
        }
        sort($verify);
        sort($musicIds);
        $this->assertEquals($verify, $musicIds);
        $this->assertEquals(count($musicEntities), 1);
    }

    public function testProvisionedEntities4()
    {
        $musicRepository = app($this->musicRepositoryInterfaceName);

        $verify = ['000050a1b2c3d4e5f6a7b8c9d'];
        $iTunesArtistIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistId = new ITunesArtistId($iTunesArtistIdValue);
        $musicTitleValue = 'Gucci';
        $musicTitle = new MusicTitle($musicTitleValue);
        $musicEntities = $musicRepository->provisionedEntities($iTunesArtistId, $musicTitle, new MusicSpecification());
        $musicIds = [];
        foreach ($musicEntities AS $musicEntity) {
            $musicIds[] = $musicEntity->id()->value();
        }
        sort($verify);
        sort($musicIds);
        $this->assertEquals($verify, $musicIds);
        $this->assertEquals(count($musicEntities), 1);
    }

    public function testReleasedEntitiesNotFound()
    {
        $musicRepository = app($this->musicRepositoryInterfaceName);

        $iTunesArtistIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $musicTitleValue = 'Humble.';
        $iTunesArtistId = new ITunesArtistId($iTunesArtistIdValue);
        $musicTitle = new MusicTitle($musicTitleValue);
        $musicEntities = $musicRepository->releasedEntities($iTunesArtistId, $musicTitle, new MusicSpecification());
        $this->assertEquals($musicEntities, []);
    }

    public function testReleasedEntities1()
    {
        $musicRepository = app($this->musicRepositoryInterfaceName);

        $verify = [
            '000010a1b2c3d4e5f6a7b8c9d' =>  true    ,
            '000020a1b2c3d4e5f6a7b8c9d' =>  true    ,
            '000030a1b2c3d4e5f6a7b8c9d' =>  true    ,
            '000040a1b2c3d4e5f6a7b8c9d' =>  true    ,
        ];
        $musicEntities = $musicRepository->releasedEntities(null, null, new MusicSpecification());
        $musicIds = [];
        foreach ($musicEntities AS $musicEntity) {
            $idValue = $musicEntity->id()->value();
            if (isset($verify[$idValue])) {
                $musicIds[$idValue] = true;
            }
        }
        sort($verify);
        sort($musicIds);
        $this->assertEquals($verify, $musicIds);
        $this->assertEquals(count($musicEntities), 10);
    }

    public function testReleasedEntities2()
    {
        $musicRepository = app($this->musicRepositoryInterfaceName);

        $verify = ['000010a1b2c3d4e5f6a7b8c9d'];
        $iTunesArtistIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistId = new ITunesArtistId($iTunesArtistIdValue);
        $musicEntities = $musicRepository->releasedEntities($iTunesArtistId, null, new MusicSpecification());
        $musicIds = [];
        foreach ($musicEntities AS $musicEntity) {
            $musicIds[] = $musicEntity->id()->value();
        }
        sort($verify);
        sort($musicIds);
        $this->assertEquals($verify, $musicIds);
        $this->assertEquals(count($musicEntities), 1);
    }

    public function testReleasedEntities3()
    {
        $musicRepository = app($this->musicRepositoryInterfaceName);

        $verify = ['000010a1b2c3d4e5f6a7b8c9d'];
        $musicTitleValue = 'Of';
        $musicTitle = new MusicTitle($musicTitleValue);
        $musicEntities = $musicRepository->releasedEntities(null, $musicTitle, new MusicSpecification());
        $musicIds = [];
        foreach ($musicEntities AS $musicEntity) {
            $musicIds[] = $musicEntity->id()->value();
        }
        sort($verify);
        sort($musicIds);
        $this->assertEquals($verify, $musicIds);
        $this->assertEquals(count($musicEntities), 1);
    }

    public function testReleasedEntities4()
    {
        $musicRepository = app($this->musicRepositoryInterfaceName);

        $verify = ['000010a1b2c3d4e5f6a7b8c9d'];
        $iTunesArtistIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistId = new ITunesArtistId($iTunesArtistIdValue);
        $musicTitleValue = 'Of';
        $musicTitle = new MusicTitle($musicTitleValue);
        $musicEntities = $musicRepository->releasedEntities($iTunesArtistId, $musicTitle, new MusicSpecification());
        $musicIds = [];
        foreach ($musicEntities AS $musicEntity) {
            $musicIds[] = $musicEntity->id()->value();
        }
        sort($verify);
        sort($musicIds);
        $this->assertEquals($verify, $musicIds);
        $this->assertEquals(count($musicEntities), 1);
    }

    public function testProvisionedPaginatorNotFound()
    {
        $musicRepository = app($this->musicRepositoryInterfaceName);

        $iTunesArtistIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $musicTitleValue = 'Thunder';
        $iTunesArtistId = new ITunesArtistId($iTunesArtistIdValue);
        $musicTitle = new MusicTitle($musicTitleValue);
        $domainPaginator = $musicRepository->provisionedPaginator($iTunesArtistId, $musicTitle, new MusicSpecification());
        $this->assertEquals($domainPaginator->getEntities(), []);
        $this->assertEquals($domainPaginator->getPaginator()->total(), 0);
    }

    public function testProvisionedPaginator1()
    {
        $musicRepository = app($this->musicRepositoryInterfaceName);

        $verify = [
            '000050a1b2c3d4e5f6a7b8c9d' =>  true    ,
            '000060a1b2c3d4e5f6a7b8c9d' =>  true    ,
            '000070a1b2c3d4e5f6a7b8c9d' =>  true    ,
            '000080a1b2c3d4e5f6a7b8c9d' =>  true    ,
        ];
        $domainPaginator = $musicRepository->provisionedPaginator(null, null, new MusicSpecification());
        $musicEntities = $domainPaginator->getEntities();
        $musicIds = [];
        foreach ($musicEntities AS $musicEntity) {
            $idValue = $musicEntity->id()->value();
            if (isset($verify[$idValue])) {
                $musicIds[$idValue] = true;
            }
        }
        sort($verify);
        sort($musicIds);
        $this->assertEquals($verify, $musicIds);
        $this->assertEquals($domainPaginator->getPaginator()->total(), 10);
    }

    public function testProvisionedPaginator2()
    {
        $musicRepository = app($this->musicRepositoryInterfaceName);

        $verify = ['000050a1b2c3d4e5f6a7b8c9d'];
        $iTunesArtistIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistId = new ITunesArtistId($iTunesArtistIdValue);
        $domainPaginator = $musicRepository->provisionedPaginator($iTunesArtistId, null, new MusicSpecification());
        $musicEntities = $domainPaginator->getEntities();
        $musicIds = [];
        foreach ($musicEntities AS $musicEntity) {
            $musicIds[] = $musicEntity->id()->value();
        }
        sort($verify);
        sort($musicIds);
        $this->assertEquals($verify, $musicIds);
        $this->assertEquals($domainPaginator->getPaginator()->total(), 1);
    }

    public function testProvisionedPaginator3()
    {
        $musicRepository = app($this->musicRepositoryInterfaceName);

        $verify = ['000050a1b2c3d4e5f6a7b8c9d'];
        $musicTitleValue = 'Gucci';
        $musicTitle = new MusicTitle($musicTitleValue);
        $domainPaginator = $musicRepository->provisionedPaginator(null, $musicTitle, new MusicSpecification());
        $musicEntities = $domainPaginator->getEntities();
        $musicIds = [];
        foreach ($musicEntities AS $musicEntity) {
            $musicIds[] = $musicEntity->id()->value();
        }
        sort($verify);
        sort($musicIds);
        $this->assertEquals($verify, $musicIds);
        $this->assertEquals($domainPaginator->getPaginator()->total(), 1);
    }

    public function testProvisionedPaginator4()
    {
        $musicRepository = app($this->musicRepositoryInterfaceName);

        $verify = ['000050a1b2c3d4e5f6a7b8c9d'];
        $iTunesArtistIdValue = '000050a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistId = new ITunesArtistId($iTunesArtistIdValue);
        $musicTitleValue = 'Gucci';
        $musicTitle = new MusicTitle($musicTitleValue);
        $domainPaginator = $musicRepository->provisionedPaginator($iTunesArtistId, $musicTitle, new MusicSpecification());
        $musicEntities = $domainPaginator->getEntities();
        $musicIds = [];
        foreach ($musicEntities AS $musicEntity) {
            $musicIds[] = $musicEntity->id()->value();
        }
        sort($verify);
        sort($musicIds);
        $this->assertEquals($verify, $musicIds);
        $this->assertEquals($domainPaginator->getPaginator()->total(), 1);
    }

    public function testReleasedPaginatorNotFound()
    {
        $musicRepository = app($this->musicRepositoryInterfaceName);

        $iTunesArtistIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $musicTitleValue = 'Humble.';
        $iTunesArtistId = new ITunesArtistId($iTunesArtistIdValue);
        $musicTitle = new MusicTitle($musicTitleValue);
        $domainPaginator = $musicRepository->releasedPaginator($iTunesArtistId, $musicTitle, new MusicSpecification());
        $this->assertEquals($domainPaginator->getEntities(), []);
        $this->assertEquals($domainPaginator->getPaginator()->total(), 0);
    }

    public function testReleasedPaginator1()
    {
        $musicRepository = app($this->musicRepositoryInterfaceName);

        $verify = [
            '000010a1b2c3d4e5f6a7b8c9d' =>  true    ,
            '000020a1b2c3d4e5f6a7b8c9d' =>  true    ,
            '000030a1b2c3d4e5f6a7b8c9d' =>  true    ,
            '000040a1b2c3d4e5f6a7b8c9d' =>  true    ,
        ];
        $domainPaginator = $musicRepository->releasedPaginator(null, null, new MusicSpecification());
        $musicEntities = $domainPaginator->getEntities();
        $musicIds = [];
        foreach ($musicEntities AS $musicEntity) {
            $idValue = $musicEntity->id()->value();
            if (isset($verify[$idValue])) {
                $musicIds[$idValue] = true;
            }
        }
        sort($verify);
        sort($musicIds);
        $this->assertEquals($verify, $musicIds);
        $this->assertEquals($domainPaginator->getPaginator()->total(), 10);
    }

    public function testReleasedPaginator2()
    {
        $musicRepository = app($this->musicRepositoryInterfaceName);

        $verify = ['000010a1b2c3d4e5f6a7b8c9d'];
        $iTunesArtistIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistId = new ITunesArtistId($iTunesArtistIdValue);
        $domainPaginator = $musicRepository->releasedPaginator($iTunesArtistId, null, new MusicSpecification());
        $musicEntities = $domainPaginator->getEntities();
        $musicIds = [];
        foreach ($musicEntities AS $musicEntity) {
            $musicIds[] = $musicEntity->id()->value();
        }
        sort($verify);
        sort($musicIds);
        $this->assertEquals($verify, $musicIds);
        $this->assertEquals($domainPaginator->getPaginator()->total(), 1);
    }

    public function testReleasedPaginator3()
    {
        $musicRepository = app($this->musicRepositoryInterfaceName);

        $verify = ['000010a1b2c3d4e5f6a7b8c9d'];
        $musicTitleValue = 'Of';
        $musicTitle = new MusicTitle($musicTitleValue);
        $domainPaginator = $musicRepository->releasedPaginator(null, $musicTitle, new MusicSpecification());
        $musicEntities = $domainPaginator->getEntities();
        $musicIds = [];
        foreach ($musicEntities AS $musicEntity) {
            $musicIds[] = $musicEntity->id()->value();
        }
        sort($verify);
        sort($musicIds);
        $this->assertEquals($verify, $musicIds);
        $this->assertEquals($domainPaginator->getPaginator()->total(), 1);
    }

    public function testReleasedPaginator4()
    {
        $musicRepository = app($this->musicRepositoryInterfaceName);

        $verify = ['000010a1b2c3d4e5f6a7b8c9d'];
        $iTunesArtistIdValue = '000010a1b2c3d4e5f6a7b8c9d';
        $iTunesArtistId = new ITunesArtistId($iTunesArtistIdValue);
        $musicTitleValue = 'Of';
        $musicTitle = new MusicTitle($musicTitleValue);
        $domainPaginator = $musicRepository->releasedPaginator($iTunesArtistId, $musicTitle, new MusicSpecification());
        $musicEntities = $domainPaginator->getEntities();
        $musicIds = [];
        foreach ($musicEntities AS $musicEntity) {
            $musicIds[] = $musicEntity->id()->value();
        }
        sort($verify);
        sort($musicIds);
        $this->assertEquals($verify, $musicIds);
        $this->assertEquals($domainPaginator->getPaginator()->total(), 1);
    }

}
