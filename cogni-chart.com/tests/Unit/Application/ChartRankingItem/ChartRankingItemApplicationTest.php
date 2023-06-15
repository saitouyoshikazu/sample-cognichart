<?php

namespace Tests\Unit\Application\ChartRankingItem;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Infrastructure\Eloquents\ChartRankingItem;
use Mockery;
use Event;
use DB;
use App\Application\ChartRankingItem\ChartRankingItemApplication;
use App\Application\DXO\ChartRankingItemDXO;
use App\Domain\EntityId;
use App\Domain\ChartRankingItem\ChartRankingItemEntity;
use App\Domain\ChartRankingItem\ChartRankingItemException;

class ChartRankingItemApplicationTest extends TestCase
{

    use DatabaseMigrations;

    private $redisDAOInterfaceName = 'App\Infrastructure\RedisDAO\RedisDAOInterface';
    private $chartRankingItemApplicationInterfaceName = 'App\Application\ChartRankingItem\ChartRankingItemApplicationInterface';
    private $chartRankingItemRepositoryInterfaceName = 'App\Domain\ChartRankingItem\ChartRankingItemRepositoryInterface';
    private $chartRankingItemFactoryInterfaceName = 'App\Domain\ChartRankingItem\ChartRankingItemFactoryInterface';

    public function setUp()
    {
        parent::setUp();

        factory(ChartRankingItem::class, 10)->create();
    }

    public function tearDown()
    {
        Mockery::close();

        $redisDAO = app($this->redisDAOInterfaceName);
        $redisDAO->clear('*');
        $redisDAO->resetIsCache();

        ChartRankingItem::truncate();

        DB::disconnect();
    }

    private function chartRankingItemFactoryMock()
    {
        return Mockery::mock('App\Domain\ChartRankingItem\ChartRankingItemFactory')->makePartial();
    }

    private function chartRankingItemRepositoryMock()
    {
        return Mockery::mock(
            'App\Infrastructure\Repositories\ChartRankingItemRepository',
            [
                app($this->redisDAOInterfaceName),
                app($this->chartRankingItemFactoryInterfaceName)
            ]
        )->makePartial();
    }

    public function testProvider()
    {
        $chartRankingItemApplication = app($this->chartRankingItemApplicationInterfaceName);
        $this->assertEquals(get_class($chartRankingItemApplication), ChartRankingItemApplication::class);
    }

    public function testExistsEmptyParameters()
    {
        $chartRankingItemApplication = app($this->chartRankingItemApplicationInterfaceName);

        $chartArtistValue = '';
        $chartMusicValue = 'Shape Of You';
        $chartRankingItemDXO = new ChartRankingItemDXO();
        $chartRankingItemDXO->exists($chartArtistValue, $chartMusicValue);
        $result = $chartRankingItemApplication->exists($chartRankingItemDXO);
        $this->assertFalse($result);

        $chartArtistValue = 'Ed Sheeran';
        $chartMusicValue = '';
        $chartRankingItemDXO = new ChartRankingItemDXO();
        $chartRankingItemDXO->exists($chartArtistValue, $chartMusicValue);
        $result = $chartRankingItemApplication->exists($chartRankingItemDXO);
        $this->assertFalse($result);
    }

    public function testExists()
    {
        $chartRankingItemApplication = app($this->chartRankingItemApplicationInterfaceName);

        $chartArtistValue = 'Ed Sheeran';
        $chartMusicValue = 'Shape Of You';
        $chartRankingItemDXO = new ChartRankingItemDXO();
        $chartRankingItemDXO->exists($chartArtistValue, $chartMusicValue);
        $result = $chartRankingItemApplication->exists($chartRankingItemDXO);
        $this->assertTrue($result);

        $chartArtistValue = 'Halsey';
        $chartMusicValue = 'Bad At Love';
        $chartRankingItemDXO = new ChartRankingItemDXO();
        $chartRankingItemDXO->exists($chartArtistValue, $chartMusicValue);
        $result = $chartRankingItemApplication->exists($chartRankingItemDXO);
        $this->assertFalse($result);
    }

    public function testFindEmptyParameters()
    {
        $chartRankingItemApplication = app($this->chartRankingItemApplicationInterfaceName);

        $entityIdValue = '';
        $chartRankingItemDXO = new ChartRankingItemDXO();
        $chartRankingItemDXO->find($entityIdValue);
        $result = $chartRankingItemApplication->find($chartRankingItemDXO);
        $this->assertNull($result);
    }

