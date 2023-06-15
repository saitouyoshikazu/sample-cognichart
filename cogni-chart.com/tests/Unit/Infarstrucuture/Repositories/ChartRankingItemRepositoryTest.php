<?php

namespace Tests\Unit\Infrastructure\Repositories;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use DB;
use App\Domain\EntityId;
use App\Domain\ValueObjects\ChartArtist;
use App\Domain\ValueObjects\ChartMusic;
use App\Domain\ChartRankingItem\ChartRankingItemBusinessId;
use App\Domain\ChartRankingItem\ChartRankingItemEntity;
use App\Domain\ChartRankingItem\ChartRankingItemSpecification;
use App\Domain\ChartRankingItem\ChartRankingItemException;
use App\Infrastructure\Eloquents\ChartRankingItem;

class ChartRankingItemRepositoryTest extends TestCase
{

    use DatabaseMigrations;

    private $redisDAOInterfaceName = 'App\Infrastructure\RedisDAO\RedisDAOInterface';
    private $chartRankingItemRepositoryInterfaceName = 'App\Domain\ChartRankingItem\ChartRankingItemRepositoryInterface';
    private $chartRankingItemFactoryInterfaceName = 'App\Domain\ChartRankingItem\ChartRankingItemFactoryInterface';

    public function setUp()
    {
        parent::setUp();

        factory(ChartRankingItem::class, 8)->create();
    }

    public function tearDown()
    {
        $redisDAO = app($this->redisDAOInterfaceName);
        $redisDAO->clear('*');
        $redisDAO->resetIsCache();

        ChartRankingItem::truncate();

        DB::disconnect();
    }

    public function testProvider()
    {
        $chartRankingItemRepository = app($this->chartRankingItemRepositoryInterfaceName);
        $this->assertEquals(get_class($chartRankingItemRepository), 'App\Infrastructure\Repositories\ChartRankingItemRepository');
    }

    public function testCreateId()
    {
        $chartRankingItemRepository = app($this->chartRankingItemRepositoryInterfaceName);

        $id = $chartRankingItemRepository->createId();
        $this->assertEquals(strlen($id->value()), 32);
    }

    public function testFind()
    {
        $chartRankingItemRepository = app($this->chartRankingItemRepositoryInterfaceName);
        $redisDAO = app($this->redisDAOInterfaceName);

        $idValue = '00000000000000000000000000000000';
        $id = new EntityId($idValue);
        $res = $chartRankingItemRepository->find($id);
        $this->assertNull($res);

        $idValue = '0123456789abcdef0123456789abcdef';
        $id = new EntityId($idValue);
        $res = $chartRankingItemRepository->find($id);
        $this->assertEquals($res->id()->value(), $idValue);
        $this->assertFalse($redisDAO->isCache());
        $chartRankingItemRepository->find($id);
        $this->assertFalse($redisDAO->isCache());
    }

    public function testFindWithCache()
    {
        $chartRankingItemRepository = app($this->chartRankingItemRepositoryInterfaceName);
        $redisDAO = app($this->redisDAOInterfaceName);
        $chartRankingItemSpecification = new ChartRankingItemSpecification();

        $idValue = '00000000000000000000000000000000';
        $entityId = new EntityId($idValue);
        $res = $chartRankingItemRepository->findWithCache($entityId, $chartRankingItemSpecification);
        $this->assertNull($res);

        $idValue = '0123456789abcdef0123456789abcdef';
        $entityId = new EntityId($idValue);
        $res = $chartRankingItemRepository->findWithCache($entityId, $chartRankingItemSpecification);
        $this->assertEquals($res->id()->value(), $idValue);
        $this->assertFalse($redisDAO->isCache());
        $chartRankingItemRepository->findWithCache($entityId, $chartRankingItemSpecification);
        $this->assertTrue($redisDAO->isCache());
    }

    public function testGet()
    {
        $chartRankingItemRepository = app($this->chartRankingItemRepositoryInterfaceName);
        $redisDAO = app($this->redisDAOInterfaceName);

        $chartArtistValue = 'Halsey';
        $chartMusicValue = 'Bad At Love';
        $chartArtist = new ChartArtist($chartArtistValue);
        $chartMusic = new ChartMusic($chartMusicValue);
        $chartRankingItemBusinessId = new ChartRankingItemBusinessId($chartArtist, $chartMusic);
        $res = $chartRankingItemRepository->get($chartRankingItemBusinessId);
        $this->assertNull($res);

        $idValue = '0123456789abcdef0123456789abcdef';
        $chartArtistValue = 'Ed Sheeran';
        $chartMusicValue = 'Shape Of You';
        $chartArtist = new ChartArtist($chartArtistValue);
        $chartMusic = new ChartMusic($chartMusicValue);
        $chartRankingItemBusinessId = new ChartRankingItemBusinessId($chartArtist, $chartMusic);
        $res = $chartRankingItemRepository->get($chartRankingItemBusinessId);
        $this->assertEquals($res->id()->value(), $idValue);
        $this->assertFalse($redisDAO->isCache());
        $chartRankingItemRepository->get($chartRankingItemBusinessId);
        $this->assertFalse($redisDAO->isCache());

        $id = new EntityId($idValue);
        $res = $chartRankingItemRepository->get($chartRankingItemBusinessId, $id);
        $this->assertNull($res);
    }

