<?php

namespace App\Http\Controllers\WWW\Music;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Application\Music\MusicApplicationInterface;
use App\Expand\Validation\ExpValidation;
use App\Application\DXO\MusicDXO;
use App\Domain\ValueObjects\Phase;

class MusicController extends Controller
{

    private $musicApplication;

    public function __construct(MusicApplicationInterface $musicApplication)
    {
        $this->musicApplication = $musicApplication;
    }

    public function resetPromotionVideos(Request $request)
    {
        $response = ['error' => '', 'musics' => []];
        $expValidator = new ExpValidation(['music_ids', 'music_ids.*']);
        $validator = $expValidator->validateOnly($request->all());
        if ($validator->fails()) {
            $response['error'] = $validator->errors()->all();
            return $response;
        }

        $musicIdValues = $request->input('music_ids');
        if (!empty($musicIdValues)) {
            foreach ($musicIdValues AS $musicIdValue) {
                if (!isset($response['musics'][$musicIdValue])) {
                    $musicDXO = new MusicDXO();
                    $musicDXO->find(Phase::released, $musicIdValue);
                    $musicEntity = $this->musicApplication->find($musicDXO);
                    if (!empty($musicEntity) && !empty($musicEntity->promotionVideoUrl())) {
                        $iTunesBaseUrlValue = "";
                        if (!empty($musicEntity->iTunesBaseUrl())) {
                            $iTunesBaseUrlValue = $musicEntity->iTunesBaseUrl()->value();
                        }
                        $response['musics'][$musicIdValue]['youtubeId'] = $musicEntity->promotionVideoUrl()->value();
                        $response['musics'][$musicIdValue]['iTunesBaseUrl'] = $iTunesBaseUrlValue;
                    } else {
                        $response['musics'][$musicIdValue] = '';
                    }
                }
            }
        }
        return $response;
    }

}
