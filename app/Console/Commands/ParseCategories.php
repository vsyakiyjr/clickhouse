<?php

namespace App\Console\Commands;

use App\Services\ParserService;
use Illuminate\Console\Command;

class ParseCategories extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:categories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse categories with all sub-categories';

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

	    ParserService::parseCategoriesAndSubcategories();
    }
}