    public function testRefreshCachedEntity()
    {
        $chartRankingItemRepository = app($this->chartRankingItemRepositoryInterfaceName);
        $redisDAO = app($this->redisDAOInterfaceName);
        $chartRankingItemSpecification = new ChartRankingItemSpecification();

        $idValue = '0123456789abcdef0123456789abcdef';
        $chartArtistValue = 'Ed Sheeran';
        $chartMusicValue = 'Shape Of You';
        $entityId = new EntityId($idValue);
        $cacheKey = $chartRankingItemRepository->cacheKeyById($entityId, ChartRankingItemEntity::class);
        $redisDAO->set($cacheKey, '1');
        $chartRankingItemRepository->refreshCachedEntity($entityId, $chartRankingItemSpecification);
        $cachedEntity = $chartRankingItemRepository->findCacheById($entityId, ChartRankingItemEntity::class);
        $this->assertEquals($cachedEntity->id()->value(), $idValue);
    }

    public function testRegister()
    {
        $chartRankingItemRepository = app($this->chartRankingItemRepositoryInterfaceName);
        $chartRankingItemFactory = app($this->chartRankingItemFactoryInterfaceName);
        $chartRankingItemSpecification = new ChartRankingItemSpecification();

        $idValue = '0123456789abcdef0123456789abcdef';
        $id = new EntityId($idValue);
        $chartRankingItemEntity = $chartRankingItemRepository->find($id);
        $exception = false;
        try {
            $chartRankingItemRepository->register($chartRankingItemEntity, $chartRankingItemSpecification);
        } catch(\Exception $e) {
            $exception = true;
            $this->assertTrue($e instanceof ChartRankingItemException);
            $this->assertEquals($e->getMessage(), "Couldn't register ChartRankingItemEntity because ChartRankingItem is already existing.");
        }
        $this->assertTrue($exception);

        $id = $chartRankingItemRepository->createId();
        $chartArtistValue = 'Ed Sheeran';
        $chartMusicValue = 'Shape Of You';
        $chartRankingItemEntity = $chartRankingItemFactory->create(
            $id->value(),
            $chartArtistValue,
            $chartMusicValue
        );
        $exception = false;
        try {
            $chartRankingItemRepository->register($chartRankingItemEntity, $chartRankingItemSpecification);
        } catch(\Exception $e) {
            $exception = true;
            $this->assertTrue($e instanceof ChartRankingItemException);
            $this->assertEquals($e->getMessage(), "Couldn't register ChartRankingItemEntity because ChartRankingItem is already existing.");
        }
        $this->assertTrue($exception);

        $id = $chartRankingItemRepository->createId();
        $chartArtistValue = 'Halsey';
        $chartMusicValue = 'Bad At Love';
        $chartRankingItemEntity = $chartRankingItemFactory->create(
            $id->value(),
            $chartArtistValue,
            $chartMusicValue
        );
        $res = $chartRankingItemRepository->register($chartRankingItemEntity, $chartRankingItemSpecification);
        $this->assertTrue($res);
    }