    public function testFind()
    {
        $chartRankingItemApplication = app($this->chartRankingItemApplicationInterfaceName);

        $entityIdValue = '00000000000000000000000000000000';
        $chartRankingItemDXO = new ChartRankingItemDXO();
        $chartRankingItemDXO->find($entityIdValue);
        $result = $chartRankingItemApplication->find($chartRankingItemDXO);
        $this->assertNull($result);

        $entityIdValue = '0123456789abcdef0123456789abcdef';
        $chartRankingItemDXO = new ChartRankingItemDXO();
        $chartRankingItemDXO->find($entityIdValue);
        $result = $chartRankingItemApplication->find($chartRankingItemDXO);
        $this->assertEquals($result->id()->value(), $entityIdValue);
    }

    public function testRegisterEmptyParameters()
    {
        $chartRankingItemApplication = app($this->chartRankingItemApplicationInterfaceName);

        $chartArtistValue = '';
        $chartMusicValue = 'Shape Of You';
        $artistIdValue = null;
        $musicIdValue = null;
        $chartRankingItemDXO = new ChartRankingItemDXO();
        $chartRankingItemDXO->register($chartArtistValue, $chartMusicValue, $artistIdValue, $musicIdValue);
        $result = $chartRankingItemApplication->register($chartRankingItemDXO);
        $this->assertFalse($result);

        $chartArtistValue = 'Ed Sheeran';
        $chartMusicValue = '';
        $artistIdValue = null;
        $musicIdValue = null;
        $chartRankingItemDXO = new ChartRankingItemDXO();
        $chartRankingItemDXO->register($chartArtistValue, $chartMusicValue, $artistIdValue, $musicIdValue);
        $result = $chartRankingItemApplication->register($chartRankingItemDXO);
        $this->assertFalse($result);
    }

    public function testRegisterFactoryCreateEmpty()
    {
        $chartRankingItemFactoryMock = $this->chartRankingItemFactoryMock();
        $chartRankingItemFactoryMock->shouldReceive('create')->andReturn(null);
        $chartRankingItemApplication = new ChartRankingItemApplication(
            app('App\Domain\ChartRankingItem\ChartRankingItemRepositoryInterface'),
            $chartRankingItemFactoryMock
        );

        $chartArtistValue = 'Halsey';
        $chartMusicValue = 'Bad At Love';
        $artistIdValue = null;
        $musicIdValue = null;
        $chartRankingItemDXO = new ChartRankingItemDXO();
        $chartRankingItemDXO->register($chartArtistValue, $chartMusicValue, $artistIdValue, $musicIdValue);
        $result = $chartRankingItemApplication->register($chartRankingItemDXO);
        $this->assertFalse($result);
    }

    public function testRegisterRepositoryReturnFalse()
    {
        $chartRankingItemRepositoryMock = $this->chartRankingItemRepositoryMock();
        $chartRankingItemRepositoryMock->shouldReceive('register')->andReturn(false);
        $chartRankingItemApplication = new ChartRankingItemApplication(
            $chartRankingItemRepositoryMock,
            app('App\Domain\ChartRankingItem\ChartRankingItemFactoryInterface')
        );

        $chartArtistValue = 'Halsey';
        $chartMusicValue = 'Bad At Love';
        $artistIdValue = null;
        $musicIdValue = null;
        $chartRankingItemDXO = new ChartRankingItemDXO();
        $chartRankingItemDXO->register($chartArtistValue, $chartMusicValue, $artistIdValue, $musicIdValue);
        $result = $chartRankingItemApplication->register($chartRankingItemDXO);
        $this->assertFalse($result);
    }

    /**
     * @expectedException App\Domain\ChartRankingItem\ChartRankingItemException
     */
    public function testRegisterExceptionOccurred()
    {
        $chartRankingItemApplication = app($this->chartRankingItemApplicationInterfaceName);

        $chartArtistValue = 'Ed Sheeran';
        $chartMusicValue = 'Shape Of You';
        $artistIdValue = null;
        $musicIdValue = null;
        $chartRankingItemDXO = new ChartRankingItemDXO();
        $chartRankingItemDXO->register($chartArtistValue, $chartMusicValue, $artistIdValue, $musicIdValue);
        $chartRankingItemApplication->register($chartRankingItemDXO);
    }

