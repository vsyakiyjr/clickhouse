<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PromoCode;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class PromoController extends Controller {

	/**
	 * @return PromoCode[]|Collection
	 */
	public function getList(){
		return PromoCode::query()->orderBy('id', 'asc')->get();
	}

	/**
	 * @param Request $request
	 * @return PromoCode[]|Collection
	 */
	public function addCode(Request $request){
		$data = $request->all();
		$data['usage_limit'] = empty($data['usage_limit']) ? null : (int)$data['usage_limit'];

		PromoCode::firstOrCreate(
			['id' => $request->get('id')],
			$data
		);

		return $this->getList();
	}

	/**
	 * @param Request $request
	 * @return PromoCode[]|Collection
	 * @throws \Exception
	 */
	public function deleteCode(Request $request){
		PromoCode::find($request->get('id'))->delete();

		return $this->getList();
	}

}
