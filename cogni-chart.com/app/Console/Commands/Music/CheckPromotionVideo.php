<?php

namespace App\Console\Commands\Music;

use Illuminate\Console\Command;
use Config;
use App\Application\Music\MusicApplicationInterface;
use App\Application\DXO\MusicDXO;

class CheckPromotionVideo extends Command
{

    private $musicApplication;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CheckPromotionVideo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command execute check of promotion video following to settings of .env file.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(MusicApplicationInterface $musicApplication)
    {
        parent::__construct();
        $this->musicApplication = $musicApplication;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $settings = Config::get('app.check_promotion_video');

        $today = new \DatetimeImmutable(date('Y-m-d'));

        $musicDXO = new MusicDXO();
        if (!empty($settings['created_at_gte'])) {
            $paramDate = $today->sub(new \DateInterval($settings['created_at_gte']));
            $musicDXO->checkPromotionVideoAppendCreatedAtGTE($paramDate->format('Y-m-d'));
        }
        if (!empty($settings['created_at_lt'])) {
            $paramDate = $today->sub(new \DateInterval($settings['created_at_lt']));
            $musicDXO->checkPromotionVideoAppendCreatedAtLT($paramDate->format('Y-m-d'));
        }
        if (!empty($settings['music_id_like'])) {
            $musicDXO->checkPromotionVideoAppendMusicIdLike($settings['music_id_like']);
        }
        $this->musicApplication->checkPromotionVideo($musicDXO);
    }

}