    public function testRegister()
    {
        $eventPublished = false;
        Event::shouldReceive('dispatch')->andReturnUsing(
            function ($event) use (&$eventPublished) {
                $eventName = 'App\Events\ChartRankingItemCreated';
                if ($event instanceof $eventName) {
                    $eventPublished = true;
                }
            }
        );
        $chartRankingItemApplication = app($this->chartRankingItemApplicationInterfaceName);

        $eventPublished = false;
        $chartArtistValue = 'Halsey';
        $chartMusicValue = 'Bad At Love';
        $artistIdValue = null;
        $musicIdValue = null;
        $chartRankingItemDXO = new ChartRankingItemDXO();
        $chartRankingItemDXO->register($chartArtistValue, $chartMusicValue, $artistIdValue, $musicIdValue);
        $result = $chartRankingItemApplication->register($chartRankingItemDXO);
        $this->assertTrue($result);
        $this->assertTrue($eventPublished);

        $delIds = ChartRankingItem::businessId($chartArtistValue, $chartMusicValue)->pluck('id');
        ChartRankingItem::destroy($delIds);

        $eventPublished = false;
        $chartArtistValue = 'Halsey';
        $chartMusicValue = 'Bad At Love';
        $artistIdValue = '9999';
        $musicIdValue = null;
        $chartRankingItemDXO = new ChartRankingItemDXO();
        $chartRankingItemDXO->register($chartArtistValue, $chartMusicValue, $artistIdValue, $musicIdValue);
        $result = $chartRankingItemApplication->register($chartRankingItemDXO);
        $this->assertTrue($result);
        $this->assertTrue($eventPublished);

        $delIds = ChartRankingItem::businessId($chartArtistValue, $chartMusicValue)->pluck('id');
        ChartRankingItem::destroy($delIds);

        $eventPublished = false;
        $chartArtistValue = 'Halsey';
        $chartMusicValue = 'Bad At Love';
        $artistIdValue = null;
        $musicIdValue = '9999';
        $chartRankingItemDXO = new ChartRankingItemDXO();
        $chartRankingItemDXO->register($chartArtistValue, $chartMusicValue, $artistIdValue, $musicIdValue);
        $result = $chartRankingItemApplication->register($chartRankingItemDXO);
        $this->assertTrue($result);
        $this->assertTrue($eventPublished);

        $delIds = ChartRankingItem::businessId($chartArtistValue, $chartMusicValue)->pluck('id');
        ChartRankingItem::destroy($delIds);

        $eventPublished = false;
        $chartArtistValue = 'Halsey';
        $chartMusicValue = 'Bad At Love';
        $artistIdValue = '9999';
        $musicIdValue = '9999';
        $chartRankingItemDXO = new ChartRankingItemDXO();
        $chartRankingItemDXO->register($chartArtistValue, $chartMusicValue, $artistIdValue, $musicIdValue);
        $result = $chartRankingItemApplication->register($chartRankingItemDXO);
        $this->assertTrue($result);
        $this->assertFalse($eventPublished);
    }

    public function testGetEmptyParameters()
    {
        $chartRankingItemApplication = app($this->chartRankingItemApplicationInterfaceName);

        $chartArtistValue = '';
        $chartMusicValue = 'Shape Of You';
        $chartRankingItemDXO = new ChartRankingItemDXO();
        $chartRankingItemDXO->get($chartArtistValue, $chartMusicValue);
        $result = $chartRankingItemApplication->get($chartRankingItemDXO);
        $this->assertNull($result);

        $chartArtistValue = 'Ed Sheeran';
        $chartMusicValue = '';
        $chartRankingItemDXO = new ChartRankingItemDXO();
        $chartRankingItemDXO->get($chartArtistValue, $chartMusicValue);
        $result = $chartRankingItemApplication->get($chartRankingItemDXO);
        $this->assertNull($result);
    }

    public function testGet()
    {
        $chartRankingItemApplication = app($this->chartRankingItemApplicationInterfaceName);

        $chartArtistValue = 'Halsey';
        $chartMusicValue = 'Bad At Love';
        $chartRankingItemDXO = new ChartRankingItemDXO();
        $chartRankingItemDXO->get($chartArtistValue, $chartMusicValue);
        $result = $chartRankingItemApplication->get($chartRankingItemDXO);
        $this->assertNull($result);

        $chartArtistValue = 'Ed Sheeran';
        $chartMusicValue = 'Shape Of You';
        $chartRankingItemDXO = new ChartRankingItemDXO();
        $chartRankingItemDXO->get($chartArtistValue, $chartMusicValue);
        $result = $chartRankingItemApplication->get($chartRankingItemDXO);
        $this->assertEquals($result->id()->value(), '0123456789abcdef0123456789abcdef');
        $this->assertEquals($result->chartArtist()->value(), $chartArtistValue);
        $this->assertEquals($result->chartMusic()->value(), $chartMusicValue);
    }

