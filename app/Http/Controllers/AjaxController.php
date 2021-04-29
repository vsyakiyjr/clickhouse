<?php

namespace App\Http\Controllers;

use App\Services\ExchangeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Cache;

class AjaxController extends Controller {

	public function getExchange(){
		return ['exchange' => ExchangeService::getExchange()];
	}

	public static function saveExchange(Request $request) {
		$exchanges = $request->exchange;
		$tabl_exchanges = DB::table('exchanges')->get();
		if (empty($tabl_exchanges)) {
			$tabl_exchanges = DB::table('exchanges')->insert(['echange_rates' => $exchanges]);
		} else {
			$tabl_exchanges = DB::table('exchanges')->where('id', 1)->update(['echange_rates' => $exchanges]);
		}

		Cache::flush();
	}
}
