<?php

namespace App\Console\Commands;

use App\Services\ParserService;
use Illuminate\Console\Command;

class ParseCatalog extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:catalog {--ids=} {--clear=0} {--process=} {--sitemap_url=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     */
    public function __construct() {
        parent::__construct();
    }

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 * @throws \Exception
	 */
    public function handle() {
	    ini_set('display_errors', '1');
	    ini_set('display_startup_errors', '1');
	    ini_set('max_execution_time', 0); // no run time limit
	    ini_set('memory_limit', -1); // no memory limit

        $ids = $this->option('ids');
        $processId = $this->option('process');
        $subcategoriesIds = explode(',', $ids);
        $sitemapUrl = $this->option('sitemap_url');
		// php artisan parse:catalog --ids=6,7,8,9,10,11,12,13,14,15,16,17,18,19,20 --process=4

	    $clear = $this->option('clear');

	    if($clear) {
	        ParserService::clearProducts($subcategoriesIds, $processId);
	    } else {
		    ParserService::saveProductsFromSitemaps($processId, $sitemapUrl);
	    }

	    return 0;
    }
}
