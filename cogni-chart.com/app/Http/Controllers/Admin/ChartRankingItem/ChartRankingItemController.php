<?php

namespace App\Http\Controllers\Admin\ChartRankingItem;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Config;
use App\Expand\Validation\ExpValidation;
use App\Application\ChartRankingItem\ChartRankingItemApplicationInterface;
use App\Infrastructure\Remote\RemoteInterface;
use App\Application\DXO\ChartRankingItemDXO;
use App\Domain\ValueObjects\ChartArtist;
use App\Domain\ValueObjects\ChartMusic;
use App\Domain\AbstractArtistMusic\Strategy\SymbolHandler;
use App\Domain\AbstractArtistMusic\Strategy\itunes\RequestSender;
use App\Domain\AbstractArtistMusic\Strategy\itunes\ArtistClarifying;

class ChartRankingItemController extends Controller
{

    private $chartRankingItemApplication;
    private $remote;

    public function __construct(
        ChartRankingItemApplicationInterface $chartRankingItemApplication,
        RemoteInterface $remote
    ) {
        $this->middleware('auth');
        $this->chartRankingItemApplication = $chartRankingItemApplication;
        $this->remote = $remote;
    }

    public function itunessearch(Request $request)
    {
        $expValidator = new ExpValidation(['chart_artist', 'chart_music']);
        $validator = $expValidator->validateOnly($request->all());
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors()->all());
        }
        $itunesSettings = Config::get('app.artist_music_resolve_api.itunes');
        if (empty($itunesSettings)) {
            return redirect()->back()->withErrors(['application' => 'Settings of itunes is invalid.']);
        }
        $requestSender = new RequestSender(new SymbolHandler(), $itunesSettings['scheme'], $itunesSettings['host'], $itunesSettings['uri']);
        $chartArtist = new ChartArtist($request->input('chart_artist'));
        $chartMusic = new ChartMusic($request->input('chart_music'));
        $itunesSearchResult = $requestSender->send($this->remote, $chartArtist, $chartMusic);
        $clarifiedArtistName['clarifiedArtistId'] = "";
        $clarifiedArtistName['clarifiedArtistName'] = "";
        if (
            !empty($itunesSearchResult) &&
            array_key_exists('resultCount', $itunesSearchResult) &&
            intVal($itunesSearchResult['resultCount']) > 0
        ) {
            $artistClarifyingSettings = Config('app.artist_clarifying_api.itunes');
            $artistClarifying = new ArtistClarifying(
                $this->remote,
                $artistClarifyingSettings['scheme'],
                $artistClarifyingSettings['host'],
                $artistClarifyingSettings['uri']
            );
            $artistName = $artistClarifying->clarify($itunesSearchResult['results'][0]['artistId']);
            $clarifiedArtistName['clarifiedArtistId'] = $itunesSearchResult['results'][0]['artistId'];
            $clarifiedArtistName['clarifiedArtistName'] = $artistName;
        }
        return view(
            'admin.chartrankingitem.itunessearchresult',
            [
                'clarifiedArtistName' => $clarifiedArtistName,
                'itunesSearchResult' => $itunesSearchResult
            ]
        );
    }

    public function notattached(Request $request)
    {
        $expValidator = new ExpValidation(['search_chart_artist', 'search_chart_music']);
        $expValidator->validateWithRedirect($request);

        $search_chart_artist = $request->input('search_chart_artist');
        $search_chart_music = $request->input('search_chart_music');
        $chartRankingItemDXO = new ChartRankingItemDXO();
        $chartRankingItemDXO->notAttachedPaginator($search_chart_artist, $search_chart_music);
        $domainPaginator = $this->chartRankingItemApplication->notAttachedPaginator($chartRankingItemDXO);
        $chartRankingItemEntities = $domainPaginator->getEntities();
        $chartRankingItemPaginator = $domainPaginator->getPaginator();
        return view(
            'admin.chartrankingitem.search',
            [
                'search_chart_artist' => $search_chart_artist,
                'search_chart_music' => $search_chart_music,
                'chartRankingItemEntities' => $chartRankingItemEntities,
                'chartRankingItemPaginator' => $chartRankingItemPaginator,
            ]
        );
    }

}