    public function testModify()
    {
        $chartRankingItemRepository = app($this->chartRankingItemRepositoryInterfaceName);
        $chartRankingItemFactory = app($this->chartRankingItemFactoryInterfaceName);
        $chartRankingItemSpecification = new ChartRankingItemSpecification();

        $idValue = '00000000000000000000000000000000';
        $id = new EntityId($idValue);
        $chartArtistValue = 'Halsey';
        $chartMusicValue = 'Bad At Love';
        $chartRankingItemEntity = $chartRankingItemFactory->create(
            $id->value(),
            $chartArtistValue,
            $chartMusicValue
        );
        $exception = false;
        try {
            $chartRankingItemRepository->modify($chartRankingItemEntity, $chartRankingItemSpecification);
        } catch(\Exception $e) {
            $exception = true;
            $this->assertTrue($e instanceof ChartRankingItemException);
            $this->assertEquals($e->getMessage(), "Couldn't modify ChartRankingItemEntity because ChartRankingItem doesn't exist.");
        }
        $this->assertTrue($exception);

        $idValue = '0123456789abcdef0123456789abcdef';
        $id = new EntityId($idValue);
        $chartRankingItemEntity = $chartRankingItemRepository->find($id);
        $chartArtistValue = 'Kendrick Lamar';
        $chartMusicValue = 'Humble.';
        $chartArtist = new ChartArtist($chartArtistValue);
        $chartMusic = new ChartMusic($chartMusicValue);
        $chartRankingItemEntity->setChartArtist($chartArtist)->setChartMusic($chartMusic);
        $exception = false;
        try {
            $chartRankingItemRepository->modify($chartRankingItemEntity, $chartRankingItemSpecification);
        } catch(\Exception $e) {
            $exception = true;
            $this->assertTrue($e instanceof ChartRankingItemException);
            $this->assertEquals($e->getMessage(), "Couldn't modify ChartRankingItemEntity because ChartRankingItem is already existing.");
        }
        $this->assertTrue($exception);

        $idValue = '0123456789abcdef0123456789abcdef';
        $id = new EntityId($idValue);
        $chartRankingItemEntity = $chartRankingItemRepository->find($id);
        $chartRankingItemEntity->setArtistId(new EntityId('00000000000000000000000000000000'));
        $res = $chartRankingItemRepository->modify($chartRankingItemEntity, $chartRankingItemSpecification);
        $this->assertTrue($res);
    }

    public function testDelete()
    {
        $chartRankingItemRepository = app($this->chartRankingItemRepositoryInterfaceName);
        $chartRankingItemSpecification = new ChartRankingItemSpecification();

        $idValue = '00000000000000000000000000000000';
        $id = new EntityId($idValue);
        $exception = false;
        try {
            $chartRankingItemRepository->delete($id, $chartRankingItemSpecification);
        } catch(\Exception $e) {
            $exception = true;
            $this->assertTrue($e instanceof ChartRankingItemException);
            $this->assertEquals($e->getMessage(), "Couldn't delete ChartRankingItemEntity because ChartRankingItem doesn't exist.");
        }
        $this->assertTrue($exception);

        $idValue = '0123456789abcdef0123456789abcdef';
        $id = new EntityId($idValue);
        $res = $chartRankingItemRepository->delete($id, $chartRankingItemSpecification);
        $this->assertTrue($res);
    }

    public function testEntitiesNotFound()
    {
        $chartRankingItemRepository = app($this->chartRankingItemRepositoryInterfaceName);

        $artistIdValue = '000080a1b2c3d4e5f6a7b8c9d';
        $musicIdValue = '000070a1b2c3d4e5f6a7b8c9d';
        $artistId = new EntityId($musicIdValue);
        $musicId = new EntityId($artistIdValue);
        $result = $chartRankingItemRepository->entities(null, null, $artistId, $musicId, new ChartRankingItemSpecification());
        $this->assertEquals($result, []);
    }

    public function testEntities1()
    {
        $chartRankingItemRepository = app($this->chartRankingItemRepositoryInterfaceName);

        $verify = [
            '0123456789abcdef0123456789abcdef' => true,
            '1123456789abcdef0123456789abcdef' => true,
            '2123456789abcdef0123456789abcdef' => true,
            '3123456789abcdef0123456789abcdef' => true,
            '4123456789abcdef0123456789abcdef' => true,
            '5123456789abcdef0123456789abcdef' => true,
            '6123456789abcdef0123456789abcdef' => true,
            '7123456789abcdef0123456789abcdef' => true,
        ];
        $chartRankingItemEntities = $chartRankingItemRepository->entities(null, null, null, null, new ChartRankingItemSpecification());
        $entityIds = [];
        foreach ($chartRankingItemEntities AS $chartRankingItemEntity) {
            $idValue = $chartRankingItemEntity->id()->value();
            if (isset($verify[$idValue])) {
                $entityIds[$idValue] = true;
            }
        }
        sort($verify);
        sort($entityIds);
        $this->assertEquals($verify, $entityIds);
        $this->assertEquals(count($chartRankingItemEntities), 8);
    }

    public function testEntities2()
    {
        $chartRankingItemRepository = app($this->chartRankingItemRepositoryInterfaceName);

        $verify = ['0123456789abcdef0123456789abcdef'];
        $chartArtistValue = 'Ed';
        $chartArtist = new ChartArtist($chartArtistValue);
        $chartRankingItemEntities = $chartRankingItemRepository->entities($chartArtist, null, null, null, new ChartRankingItemSpecification());
        $entityIds = [];
        foreach ($chartRankingItemEntities AS $chartRankingItemEntity) {
            $entityIds[] = $chartRankingItemEntity->id()->value();
        }
        sort($verify);
        sort($entityIds);
        $this->assertEquals($verify, $entityIds);
        $this->assertEquals(count($chartRankingItemEntities), 1);
    }