    public function testModifyEmptyParameters()
    {
        $chartRankingItemApplication = app($this->chartRankingItemApplicationInterfaceName);

        $entityIdValue = '';
        $chartArtistValue = 'Ed Sheeran';
        $chartMusicValue = 'Shape Of You';
        $artistIdValue = null;
        $musicIdValue = null;
        $chartRankingItemDXO = new ChartRankingItemDXO();
        $chartRankingItemDXO->modify($entityIdValue, $chartArtistValue, $chartMusicValue, $artistIdValue, $musicIdValue);
        $result = $chartRankingItemApplication->modify($chartRankingItemDXO);
        $this->assertFalse($result);

        $entityIdValue = '0123456789abcdef0123456789abcdef';
        $chartArtistValue = '';
        $chartMusicValue = 'Shape Of You';
        $artistIdValue = null;
        $musicIdValue = null;
        $chartRankingItemDXO = new ChartRankingItemDXO();
        $chartRankingItemDXO->modify($entityIdValue, $chartArtistValue, $chartMusicValue, $artistIdValue, $musicIdValue);
        $result = $chartRankingItemApplication->modify($chartRankingItemDXO);
        $this->assertFalse($result);

        $entityIdValue = '0123456789abcdef0123456789abcdef';
        $chartArtistValue = 'Ed Sheeran';
        $chartMusicValue = '';
        $artistIdValue = null;
        $musicIdValue = null;
        $chartRankingItemDXO = new ChartRankingItemDXO();
        $chartRankingItemDXO->modify($entityIdValue, $chartArtistValue, $chartMusicValue, $artistIdValue, $musicIdValue);
        $result = $chartRankingItemApplication->modify($chartRankingItemDXO);
        $this->assertFalse($result);
    }

    public function testModifyEntityNotFound()
    {
        $chartRankingItemApplication = app($this->chartRankingItemApplicationInterfaceName);

        $entityIdValue = '00000000000000000000000000000000';
        $chartArtistValue = 'Halsey';
        $chartMusicValue = 'Bad At Love';
        $artistIdValue = null;
        $musicIdValue = null;
        $chartRankingItemDXO = new ChartRankingItemDXO();
        $chartRankingItemDXO->modify($entityIdValue, $chartArtistValue, $chartMusicValue, $artistIdValue, $musicIdValue);
        $result = $chartRankingItemApplication->modify($chartRankingItemDXO);
        $this->assertFalse($result);
    }

    public function testModifyRepositoryReturnFalse()
    {
        $chartRankingItemRepositoryMock = $this->chartRankingItemRepositoryMock();
        $chartRankingItemRepositoryMock->shouldReceive('modify')->andReturn(false);
        $chartRankingItemApplication = new ChartRankingItemApplication(
            $chartRankingItemRepositoryMock,
            app($this->chartRankingItemFactoryInterfaceName)
        );

        $entityIdValue = '0123456789abcdef0123456789abcdef';
        $chartArtistValue = 'Ed Sheeran';
        $chartMusicValue = 'Shape Of You';
        $artistIdValue = null;
        $musicIdValue = null;
        $chartRankingItemDXO = new ChartRankingItemDXO();
        $chartRankingItemDXO->modify($entityIdValue, $chartArtistValue, $chartMusicValue, $artistIdValue, $musicIdValue);
        $result = $chartRankingItemApplication->modify($chartRankingItemDXO);
        $this->assertFalse($result);
    }

    /**
     * @expectedException App\Domain\ChartRankingItem\ChartRankingItemException
     */
    public function testModifyExceptionOccurred()
    {
        $chartRankingItemApplication = app($this->chartRankingItemApplicationInterfaceName);

        $entityIdValue = '0123456789abcdef0123456789abcdef';
        $chartArtistValue = 'Kendrick Lamar';
        $chartMusicValue = 'Humble.';
        $artistIdValue = null;
        $musicIdValue = null;
        $chartRankingItemDXO = new ChartRankingItemDXO();
        $chartRankingItemDXO->modify($entityIdValue, $chartArtistValue, $chartMusicValue, $artistIdValue, $musicIdValue);
        $chartRankingItemApplication->modify($chartRankingItemDXO);
    }

