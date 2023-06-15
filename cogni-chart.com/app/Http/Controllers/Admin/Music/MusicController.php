<?php

namespace App\Http\Controllers\Admin\Music;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Expand\Validation\ExpValidation;
use App\Infrastructure\URL\URLBuilder;
use App\Application\ChartRankingItem\ChartRankingItemApplicationInterface;
use App\Application\Music\MusicApplicationInterface;
use App\Application\Artist\ArtistApplicationInterface;
use App\Application\DXO\ChartRankingItemDXO;
use App\Application\DXO\MusicDXO;
use App\Application\DXO\ArtistDXO;
use App\Domain\ValueObjects\Phase;

class MusicController extends Controller
{

    private $chartRankingItemApplication;
    private $musicApplication;
    private $artistApplication;

    public function __construct(
        ChartRankingItemApplicationInterface $chartRankingItemApplication,
        MusicApplicationInterface $musicApplication,
        ArtistApplicationInterface $artistApplication
    ) {
        $this->middleware('auth');
        $this->chartRankingItemApplication = $chartRankingItemApplication;
        $this->musicApplication = $musicApplication;
        $this->artistApplication = $artistApplication;
    }

    public function register(Request $request)
    {
        $expValidator = new ExpValidation([
            'music_title',
            'itunes_artist_id',
            'chartrankingitem_id',
            'itunes_base_url',
            'promotion_video_url',
            'thumbnail_url'
        ]);
        $expValidator->validateWithRedirect($request);

        $musicDXO = new MusicDXO();
        $musicDXO->get(Phase::provisioned, $request->input('itunes_artist_id'), $request->input('music_title'));
        $musicEntity = $this->musicApplication->get($musicDXO);
        if (empty($musicEntity)) {
            $musicDXO->get(Phase::released, $request->input('itunes_artist_id'), $request->input('music_title'));
            $musicEntity = $this->musicApplication->get($musicDXO);
        }

        if (empty($musicEntity)) {
            $musicDXO = new MusicDXO();
            $musicDXO->register(
                $request->input('itunes_artist_id'),
                $request->input('music_title'),
                $request->input('itunes_base_url'),
                $request->input('promotion_video_url'),
                $request->input('thumbnail_url')
            );
            try {
                $result = $this->musicApplication->register($musicDXO);
                if ($result === false) {
                    return redirect()->back()->withInput()->withErrors(['application' => 'Failed to register Music.']);
                }
                $musicDXO = new MusicDXO();
                $musicDXO->get(Phase::provisioned, $request->input('itunes_artist_id'), $request->input('music_title'));
                $musicEntity = $this->musicApplication->get($musicDXO);
                if (empty($musicEntity)) {
                    return redirect()->back()->withInput()->withErrors(['application' => 'Failed to register Music.']);
                }
            } catch (\Exception $e) {
                return redirect()->back()->withInput()->withErrors(['application' => $e->getMessage()]);
            }
        }

        $chartRankingItemDXO = new ChartRankingItemDXO();
        $chartRankingItemDXO->find($request->input('chartrankingitem_id'));
        $chartRankingItemEntity = $this->chartRankingItemApplication->find($chartRankingItemDXO);
        if (empty($chartRankingItemEntity)) {
            return redirect()->back()->withInput()->withErrors(['application' => "Couldn't find ChartRankingItem."]);
        }
        $chartRankingItemDXO = new ChartRankingItemDXO();
        $chartRankingItemDXO->modify(
            $chartRankingItemEntity->id()->value(),
            $chartRankingItemEntity->chartArtist()->value(),
            $chartRankingItemEntity->chartMusic()->value(),
            empty($chartRankingItemEntity->artistId())?null:$chartRankingItemEntity->artistId()->value(),
            $musicEntity->id()->value()
        );
        try {
            $result = $this->chartRankingItemApplication->modify($chartRankingItemDXO);
            if ($result === false) {
                return redirect()->back()->withInput()->withErrors(['application' => "Failed to assign Music to ChartRankingItem."]);
            }
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['application' => $e->getMessage()]);
        }
        return redirect()->to(
            URLBuilder::rebuildQueryString(url()->previous(), ['pageLocationId' =>  $request->input('chartrankingitem_id')])
        );
    }

    public function modify(Request $request)
    {
        $expValidator = new ExpValidation([
            'music_phase',
            'music_id',
            'itunes_artist_id',
            'music_title',
            'itunes_base_url',
            'promotion_video_url',
            'thumbnail_url'
        ]);
        $expValidator->validateWithRedirect($request);

        $musicDXO = new MusicDXO();
        $musicDXO->modify(
            $request->input('music_phase'),
            $request->input('music_id'),
            $request->input('itunes_artist_id'),
            $request->input('music_title'),
            $request->input('itunes_base_url'),
            $request->input('promotion_video_url'),
            $request->input('thumbnail_url')
        );
        try {
            $result = $this->musicApplication->modify($musicDXO);
            if ($result === false) {
                return redirect()->back()->withInput()->withErrors(['application' => 'Failed to modify Music.']);
            }
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['application' => $e->getMessage()]);
        }
        return redirect()->to(
            URLBuilder::rebuildQueryString(url()->previous(), ['pageLocationId' =>  $request->input('chartrankingitem_id')])
        );
    }

    public function delete(Request $request)
    {
        $expValidator = new ExpValidation(['music_id']);
        $expValidator->validateWithRedirect($request);

        $musicDXO = new MusicDXO();
        $musicDXO->delete($request->input('music_id'));
        try {
            $result = $this->musicApplication->delete($musicDXO);
            if ($result === false) {
                return redirect()->back()->withInput()->withErrors(['application' => 'Failed to delete Music.']);
            }
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['application' => $e->getMessage()]);
        }
        return redirect()->to(
            URLBuilder::rebuildQueryString(url()->previous(), ['pageLocationId' =>  $request->input('chartrankingitem_id')])
        );
    }

    public function release(Request $request)
    {
        $expValidator = new ExpValidation(['music_id']);
        $expValidator->validateWithRedirect($request);

        $musicDXO = new MusicDXO();
        $musicDXO->release($request->input('music_id'));
        try {
            $result = $this->musicApplication->release($musicDXO);
            if ($result === false) {
                return redirect()->back()->withInput()->withErrors(['application' => 'Failed to release Music.']);
            }
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['application' => $e->getMessage()]);
        }

        return redirect()->to(
            URLBuilder::rebuildQueryString(url()->previous(), ['pageLocationId' =>  $request->input('chartrankingitem_id')])
        );
    }

    public function rollback(Request $request)
    {
        $expValidator = new ExpValidation(['music_id']);
        $expValidator->validateWithRedirect($request);

        $musicDXO = new MusicDXO();
        $musicDXO->rollback($request->input('music_id'));
        try {
            $result = $this->musicApplication->rollback($musicDXO);
            if ($result === false) {
                return redirect()->back()->withInput()->withErrors(['application' => 'Failed to rollback Music.']);
            }
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['application' => $e->getMessage()]);
        }
        return redirect()->to(
            URLBuilder::rebuildQueryString(url()->previous(), ['pageLocationId' =>  $request->input('chartrankingitem_id')])
        );
    }

    public function promotion_video_broken_links(Request $request)
    {
        $expValidator = new ExpValidation(['search_artist_name']);
        $expValidator->validateWithRedirect($request);

        $musicDXO = new MusicDXO();
        $search_artist_name = $request->input('search_artist_name');
        if (!empty($search_artist_name)) {
            $artistDXO = new ArtistDXO();
            $artistDXO->provisionedEntities(null, $search_artist_name);
            $provisionedArtistEntities = $this->artistApplication->provisionedEntities($artistDXO);
            if (!empty($provisionedArtistEntities)) {
                foreach ($provisionedArtistEntities AS $artistEntity) {
                    $musicDXO->promotionVideoBrokenLinksAppendItunesArtistId($artistEntity->iTunesArtistId()->value());
                }
            }

            $artistDXO = new ArtistDXO();
            $artistDXO->releasedEntities(null, $search_artist_name);
            $releasedArtistEntities = $this->artistApplication->releasedEntities($artistDXO);
            if (!empty($releasedArtistEntities)) {
                foreach ($releasedArtistEntities AS $artistEntity) {
                    $musicDXO->promotionVideoBrokenLinksAppendItunesArtistId($artistEntity->iTunesArtistId()->value());
                }
            }
        }

        $domainPaginator = $this->musicApplication->promotionVideoBrokenLinks($musicDXO);
        $musicEntities = $domainPaginator->getEntities();
        $musicPaginator = $domainPaginator->getPaginator();
        return view(
            'admin.music.promotion_video_broken_links',
            [
                'musicEntities'         =>  $musicEntities,
                'musicPaginator'        =>  $musicPaginator,
                'search_artist_name'    =>  $search_artist_name,
            ]
        );
    }

    public function search(Request $request, string $music_phase)
    {
        $expValidator = new ExpValidation(['music_phase', 'search_itunes_artist_id', 'search_music_title']);
        $request->merge(['music_phase' => $music_phase]);
        $expValidator->validateWithRedirect($request);

        $search_itunes_artist_id = $request->input('search_itunes_artist_id');
        $search_music_title = $request->input('search_music_title');
        $musicEntities = null;
        $musicPaginator = null;
        if (!empty($search_itunes_artist_id) || !empty($search_music_title)) {
            $musicDXO = new MusicDXO();
            $domainPaginator = null;
            if ($music_phase === Phase::provisioned) {
                $musicDXO->provisionedPaginator($search_itunes_artist_id, $search_music_title);
                $domainPaginator = $this->musicApplication->provisionedPaginator($musicDXO);
            } elseif ($music_phase === Phase::released) {
                $musicDXO->releasedPaginator($search_itunes_artist_id, $search_music_title);
                $domainPaginator = $this->musicApplication->releasedPaginator($musicDXO);
            }
            if (!empty($domainPaginator)) {
                $musicEntities = $domainPaginator->getEntities();
                $musicPaginator = $domainPaginator->getPaginator();
            }
        }
        return view(
            'admin.music.search',
            [
                'music_phase'               =>  $music_phase,
                'search_itunes_artist_id'   =>  $search_itunes_artist_id,
                'search_music_title'        =>  $search_music_title,
                'musicEntities'             =>  $musicEntities,
                'musicPaginator'            =>  $musicPaginator,
            ]
        );
    }

}
