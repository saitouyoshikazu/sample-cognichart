<?php

namespace Tests\Unit\Domain\AbstractArtistMusic;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Domain\EntityId;
use App\Domain\ValueObjects\ChartArtist;
use App\Domain\ValueObjects\ChartMusic;
use App\Domain\ValueObjects\ApiName;
use App\Domain\AbstractArtistMusic\AbstractArtistMusicBusinessId;
use App\Domain\AbstractArtistMusic\AbstractArtistMusicEntity;


class AbstractArtistMusicServiceTest extends TestCase
{

    use RefreshDatabase, DatabaseMigrations;

    private $abstractArtistMusicServiceInterfaceName = 'App\Domain\AbstractArtistMusic\AbstractArtistMusicServiceInterface';
    private $redisDAOInterfaceName = 'App\Infrastructure\RedisDAO\RedisDAOInterface';
    private $abstractArtistMusicRepositoryInterfaceName = 'App\Domain\AbstractArtistMusic\AbstractArtistMusicRepositoryInterface';

    public function tearDown()
    {
        $redisDAO = app($this->redisDAOInterfaceName);
        $redisDAO->clear('*');
        $redisDAO->resetIsCache();
    }

    public function testProvider()
    {
        $abstractArtistMusicService = app($this->abstractArtistMusicServiceInterfaceName);
        $this->assertEquals(get_class($abstractArtistMusicService), 'App\Domain\AbstractArtistMusic\AbstractArtistMusicService');
    }

    public function testPrepare()
    {
        $abstractArtistMusicService = app($this->abstractArtistMusicServiceInterfaceName);
        $abstractArtistMusicRepository = app($this->abstractArtistMusicRepositoryInterfaceName);

        $itunesSettings = config('app.artist_music_resolve_api.itunes');
        $apiName = new ApiName('itunes');

        $chartRankingItemId = new EntityId('DoNotExist');
        $chartArtist = new ChartArtist('AAAAABBBBBCCCCC');
        $chartMusic = new ChartMusic('AAAAABBBBBCCCCC');
        $abstractArtistMusicService->prepare(
            $chartRankingItemId,
            $chartArtist,
            $chartMusic,
            $apiName,
            $itunesSettings['scheme'],
            $itunesSettings['host'],
            $itunesSettings['uri']
        );
        $abstractArtistMusicBusinessId = new AbstractArtistMusicBusinessId($apiName, $chartRankingItemId);
        $abstractArtistMusicEntity = $abstractArtistMusicRepository->findCache($abstractArtistMusicBusinessId, AbstractArtistMusicEntity::class);
        $this->assertNull($abstractArtistMusicEntity);

        sleep(10);

        $chartRankingItemId = new EntityId('ThisWillExist');
        $chartArtist = new ChartArtist('Bebe Rexha & Florida Georgia Line');
        $chartMusic = new ChartMusic('Meant To Be');
        $abstractArtistMusicService->prepare(
            $chartRankingItemId,
            $chartArtist,
            $chartMusic,
            $apiName,
            $itunesSettings['scheme'],
            $itunesSettings['host'],
            $itunesSettings['uri']
        );
        $abstractArtistMusicBusinessId = new AbstractArtistMusicBusinessId($apiName, $chartRankingItemId);
        $abstractArtistMusicEntity = $abstractArtistMusicRepository->findCache($abstractArtistMusicBusinessId, AbstractArtistMusicEntity::class);
        $this->assertEquals($abstractArtistMusicEntity->id()->value(), $chartRankingItemId->value());
    }

    public function testResolve()
    {
        $abstractArtistMusicService = app($this->abstractArtistMusicServiceInterfaceName);

        $itunesSettings = config('app.artist_music_resolve_api.itunes');
        $apiName = new ApiName('itunes');

        $chartRankingItemId = new EntityId('DoNotExist');
        $chartArtist = new ChartArtist('AAAAABBBBBCCCCC');
        $chartMusic = new ChartMusic('AAAAABBBBBCCCCC');
        $abstractArtistMusicService->prepare(
            $chartRankingItemId,
            $chartArtist,
            $chartMusic,
            $apiName,
            $itunesSettings['scheme'],
            $itunesSettings['host'],
            $itunesSettings['uri']
        );
        $artistMusicResolved = $abstractArtistMusicService->resolve(
            $chartRankingItemId,
            $chartArtist,
            $chartMusic,
            $apiName
        );
        $this->assertNull($artistMusicResolved);

        sleep(10);

        $chartRankingItemId = new EntityId('ThisWillExist');
        $chartArtist = new ChartArtist('Bebe Rexha & Florida Georgia Line');
        $chartMusic = new ChartMusic('Meant To Be');
        $abstractArtistMusicService->prepare(
            $chartRankingItemId,
            $chartArtist,
            $chartMusic,
            $apiName,
            $itunesSettings['scheme'],
            $itunesSettings['host'],
            $itunesSettings['uri']
        );
        $artistMusicResolved = $abstractArtistMusicService->resolve(
            $chartRankingItemId,
            $chartArtist,
            $chartMusic,
            $apiName
        );
        $this->assertEquals($artistMusicResolved->chartRankingItemIdValue(), $chartRankingItemId->value());
        $this->assertEquals($artistMusicResolved->apiNameValue(), $apiName->value());
        $this->assertNotEmpty($artistMusicResolved->resolvedArtistValue());
        $this->assertNotEmpty($artistMusicResolved->resolvedMusicValue());
    }

}
