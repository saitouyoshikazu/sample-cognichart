<?php

namespace App\Domain\AbstractArtistMusic;
use Config;
use App\Domain\AbstractArtistMusic\AbstractArtistMusicRepositoryInterface;
use App\Domain\EntityId;
use App\Domain\ValueObjects\ChartArtist;
use App\Domain\ValueObjects\ChartMusic;
use App\Domain\ValueObjects\ApiName;
use App\Domain\AbstractArtistMusic\Strategy\SymbolHandler;
use App\Events\ArtistMusicResolved;

class AbstractArtistMusicService implements AbstractArtistMusicServiceInterface
{

    private $abstractArtistMusicRepository;

    public function __construct(AbstractArtistMusicRepositoryInterface $abstractArtistMusicRepository)
    {
        $this->abstractArtistMusicRepository = $abstractArtistMusicRepository;
    }

    public function prepare(
        EntityId $chartRankingItemId,
        ChartArtist $chartArtist,
        ChartMusic $chartMusic,
        ApiName $apiName,
        string $scheme,
        string $host,
        string $uri
    ) {
        $className = "App\\Domain\\AbstractArtistMusic\\Strategy\\" . $apiName->value() . "\\RequestSender";
        $reflectionRequestSender = new \ReflectionClass($className);
        $requestSender = $reflectionRequestSender->newInstanceArgs([new SymbolHandler(), $scheme, $host, $uri]);
        return $this->abstractArtistMusicRepository->prepare($chartRankingItemId, $chartArtist, $chartMusic, $apiName, $requestSender);
    }

    public function resolve(
        EntityId $chartRankingItemId,
        ChartArtist $chartArtist,
        ChartMusic $chartMusic,
        ApiName $apiName
    ) {
        $abstractArtistMusicBusinessId = new AbstractArtistMusicBusinessId($apiName, $chartRankingItemId);
        $abstractArtistMusicEntity = $this->abstractArtistMusicRepository->get($abstractArtistMusicBusinessId);
        if (empty($abstractArtistMusicEntity)) {
            return null;
        }
        $response = $abstractArtistMusicEntity->response();
        if (empty($response)) {
            return null;
        }
        $className = "App\\Domain\\AbstractArtistMusic\\Strategy\\" . $apiName->value() . "\\ArtistMusicResolver";
        $reflectionArtistMusicResolver = new \ReflectionClass($className);
        $artistMusicResolver = $reflectionArtistMusicResolver->newInstanceArgs([new SymbolHandler()]);
        $resolved = $artistMusicResolver->resolve($chartArtist, $chartMusic, $response);
        if (empty($resolved)) {
            return null;
        }

        $className = "App\\Domain\\AbstractArtistMusic\\Strategy\\" . $apiName->value() . "\\ArtistClarifying";
        if (class_exists($className)) {
            $reflectionArtistClarifying = new \ReflectionClass($className);
            $artistClarifyingSettings = Config('app.artist_clarifying_api.'.$apiName->value());
            $artistClarifying = $reflectionArtistClarifying->newInstanceArgs([
                app('App\Infrastructure\Remote\RemoteInterface'),
                $artistClarifyingSettings['scheme'],
                $artistClarifyingSettings['host'],
                $artistClarifyingSettings['uri']
            ]);
            $artistNameValue = $artistClarifying->clarify($resolved->resolvedArtistId());
            if (!empty($artistNameValue)) {
                $resolved->setResolvedArtist($artistNameValue);
            }
        }

        return new ArtistMusicResolved(
            $chartRankingItemId->value(),
            $apiName->value(),
            $resolved->resolvedArtist(),
            $resolved->resolvedMusic(),
            $resolved->resolvedArtistId(),
            $resolved->resolvedITunesBaseUrl()
        );
    }

}