    public function testModify()
    {
        $eventPublished = false;
        Event::shouldReceive('dispatch')->andReturnUsing(
            function ($event) use (&$eventPublished)
            {
                $eventName = 'App\Events\ChartRankingItemModified';
                if ($event instanceOf $eventName) {
                    $eventPublished = true;
                }
            }
        );
        $chartRankingItemApplication = app($this->chartRankingItemApplicationInterfaceName);
        $chartRankingItemRepository = app($this->chartRankingItemRepositoryInterfaceName);

        $entityIdValue = '0123456789abcdef0123456789abcdef';
        $chartArtistValue = 'Ed Sheeran +';
        $chartMusicValue = 'Shape Of You +';
        $artistIdValue = '99999';
        $musicIdValue = '88888';
        $chartRankingItemDXO = new ChartRankingItemDXO();
        $chartRankingItemDXO->modify($entityIdValue, $chartArtistValue, $chartMusicValue, $artistIdValue, $musicIdValue);
        $result = $chartRankingItemApplication->modify($chartRankingItemDXO);
        $this->assertTrue($result);
        $this->assertTrue($eventPublished);
        $entityId = new EntityId($entityIdValue);
        $modifiedEntity = $chartRankingItemRepository->find($entityId);
        $this->assertEquals($modifiedEntity->chartArtist()->value(), $chartArtistValue);
        $this->assertEquals($modifiedEntity->chartMusic()->value(), $chartMusicValue);
        $this->assertEquals($modifiedEntity->artistId()->value(), $artistIdValue);
        $this->assertEquals($modifiedEntity->musicId()->value(), $musicIdValue);
    }

    public function testRefreshCachedEntityEmptyParameters()
    {
        $chartRankingItemApplication = app($this->chartRankingItemApplicationInterfaceName);

        $entityIdValue = '';
        $chartRankingItemDXO = new ChartRankingItemDXO();
        $chartRankingItemDXO->refreshCachedEntity($entityIdValue);
        $result = $chartRankingItemApplication->refreshCachedEntity($chartRankingItemDXO);
        $this->assertFalse($result);
    }

    public function testRefreshCachedEntity()
    {
        $redisDAO = app($this->redisDAOInterfaceName);
        $chartRankingItemApplication = app($this->chartRankingItemApplicationInterfaceName);
        $chartRankingItemRepository = app($this->chartRankingItemRepositoryInterfaceName);

        $entityIdValue = '0123456789abcdef0123456789abcdef';
        $chartRankingItemDXO = new ChartRankingItemDXO();
        $chartRankingItemDXO->refreshCachedEntity($entityIdValue);
        $entityId = $chartRankingItemDXO->getEntityId();
        $cacheKey = $chartRankingItemRepository->cacheKeyById($entityId, ChartRankingItemEntity::class);
        $redisDAO->set($cacheKey, '1');
        $result = $chartRankingItemApplication->refreshCachedEntity($chartRankingItemDXO);
        $this->assertTrue($result);
        $cache = $redisDAO->get($cacheKey);
        $cachedEntity = unserialize($cache);
        $this->assertEquals($cachedEntity->id()->value(), $entityIdValue);
    }

    public function testDetachArtistParametersEmpty()
    {
        $chartRankingItemApplication = app($this->chartRankingItemApplicationInterfaceName);

        $chartRankingItemDXO = new ChartRankingItemDXO();
        $result = $chartRankingItemApplication->detachArtist($chartRankingItemDXO);
        $this->assertFalse($result);
    }

    public function testDetachArtistArtistNotFound()
    {
        $chartRankingItemRepositoryMock = $this->chartRankingItemRepositoryMock();
        $chartRankingItemRepositoryMock->shouldReceive('entities')->andReturn([]);
        $chartRankingItemApplication = new ChartRankingItemApplication(
            $chartRankingItemRepositoryMock,
            app($this->chartRankingItemFactoryInterfaceName)
        );

        $artistIdValue = '000080a1b2c3d4e5f6a7b8c9d';
        $chartRankingItemDXO = new ChartRankingItemDXO();
        $chartRankingItemDXO->detachArtist($artistIdValue);
        $result = $chartRankingItemApplication->detachArtist($chartRankingItemDXO);
        $this->assertTrue($result);
    }

    public function testDetachArtistModifyFalse()
    {
        $chartRankingItemRepositoryMock = $this->chartRankingItemRepositoryMock();
        $chartRankingItemRepositoryMock->shouldReceive('modify')->andReturn(false);
        $chartRankingItemApplication = new ChartRankingItemApplication(
            $chartRankingItemRepositoryMock,
            app($this->chartRankingItemFactoryInterfaceName)
        );
        $chartRankingItemRepository = app($this->chartRankingItemRepositoryInterfaceName);

        $entityIdValue = '0123456789abcdef0123456789abcdef';
        $entityId = new EntityId($entityIdValue);
        $artistIdValue = '0123456789abcdef0123456789abcdef';
        $chartRankingItemDXO = new ChartRankingItemDXO();
        $chartRankingItemDXO->detachArtist($artistIdValue);
        $result = $chartRankingItemApplication->detachArtist($chartRankingItemDXO);
        $this->assertFalse($result);
        $chartRankingItemEntity = $chartRankingItemRepository->find($entityId);
        $this->assertEquals($chartRankingItemEntity->artistId()->value(), $artistIdValue);
    }