    public function testEntities3()
    {
        $chartRankingItemRepository = app($this->chartRankingItemRepositoryInterfaceName);

        $verify = ['0123456789abcdef0123456789abcdef'];
        $chartMusicValue = 'Of';
        $chartMusic = new ChartMusic($chartMusicValue);
        $chartRankingItemEntities = $chartRankingItemRepository->entities(null, $chartMusic, null, null, new ChartRankingItemSpecification());
        $entityIds = [];
        foreach ($chartRankingItemEntities AS $chartRankingItemEntity) {
            $entityIds[] = $chartRankingItemEntity->id()->value();
        }
        sort($verify);
        sort($entityIds);
        $this->assertEquals($verify, $entityIds);
        $this->assertEquals(count($chartRankingItemEntities), 1);
    }

    public function testEntities4()
    {
        $chartRankingItemRepository = app($this->chartRankingItemRepositoryInterfaceName);

        $verify = ['0123456789abcdef0123456789abcdef'];
        $artistIdValue = '0123456789abcdef0123456789abcdef';
        $artistId = new EntityId($artistIdValue);
        $chartRankingItemEntities = $chartRankingItemRepository->entities(null, null, $artistId, null, new ChartRankingItemSpecification());
        $entityIds = [];
        foreach ($chartRankingItemEntities AS $chartRankingItemEntity) {
            $entityIds[] = $chartRankingItemEntity->id()->value();
        }
        sort($verify);
        sort($entityIds);
        $this->assertEquals($verify, $entityIds);
        $this->assertEquals(count($chartRankingItemEntities), 1);
    }

    public function testEntities5()
    {
        $chartRankingItemRepository = app($this->chartRankingItemRepositoryInterfaceName);

        $verify = ['0123456789abcdef0123456789abcdef'];
        $musicIdValue = '0123456789abcdef0123456789abcdef';
        $musicId = new EntityId($musicIdValue);
        $chartRankingItemEntities = $chartRankingItemRepository->entities(null, null, null, $musicId, new ChartRankingItemSpecification());
        $entityIds = [];
        foreach ($chartRankingItemEntities AS $chartRankingItemEntity) {
            $entityIds[] = $chartRankingItemEntity->id()->value();
        }
        sort($verify);
        sort($entityIds);
        $this->assertEquals($verify, $entityIds);
        $this->assertEquals(count($chartRankingItemEntities), 1);
    }

    public function testEntities6()
    {
        $chartRankingItemRepository = app($this->chartRankingItemRepositoryInterfaceName);

        $verify = ['0123456789abcdef0123456789abcdef'];
        $chartArtistValue = 'Ed';
        $chartMusicValue = 'Of';
        $artistIdValue = '0123456789abcdef0123456789abcdef';
        $musicIdValue = '0123456789abcdef0123456789abcdef';
        $chartArtist = new ChartArtist($chartArtistValue);
        $chartMusic = new ChartMusic($chartMusicValue);
        $artistId = new EntityId($artistIdValue);
        $musicId = new EntityId($musicIdValue);
        $chartRankingItemEntities = $chartRankingItemRepository->entities($chartArtist, $chartMusic, $artistId, $musicId, new ChartRankingItemSpecification());
        $entityIds = [];
        foreach ($chartRankingItemEntities AS $chartRankingItemEntity) {
            $entityIds[] = $chartRankingItemEntity->id()->value();
        }
        sort($verify);
        sort($entityIds);
        $this->assertEquals($verify, $entityIds);
        $this->assertEquals(count($chartRankingItemEntities), 1);
    }

    public function testNotAttachedPaginatorNotFound()
    {
        $chartRankingItemRepository = app($this->chartRankingItemRepositoryInterfaceName);

        $chartArtistValue = 'Ed';
        $chartMusicValue = 'Humble.';
        $chartArtist = new ChartArtist($chartArtistValue);
        $chartMusic = new ChartMusic($chartMusicValue);
        $domainPaginator = $chartRankingItemRepository->notAttachedPaginator($chartArtist, $chartMusic, new ChartRankingItemSpecification());
        $this->assertEquals($domainPaginator->getEntities(), []);
        $this->assertEquals($domainPaginator->getPaginator()->total(), 0);
    }

