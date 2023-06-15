<?php

namespace App\Http\Controllers\Admin\Artist;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Expand\Validation\ExpValidation;
use App\Infrastructure\URL\URLBuilder;
use App\Application\ChartRankingItem\ChartRankingItemApplicationInterface;
use App\Application\Artist\ArtistApplicationInterface;
use App\Application\DXO\ChartRankingItemDXO;
use App\Application\DXO\ArtistDXO;
use App\Domain\ValueObjects\Phase;

class ArtistController extends Controller
{

    private $chartRankingItemApplication;
    private $artistApplication;

    public function __construct(
        ChartRankingItemApplicationInterface $chartRankingItemApplication,
        ArtistApplicationInterface $artistApplication
    ) {
        $this->middleware('auth');
        $this->chartRankingItemApplication = $chartRankingItemApplication;
        $this->artistApplication = $artistApplication;
    }

    public function register(Request $request)
    {
        $expValidator = new ExpValidation(['artist_name', 'itunes_artist_id', 'chartrankingitem_id']);
        $expValidator->validateWithRedirect($request);

        $artistDXO = new ArtistDXO();
        $artistDXO->get(Phase::provisioned, $request->input('itunes_artist_id'));
        $artistEntity = $this->artistApplication->get($artistDXO);
        if (empty($artistEntity)) {
            $artistDXO->get(Phase::released, $request->input('itunes_artist_id'));
            $artistEntity = $this->artistApplication->get($artistDXO);
        }

        if (empty($artistEntity)) {
            $artistDXO = new ArtistDXO();
            $artistDXO->register($request->input('itunes_artist_id'), $request->input('artist_name'));
            try {
                $result = $this->artistApplication->register($artistDXO);
                if ($result === false) {
                    return redirect()->back()->withInput()->withErrors(['application' => 'Failed to register Artist.']);
                }
                $artistDXO = new ArtistDXO();
                $artistDXO->get(Phase::provisioned, $request->input('itunes_artist_id'));
                $artistEntity = $this->artistApplication->get($artistDXO);
                if (empty($artistEntity)) {
                    return redirect()->back()->withInput()->withErrors(['application' => 'Failed to register Artist.']);
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
            $artistEntity->id()->value(),
            empty($chartRankingItemEntity->musicId())?null:$chartRankingItemEntity->musicId()->value()
        );
        try {
            $result = $this->chartRankingItemApplication->modify($chartRankingItemDXO);
            if ($result === false) {
                return redirect()->back()->withInput()->withErrors(['application' => "Failed to assign Artist to ChartRankingItem."]);
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
        $expValidator = new ExpValidation(['artist_phase', 'artist_id', 'itunes_artist_id', 'artist_name']);
        $expValidator->validateWithRedirect($request);

        $artistDXO = new ArtistDXO();
        $artistDXO->modify(
            $request->input('artist_phase'),
            $request->input('artist_id'),
            $request->input('itunes_artist_id'),
            $request->input('artist_name')
        );
        try {
            $result = $this->artistApplication->modify($artistDXO);
            if ($result === false) {
                return redirect()->back()->withInput()->withErrors(['application' => 'Failed to modify Artist.']);
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
        $expValidator = new ExpValidation(['artist_id']);
        $expValidator->validateWithRedirect($request);

        $artistDXO = new ArtistDXO();
        $artistDXO->delete($request->input('artist_id'));
        try {
            $result = $this->artistApplication->delete($artistDXO);
            if ($result === false) {
                return redirect()->back()->withInput()->withErrors(['application' => 'Failed to delete Artist.']);
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
        $expValidator = new ExpValidation(['artist_id']);
        $expValidator->validateWithRedirect($request);

        $artistDXO = new ArtistDXO();
        $artistDXO->release($request->input('artist_id'));
        try {
            $result = $this->artistApplication->release($artistDXO);
            if ($result === false) {
                return redirect()->back()->withInput()->withErrors(['application' => 'Failed to release Artist.']);
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
        $expValidator = new ExpValidation(['artist_id']);
        $expValidator->validateWithRedirect($request);

        $artistDXO = new ArtistDXO();
        $artistDXO->rollback($request->input('artist_id'));
        try {
            $result = $this->artistApplication->rollback($artistDXO);
            if ($result === false) {
                return redirect()->back()->withInput()->withErrors(['application' => 'Failed to rollback Artist.']);
            }
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['application' => $e->getMessage()]);
        }
        return redirect()->to(
            URLBuilder::rebuildQueryString(url()->previous(), ['pageLocationId' =>  $request->input('chartrankingitem_id')])
        );
    }

    public function search(Request $request, string $artist_phase)
    {
        $expValidator = new ExpValidation(['artist_phase', 'search_artist_name']);
        $request->merge(['artist_phase' => $artist_phase]);
        $expValidator->validateWithRedirect($request);

        $artistEntities = null;
        $artistPaginator = null;
        $search_artist_name = $request->input('search_artist_name');
        if (!empty($search_artist_name)) {
            $artistDXO = new ArtistDXO();
            $domainPaginator = null;
            if ($artist_phase === Phase::provisioned) {
                $artistDXO->provisionedPaginator(null, $search_artist_name);
                $domainPaginator = $this->artistApplication->provisionedPaginator($artistDXO);
            } else if ($artist_phase === Phase::released) {
                $artistDXO->releasedPaginator(null, $search_artist_name);
                $domainPaginator = $this->artistApplication->releasedPaginator($artistDXO);
            }
            if (!empty($domainPaginator)) {
                $artistEntities = $domainPaginator->getEntities();
                $artistPaginator = $domainPaginator->getPaginator();
            }
        }
        return view(
            'admin.artist.search',
            [
                'artist_phase'          =>  $artist_phase,
                'search_artist_name'    =>  $search_artist_name,
                'artistEntities'        =>  $artistEntities,
                'artistPaginator'       =>  $artistPaginator,
            ]
        );
    }

}