    /**
     * @expectedException App\Domain\ChartRankingItem\ChartRankingItemException
     */
    public function testDetachArtistExceptionOccurred()
    {
        $chartRankingItemRepositoryMock = $this->chartRankingItemRepositoryMock();
        $chartRankingItemRepositoryMock->shouldReceive('modify')->andReturnUsing(
            function ($chartRankingItemEntity, $chartRankingItemSpecification) {
                throw new ChartRankingItemException();
            }
        );
        $chartRankingItemApplication = new ChartRankingItemApplication(
            $chartRankingItemRepositoryMock,
            app($this->chartRankingItemFactoryInterfaceName)
        );
        $chartRankingItemRepository = app($this->chartRankingItemRepositoryInterfaceName);

        $entityIdValue = '0123456789abcdef0123456789abcdef';
        $entityId = new EntityId($entityIdValue);
        $artistIdValue = '0123456789abcdef0123456789abcdef';
        $chartRankingItemDXO = new ChartRankingItemDXO();
        $chartRankingItemDXO->detachArtist($artistIdValue);
        $chartRankingItemApplication->detachArtist($chartRankingItemDXO);
        $chartRankingItemEntity = $chartRankingItemRepository->find($entityId);
        $this->assertEquals($chartRankingItemEntity->artistId()->value(), $artistIdValue);
    }

    public function testDetachArtist()
    {
        $chartRankingItemApplication = app($this->chartRankingItemApplicationInterfaceName);
        $dispatched = [];
        Event::shouldReceive('dispatch')->andReturnUsing(
            function ($event) use (&$dispatched) {
                $eventName = 'App\Events\ChartRankingItemModified';
                if ($event instanceOf $eventName) {
                    $dispatched[] = $event->entityIdValue();
                }
            }
        );
        $chartRankingItemRepository = app($this->chartRankingItemRepositoryInterfaceName);
        $entityId1 = '0123456789abcdef0123456789abcdef';
        $entityId2 = '2123456789abcdef0123456789abcdef';
        $artistIdValue = '0123456789abcdef0123456789abcdef';
        $chartRankingItem = ChartRankingItem::find($entityId2);
        $chartRankingItem->artist_id = $artistIdValue;
        $chartRankingItem->save();

        $verify = [$entityId1, $entityId2];
        $chartRankingItemDXO = new ChartRankingItemDXO();
        $chartRankingItemDXO->detachArtist($artistIdValue);
        $result = $chartRankingItemApplication->detachArtist($chartRankingItemDXO);
        $this->assertTrue($result);
        sort($verify);
        sort($dispatched);
        $this->assertEquals($verify, $dispatched);
        $entityId = new EntityId($entityId1);
        $chartRankingItemEntity = $chartRankingItemRepository->find($entityId);
        $this->assertEmpty($chartRankingItemEntity->artistId());
        $entityId = new EntityId($entityId2);
        $chartRankingItemEntity = $chartRankingItemRepository->find($entityId);
        $this->assertEmpty($chartRankingItemEntity->artistId());
    }

    public function testDetachMusicParametersEmpty()
    {
        $chartRankingItemApplication = app($this->chartRankingItemApplicationInterfaceName);

        $chartRankingItemDXO = new ChartRankingItemDXO();
        $result = $chartRankingItemApplication->detachMusic($chartRankingItemDXO);
        $this->assertFalse($result);
    }

    public function testDetachMusicMusicNotFound()
    {
        $chartRankingItemRepositoryMock = $this->chartRankingItemRepositoryMock();
        $chartRankingItemRepositoryMock->shouldReceive('entities')->andReturn([]);
        $chartRankingItemApplication = new ChartRankingItemApplication(
            $chartRankingItemRepositoryMock,
            app($this->chartRankingItemFactoryInterfaceName)
        );

        $musicIdValue = '000080a1b2c3d4e5f6a7b8c9d';
        $chartRankingItemDXO = new ChartRankingItemDXO();
        $chartRankingItemDXO->detachMusic($musicIdValue);
        $result = $chartRankingItemApplication->detachMusic($chartRankingItemDXO);
        $this->assertTrue($result);
    }

