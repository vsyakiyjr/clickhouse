<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Services\ParserService;
use Illuminate\Console\Command;

class ClearOldProducts extends Command {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'parse:clear_old_products {--ids=}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Clear not existing products for given subcategories ids';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle() {
		ini_set('display_errors', '1');
		ini_set('display_startup_errors', '1');
		ini_set('max_execution_time', 0); // no run time limit
		ini_set('memory_limit', -1); // no memory limit
		ini_set('max_execution_time', 0);

		$ids = $this->option('ids');
		$subcategoriesIds = explode(',', $ids);

		$products = Product::whereHas('subcategories', function ($q) use ($subcategoriesIds) {
			$q->whereIn('id', $subcategoriesIds);
		})->cursor();

		foreach ($products as $product) {
			/** @var Product $product */
			$parsedProduct = ParserService::parseProductPage($product->vendor_code, $product->link);

			if(!$parsedProduct){
				$product->delete();
			}
		}
	}
}