    public function testNotAttachedPaginator1()
    {
        $chartRankingItemRepository = app($this->chartRankingItemRepositoryInterfaceName);

        $verify = [
            '1123456789abcdef0123456789abcdef' => true,
            '2123456789abcdef0123456789abcdef' => true,
            '3123456789abcdef0123456789abcdef' => true,
            '5123456789abcdef0123456789abcdef' => true,
            '6123456789abcdef0123456789abcdef' => true,
            '7123456789abcdef0123456789abcdef' => true,
        ];
        $domainPaginator = $chartRankingItemRepository->notAttachedPaginator(null, null, new ChartRankingItemSpecification());
        $chartRankingItemEntities = $domainPaginator->getEntities();
        $entityIds = [];
        foreach ($chartRankingItemEntities AS $chartRankingItemEntity) {
            $idValue = $chartRankingItemEntity->id()->value();
            if (isset($verify[$idValue])) {
                $entityIds[$idValue] = true;
            }
        }
        sort($verify);
        sort($entityIds);
        $this->assertEquals($verify, $entityIds);
        $this->assertEquals($domainPaginator->getPaginator()->total(), 6);
    }

    public function testNotAttachedPaginator2()
    {
        $chartRankingItemRepository = app($this->chartRankingItemRepositoryInterfaceName);
        $entityIdValue = '4123456789abcdef0123456789abcdef';

        $chartRankingItem = ChartRankingItem::find($entityIdValue);
        $chartRankingItem->artist_id = '';
        $chartRankingItem->save();

        $verify = [$entityIdValue];
        $chartArtistValue = 'Pump';
        $chartArtist = new ChartArtist($chartArtistValue);
        $domainPaginator = $chartRankingItemRepository->notAttachedPaginator($chartArtist, null, new ChartRankingItemSpecification());
        $chartRankingItemEntities = $domainPaginator->getEntities();
        $entityIds = [];
        foreach ($chartRankingItemEntities AS $chartRankingItemEntity) {
            $idValue = $chartRankingItemEntity->id()->value();
            $entityIds[] = $idValue;
        }
        sort($verify);
        sort($entityIds);
        $this->assertEquals($verify, $entityIds);
        $this->assertEquals($domainPaginator->getPaginator()->total(), 1);
    }

    public function testNotAttachedPaginator3()
    {
        $chartRankingItemRepository = app($this->chartRankingItemRepositoryInterfaceName);
        $entityIdValue = '4123456789abcdef0123456789abcdef';

        $chartRankingItem = ChartRankingItem::find($entityIdValue);
        $chartRankingItem->music_id = '';
        $chartRankingItem->save();

        $verify = [$entityIdValue];
        $chartMusicValue = 'Gucci';
        $chartMusic = new ChartMusic($chartMusicValue);
        $domainPaginator = $chartRankingItemRepository->notAttachedPaginator(null, $chartMusic, new ChartRankingItemSpecification());
        $chartRankingItemEntities = $domainPaginator->getEntities();
        $entityIds = [];
        foreach ($chartRankingItemEntities AS $chartRankingItemEntity) {
            $idValue = $chartRankingItemEntity->id()->value();
            $entityIds[] = $idValue;
        }
        sort($verify);
        sort($entityIds);
        $this->assertEquals($verify, $entityIds);
        $this->assertEquals($domainPaginator->getPaginator()->total(), 1);
    }

    public function testNotAttachedPaginator4()
    {
        $chartRankingItemRepository = app($this->chartRankingItemRepositoryInterfaceName);
        $entityIdValue = '4123456789abcdef0123456789abcdef';

        $chartRankingItem = ChartRankingItem::find($entityIdValue);
        $chartRankingItem->artist_id = '';
        $chartRankingItem->music_id = '';
        $chartRankingItem->save();

        $verify = [$entityIdValue];
        $chartArtistValue = 'Pump';
        $chartMusicValue = 'Gucci';
        $chartArtist = new ChartArtist($chartArtistValue);
        $chartMusic = new ChartMusic($chartMusicValue);
        $domainPaginator = $chartRankingItemRepository->notAttachedPaginator($chartArtist, $chartMusic, new ChartRankingItemSpecification());
        $chartRankingItemEntities = $domainPaginator->getEntities();
        $entityIds = [];
        foreach ($chartRankingItemEntities AS $chartRankingItemEntity) {
            $idValue = $chartRankingItemEntity->id()->value();
            $entityIds[] = $idValue;
        }
        sort($verify);
        sort($entityIds);
        $this->assertEquals($verify, $entityIds);
        $this->assertEquals($domainPaginator->getPaginator()->total(), 1);
    }

}