    public function testDetachMusicModifyFalse()
    {
        $chartRankingItemRepositoryMock = $this->chartRankingItemRepositoryMock();
        $chartRankingItemRepositoryMock->shouldReceive('modify')->andReturn(false);
        $chartRankingItemApplication = new ChartRankingItemApplication(
            $chartRankingItemRepositoryMock,
            app($this->chartRankingItemFactoryInterfaceName)
        );
        $chartRankingItemRepository = app($this->chartRankingItemRepositoryInterfaceName);

        $entityIdValue = '0123456789abcdef0123456789abcdef';
        $entityId = new EntityId($entityIdValue);
        $musicIdValue = '0123456789abcdef0123456789abcdef';
        $chartRankingItemDXO = new ChartRankingItemDXO();
        $chartRankingItemDXO->detachMusic($musicIdValue);
        $result = $chartRankingItemApplication->detachMusic($chartRankingItemDXO);
        $this->assertFalse($result);
        $chartRankingItemEntity = $chartRankingItemRepository->find($entityId);
        $this->assertEquals($chartRankingItemEntity->musicId()->value(), $musicIdValue);
    }

    /**
     * @expectedException App\Domain\ChartRankingItem\ChartRankingItemException
     */
    public function testDetachMusicExceptionOccurred()
    {
        $chartRankingItemRepositoryMock = $this->chartRankingItemRepositoryMock();
        $chartRankingItemRepositoryMock->shouldReceive('modify')->andReturnUsing(
            function ($chartRankingItemEntity, $chartRankingItemSpecification) {
                throw new ChartRankingItemException();
            }
        );
        $chartRankingItemApplication = new ChartRankingItemApplication(
            $chartRankingItemRepositoryMock,
            app($this->chartRankingItemFactoryInterfaceName)
        );
        $chartRankingItemRepository = app($this->chartRankingItemRepositoryInterfaceName);

        $entityIdValue = '0123456789abcdef0123456789abcdef';
        $entityId = new EntityId($entityIdValue);
        $musicIdValue = '0123456789abcdef0123456789abcdef';
        $chartRankingItemDXO = new ChartRankingItemDXO();
        $chartRankingItemDXO->detachMusic($musicIdValue);
        $chartRankingItemApplication->detachMusic($chartRankingItemDXO);
        $chartRankingItemEntity = $chartRankingItemRepository->find($entityId);
        $this->assertEquals($chartRankingItemEntity->musicId()->value(), $musicIdValue);
    }

    public function testDetachMusic()
    {
        $chartRankingItemApplication = app($this->chartRankingItemApplicationInterfaceName);
        $dispatched = [];
        Event::shouldReceive('dispatch')->andReturnUsing(
            function ($event) use (&$dispatched) {
                $eventName = 'App\Events\ChartRankingItemModified';
                if ($event instanceOf $eventName) {
                    $dispatched[] = $event->entityIdValue();
                }
            }
        );
        $chartRankingItemRepository = app($this->chartRankingItemRepositoryInterfaceName);
        $entityId1 = '0123456789abcdef0123456789abcdef';
        $entityId2 = '2123456789abcdef0123456789abcdef';
        $musicIdValue = '0123456789abcdef0123456789abcdef';
        $chartRankingItem = ChartRankingItem::find($entityId2);
        $chartRankingItem->music_id = $musicIdValue;
        $chartRankingItem->save();

        $verify = [$entityId1, $entityId2];
        $chartRankingItemDXO = new ChartRankingItemDXO();
        $chartRankingItemDXO->detachMusic($musicIdValue);
        $result = $chartRankingItemApplication->detachMusic($chartRankingItemDXO);
        $this->assertTrue($result);
        sort($verify);
        sort($dispatched);
        $this->assertEquals($verify, $dispatched);
        $entityId = new EntityId($entityId1);
        $chartRankingItemEntity = $chartRankingItemRepository->find($entityId);
        $this->assertEmpty($chartRankingItemEntity->musicId());
        $entityId = new EntityId($entityId2);
        $chartRankingItemEntity = $chartRankingItemRepository->find($entityId);
        $this->assertEmpty($chartRankingItemEntity->musicId());
    }

