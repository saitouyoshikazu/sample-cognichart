<?php

namespace Tests\Unit\Infrastructure\Repositories;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Domain\EntityId;
use App\Domain\ValueObjects\ChartArtist;
use App\Domain\ValueObjects\ChartMusic;
use App\Domain\ValueObjects\ApiName;
use App\Domain\AbstractArtistMusic\Strategy\itunes\RequestSender;
use App\Domain\AbstractArtistMusic\Strategy\SymbolHandler;
use App\Domain\AbstractArtistMusic\AbstractArtistMusicBusinessId;
use App\Domain\AbstractArtistMusic\AbstractArtistMusicEntity;

class AbstractArtistMusicRepositoryTest extends TestCase
{

    use RefreshDatabase, DatabaseMigrations;

    private $abstractArtistMusicRepositoryInterfaceName = 'App\Domain\AbstractArtistMusic\AbstractArtistMusicRepositoryInterface';
    private $redisDAOInterfaceName = 'App\Infrastructure\RedisDAO\RedisDAOInterface';

    public function tearDown()
    {
        $redisDAO = app($this->redisDAOInterfaceName);
        $redisDAO->clear('*');
        $redisDAO->resetIsCache();
    }

    public function testProvider()
    {
        $abstractArtistMusicRepository = app($this->abstractArtistMusicRepositoryInterfaceName);
        $this->assertEquals(get_class($abstractArtistMusicRepository), 'App\Infrastructure\Repositories\AbstractArtistMusicRepository');
    }

    public function testPrepare()
    {
        $abstractArtistMusicRepository = app($this->abstractArtistMusicRepositoryInterfaceName);
        $redisDAO = app($this->redisDAOInterfaceName);

        $itunesSettings = config('app.artist_music_resolve_api.itunes');
        $apiName = new ApiName('itunes');
        $requestSender = new RequestSender(
            new SymbolHandler(),
            $itunesSettings['scheme'],
            $itunesSettings['host'],
            $itunesSettings['uri']
        );

        $chartRankingItemId = new EntityId('DoNotExist');
        $chartArtist = new ChartArtist('AAAAABBBBBCCCCC');
        $chartMusic = new ChartMusic('AAAAABBBBBCCCCC');
        $abstractArtistMusicBusinessId = new AbstractArtistMusicBusinessId($apiName, $chartRankingItemId);
        $abstractArtistMusicRepository->prepare($chartRankingItemId, $chartArtist, $chartMusic, $apiName, $requestSender);
        $abstractArtistMusicEntity = $abstractArtistMusicRepository->findCache($abstractArtistMusicBusinessId, AbstractArtistMusicEntity::class);
        $this->assertNull($abstractArtistMusicEntity);

        sleep(10);

        $chartRankingItemId = new EntityId('ThisWillExist');
        $chartArtist = new ChartArtist('Bebe Rexha & Florida Georgia Line');
        $chartMusic = new ChartMusic('Meant To Be');
        $abstractArtistMusicBusinessId = new AbstractArtistMusicBusinessId($apiName, $chartRankingItemId);
        $abstractArtistMusicRepository->prepare($chartRankingItemId, $chartArtist, $chartMusic, $apiName, $requestSender);
        $abstractArtistMusicEntity = $abstractArtistMusicRepository->findCache($abstractArtistMusicBusinessId, AbstractArtistMusicEntity::class);
        $this->assertEquals($abstractArtistMusicEntity->businessId()->value(), $abstractArtistMusicBusinessId->value());
    }

    public function testGet()
    {
        $abstractArtistMusicRepository = app($this->abstractArtistMusicRepositoryInterfaceName);
        $redisDAO = app($this->redisDAOInterfaceName);

        $itunesSettings = config('app.artist_music_resolve_api.itunes');
        $apiName = new ApiName('itunes');
        $requestSender = new RequestSender(
            new SymbolHandler(),
            $itunesSettings['scheme'],
            $itunesSettings['host'],
            $itunesSettings['uri']
        );
        $existChartRankingItemId = new EntityId('ThisWillExist');
        $chartArtist = new ChartArtist('Bebe Rexha & Florida Georgia Line');
        $chartMusic = new ChartMusic('Meant To Be');
        sleep(10);
        $abstractArtistMusicRepository->prepare($existChartRankingItemId, $chartArtist, $chartMusic, $apiName, $requestSender);

        $donotExistChartRankingItemId = new EntityId('DoNotExist');
        $abstractArtistMusicBusinessId = new AbstractArtistMusicBusinessId($apiName, $donotExistChartRankingItemId);
        $abstractArtistMusicEntity = $abstractArtistMusicRepository->get($abstractArtistMusicBusinessId);
        $this->assertNull($abstractArtistMusicEntity);

        $abstractArtistMusicBusinessId = new AbstractArtistMusicBusinessId($apiName, $existChartRankingItemId);
        $abstractArtistMusicEntity = $abstractArtistMusicRepository->get($abstractArtistMusicBusinessId);
        $this->assertEquals($abstractArtistMusicEntity->id()->value(), $existChartRankingItemId->value());
    }

}
