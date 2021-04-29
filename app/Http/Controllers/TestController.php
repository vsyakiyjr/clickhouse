<?php

namespace App\Http\Controllers;

use App\Models\ParserStatus;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Subcategory;
use App\Models\ProductsVisible;
use App\Models\User;
use App\Services\ParserService;
use App\StringNormalize;
use DB;
use Hash;

class TestController extends Controller {
	function test() {
        $product = Product::where(['vendor_code' => '29304398'])->first();

        dd($product);
	}

}
