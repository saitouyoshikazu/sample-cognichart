<?php

namespace App\Console\Commands\SEO;

use Illuminate\Console\Command;
use App\Domain\SEO\SEOServiceInterface;

class SiteMapXml extends Command
{

    private $seoService;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SEO:sitemapxml';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create sitemap.xml file.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        SEOServiceInterface $seoService
    ) {
        $this->seoService = $seoService;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->seoService->sitemapxml();
    }

}
