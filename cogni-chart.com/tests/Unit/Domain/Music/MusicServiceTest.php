<?php

namespace Tests\Unit\Infrastructure\Repositories;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Infrastructure\Eloquents\Music;
use App\Domain\EntityId;
use App\Domain\ValueObjects\ArtistName;
use App\Domain\ValueObjects\MusicTitle;
use App\Domain\ValueObjects\PromotionVideoUrl;
use App\Domain\Music\MusicService;

class MusicServiceTest extends TestCase
{

    use DatabaseMigrations;

    private $musicServiceInterfaceName = 'App\Domain\Music\MusicServiceInterface';

    public function setUp()
    {
        parent::setUp();
    }

    public function tearDown()
    {
        Music::truncate();
    }

    public function testProvider()
    {
        $musicService = app($this->musicServiceInterfaceName);
        $this->assertEquals(get_class($musicService), MusicService::class);
    }

    public function testSearchPromotionVideo()
    {
        $musicService = app($this->musicServiceInterfaceName);

        $artistNameValue = 'Ed Sheeran';
        $musicTitleValue = 'Shape Of You';
        $artistName = new ArtistName($artistNameValue);
        $musicTitle = new MusicTitle($musicTitleValue);
        $result = $musicService->searchPromotionVideo($artistName, $musicTitle);
        $this->assertNotNull($result['url']);
        $this->assertNotNull($result['thumbnail_url']);
    }

    public function testCheckPromotionVideoPromotionVideoEmpty()
    {
        factory(Music::class, 4)->create();

        $musicService = app($this->musicServiceInterfaceName);
        $musicRepository = app('App\Domain\Music\MusicRepositoryInterface');

        $idValue = '000010a1b2c3d4e5f6a7b8c9d';
        $id = new EntityId($idValue);
        $musicEntity = $musicRepository->findRelease($id);

        $result = $musicService->checkPromotionVideo($musicEntity);
        $this->assertFalse($result);
    }

    public function testCheckPromotionVideoPromotionVideoDisabled()
    {
        factory(Music::class, 4)->create();

        $musicService = app($this->musicServiceInterfaceName);
        $musicRepository = app('App\Domain\Music\MusicRepositoryInterface');

        $idValue = '000010a1b2c3d4e5f6a7b8c9d';
        $id = new EntityId($idValue);
        $musicEntity = $musicRepository->findRelease($id);
        $promotionVideoUrl = new PromotionVideoUrl('nXBaq_zNbKI');
        $musicEntity->setPromotionVideoUrl($promotionVideoUrl);

        $result = $musicService->checkPromotionVideo($musicEntity);
        $this->assertFalse($result);
    }

    public function testCheckPromotionVideo()
    {
        factory(Music::class, 4)->create();

        $musicService = app($this->musicServiceInterfaceName);
        $musicRepository = app('App\Domain\Music\MusicRepositoryInterface');

        $idValue = '000010a1b2c3d4e5f6a7b8c9d';
        $id = new EntityId($idValue);
        $musicEntity = $musicRepository->findRelease($id);
        $promotionVideoUrl = new PromotionVideoUrl('JGwWNGJdvx8');
        $musicEntity->setPromotionVideoUrl($promotionVideoUrl);

        $result = $musicService->checkPromotionVideo($musicEntity);
        $this->assertTrue($result);
    }

}
