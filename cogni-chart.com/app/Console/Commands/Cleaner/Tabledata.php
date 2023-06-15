<?php

namespace App\Console\Commands\Cleaner;

use Illuminate\Console\Command;
use App\Infrastructure\Eloquents\ChartRanking;
use App\Infrastructure\Eloquents\ChartRankingItem;
use App\Infrastructure\Eloquents\ProvisionedArtist;
use App\Infrastructure\Eloquents\Artist;
use App\Infrastructure\Eloquents\ProvisionedMusic;
use App\Infrastructure\Eloquents\Music;
use App\Infrastructure\Eloquents\PromotionVideo;

class Tabledata extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Cleaner:tabledata';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // ChartRankingに紐づけられていないChartRankingItemを削除
        $chartRankingItems = ChartRankingItem::all();
        foreach ($chartRankingItems AS $chartRankingItem) {
            $chartRankings = ChartRanking::where('chart_ranking_item_id', $chartRankingItem->id)->get();
            if ($chartRankings->count() == 0) {
                $chartRankingItem->delete();
            }
        }

        // ChartRankingItemに紐づけられていないArtistを削除
        $artists = Artist::all();
        foreach ($artists AS $artist) {
            $chartRankingItems = ChartRankingItem::where('artist_id', $artist->id)->get();
            if ($chartRankingItems->count() == 0) {
                $artist->delete();
            }
        }

        // ChartRankingItemに紐づけられていないProvisionedArtistを削除
        $provisionedArtists = ProvisionedArtist::all();
        foreach ($provisionedArtists AS $provisionedArtist) {
            $chartRankingItems = ChartRankingItem::where('artist_id', $provisionedArtist->id)->get();
            if ($chartRankingItems->count() == 0) {
                $provisionedArtist->delete();
            }
        }

        // ChartRankingItemに紐づけられていないMusicを削除(PromotionVideoも削除)
        $musics = Music::all();
        foreach ($musics AS $music) {
            $chartRankingItems = ChartRankingItem::where('music_id', $music->id)->get();
            if ($chartRankingItems->count() == 0) {
                $promotionVideos = PromotionVideo::where('music_id', $music->id)->delete();
                $music->delete();
            }
        }

        // ChartRankingItemに紐づけられていないProvisionedMusicを削除(PromotionVideoも削除)
        $provisionedMusics = ProvisionedMusic::all();
        foreach ($provisionedMusics AS $provisionedMusic) {
            $chartRankingItems = ChartRankingItem::where('music_id', $provisionedMusic->id)->get();
            if ($chartRankingItems->count() == 0) {
                $promotionVideos = PromotionVideo::where('music_id', $provisionedMusic->id)->delete();
                $provisionedMusic->delete();
            }
        }
    }

/*
    for test
INSERT INTO chart_ranking_items (id, chart_artist, chart_music, artist_id, music_id, created_at, updated_at) VALUES('aaa', 'aaa', 'aaa', '', '', now(), now());
INSERT INTO chart_ranking_items (id, chart_artist, chart_music, artist_id, music_id, created_at, updated_at) VALUES('bbb','bbb','bbb','','',now(),now());
SELECT * FROM chart_ranking_items WHERE id = 'aaa';
SELECT * FROM chart_ranking_items WHERE id = 'bbb';

INSERT INTO artists (id, artist_name, itunes_artist_id, created_at, updated_at) VALUES('aaa', 'aaa', 'aaa', now(), now());
INSERT INTO artists (id, artist_name, itunes_artist_id, created_at, updated_at) VALUES('bbb', 'bbb', 'bbb', now(), now());
SELECT * FROM artists WHERE id = 'aaa';
SELECT * FROM artists WHERE id = 'bbb';

INSERT INTO provisioned_artists (id, artist_name, itunes_artist_id, created_at, updated_at) VALUES('aaa', 'aaa', 'aaa', now(), now());
INSERT INTO provisioned_artists (id, artist_name, itunes_artist_id, created_at, updated_at) VALUES('bbb', 'bbb', 'bbb', now(), now());
SELECT * FROM provisioned_artists WHERE id = 'aaa';
SELECT * FROM provisioned_artists WHERE id = 'bbb';

INSERT INTO musics (id, itunes_artist_id, music_title, itunes_base_url, created_at, updated_at) VALUES('aaa', 'aaa', 'aaa', 'aaa', now(), now());
INSERT INTO musics (id, itunes_artist_id, music_title, itunes_base_url, created_at, updated_at) VALUES('bbb', 'bbb', 'bbb', 'bbb', now(), now());
INSERT INTO promotion_videos (music_id, url, thumbnail_url, created_at, updated_at) VALUES('aaa', 'aaa', 'aaa', now(), now());
INSERT INTO promotion_videos (music_id, url, thumbnail_url, created_at, updated_at) VALUES('bbb', 'bbb', 'bbb', now(), now());
SELECT * FROM musics WHERE id = 'aaa';
SELECT * FROM musics WHERE id = 'bbb';
SELECT * FROM promotion_videos WHERE music_id = 'aaa';
SELECT * FROM promotion_videos WHERE music_id = 'bbb';

INSERT INTO provisioned_musics (id, itunes_artist_id, music_title, itunes_base_url, created_at, updated_at) VALUES('aaa', 'aaa', 'aaa', 'aaa', now(), now());
INSERT INTO provisioned_musics (id, itunes_artist_id, music_title, itunes_base_url, created_at, updated_at) VALUES('bbb', 'bbb', 'bbb', 'bbb', now(), now());
INSERT INTO promotion_videos (music_id, url, thumbnail_url, created_at, updated_at) VALUES('aaa', 'aaa', 'aaa', now(), now());
INSERT INTO promotion_videos (music_id, url, thumbnail_url, created_at, updated_at) VALUES('bbb', 'bbb', 'bbb', now(), now());
SELECT * FROM provisioned_musics WHERE id = 'aaa';
SELECT * FROM provisioned_musics WHERE id = 'bbb';
SELECT * FROM promotion_videos WHERE music_id = 'aaa';
SELECT * FROM promotion_videos WHERE music_id = 'bbb';
 */

}
