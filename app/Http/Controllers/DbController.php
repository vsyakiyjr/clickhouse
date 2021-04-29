<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class DbController extends Controller {
	public function resetProducts(Request $request) {
		DB::statement('SET FOREIGN_KEY_CHECKS=0;');

		DB::table('products_subcategories')->truncate();
		DB::table('show_products')->truncate();
		DB::table('products')->truncate();
		DB::table('subcategories')->truncate();
		DB::table('categories')->truncate();

		DB::statement('SET FOREIGN_KEY_CHECKS=1;');
	}
}