    public function testNotAttachedPaginator1()
    {
        $chartArtistValue = '';
        $chartMusicValue = '';
        $notAttachedPaginatorCalled = false;
        $chartRankingItemRepositoryMock = $this->chartRankingItemRepositoryMock();
        $chartRankingItemRepositoryMock->shouldReceive('notAttachedPaginator')->andReturnUsing(
            function ($chartArtist, $chartMusic, $chartRankingItemSpecification) use (&$notAttachedPaginatorCalled) {
                if (empty($chartArtist) && empty($chartMusic)) {
                    $notAttachedPaginatorCalled = true;
                }
            }
        );
        $chartRankingItemApplication = new ChartRankingItemApplication(
            $chartRankingItemRepositoryMock,
            app($this->chartRankingItemFactoryInterfaceName)
        );

        $chartRankingItemDXO = new ChartRankingItemDXO();
        $chartRankingItemDXO->notAttachedPaginator($chartArtistValue, $chartMusicValue);
        $chartRankingItemApplication->notAttachedPaginator($chartRankingItemDXO);
        $this->assertTrue($notAttachedPaginatorCalled);
    }

    public function testNotAttachedPaginator2()
    {
        $chartArtistValue = 'Ed';
        $chartMusicValue = '';
        $notAttachedPaginatorCalled = false;
        $chartRankingItemRepositoryMock = $this->chartRankingItemRepositoryMock();
        $chartRankingItemRepositoryMock->shouldReceive('notAttachedPaginator')->andReturnUsing(
            function ($chartArtist, $chartMusic, $chartRankingItemSpecification) use ($chartArtistValue, &$notAttachedPaginatorCalled) {
                if ($chartArtist->value() === $chartArtistValue && empty($chartMusic)) {
                    $notAttachedPaginatorCalled = true;
                }
            }
        );
        $chartRankingItemApplication = new ChartRankingItemApplication(
            $chartRankingItemRepositoryMock,
            app($this->chartRankingItemFactoryInterfaceName)
        );

        $chartRankingItemDXO = new ChartRankingItemDXO();
        $chartRankingItemDXO->notAttachedPaginator($chartArtistValue, $chartMusicValue);
        $chartRankingItemApplication->notAttachedPaginator($chartRankingItemDXO);
        $this->assertTrue($notAttachedPaginatorCalled);
    }

    public function testNotAttachedPaginator3()
    {
        $chartArtistValue = '';
        $chartMusicValue = 'Of';
        $notAttachedPaginatorCalled = false;
        $chartRankingItemRepositoryMock = $this->chartRankingItemRepositoryMock();
        $chartRankingItemRepositoryMock->shouldReceive('notAttachedPaginator')->andReturnUsing(
            function ($chartArtist, $chartMusic, $chartRankingItemSpecification) use ($chartMusicValue, &$notAttachedPaginatorCalled) {
                if (empty($chartArtist) && $chartMusic->value() === $chartMusicValue) {
                    $notAttachedPaginatorCalled = true;
                }
            }
        );
        $chartRankingItemApplication = new ChartRankingItemApplication(
            $chartRankingItemRepositoryMock,
            app($this->chartRankingItemFactoryInterfaceName)
        );

        $chartRankingItemDXO = new ChartRankingItemDXO();
        $chartRankingItemDXO->notAttachedPaginator($chartArtistValue, $chartMusicValue);
        $chartRankingItemApplication->notAttachedPaginator($chartRankingItemDXO);
        $this->assertTrue($notAttachedPaginatorCalled);
    }

    public function testNotAttachedPaginator4()
    {
        $chartArtistValue = 'Ed';
        $chartMusicValue = 'Of';
        $notAttachedPaginatorCalled = false;
        $chartRankingItemRepositoryMock = $this->chartRankingItemRepositoryMock();
        $chartRankingItemRepositoryMock->shouldReceive('notAttachedPaginator')->andReturnUsing(
            function ($chartArtist, $chartMusic, $chartRankingItemSpecification) use ($chartArtistValue, $chartMusicValue, &$notAttachedPaginatorCalled) {
                if ($chartArtist->value() === $chartArtistValue && $chartMusic->value() === $chartMusicValue) {
                    $notAttachedPaginatorCalled = true;
                }
            }
        );
        $chartRankingItemApplication = new ChartRankingItemApplication(
            $chartRankingItemRepositoryMock,
            app($this->chartRankingItemFactoryInterfaceName)
        );

        $chartRankingItemDXO = new ChartRankingItemDXO();
        $chartRankingItemDXO->notAttachedPaginator($chartArtistValue, $chartMusicValue);
        $chartRankingItemApplication->notAttachedPaginator($chartRankingItemDXO);
        $this->assertTrue($notAttachedPaginatorCalled);
    }

}
