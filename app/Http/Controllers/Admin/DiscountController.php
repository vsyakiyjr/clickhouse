<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Discounts;
use Illuminate\Http\Request;
use Validator;

class DiscountController extends Controller {
	/**
	 * Display a listing of active resources.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function lists(Request $request) {
		return Discounts::orderBy("percentage", "asc")->get();
	}

	/**
	 * Store resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		$discounts = $request->all();
		foreach ($discounts as &$entity) {

			$validator = Validator::make($entity, [
				'from'       => 'required',
				'to'         => 'required',
				'percentage' => 'required',
			]);

			$entity['errors'] = false;

			if ($validator->fails()) {
				$entity['errors'] = $validator->errors();
			} else {
				$discount = Discounts::firstOrNew(['id' => $entity['id']], [
					'from'       => $entity['from'],
					'to'         => $entity['to'],
					'percentage' => $entity['percentage'],
				]);

				$discount->from = $entity['from'];
				$discount->to = $entity['to'];
				$discount->percentage = $entity['percentage'];

				$discount->save();
			}
		}

		return $discounts;
	}

	public function add() {
		$discount = Discounts::create([
			'from'       => 0,
			'to'         => 0,
			'percentage' => 0,
		]);

		return [
			'success' => true,
			'entity'  => $discount,
		];
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param int $id
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id) {
		Discounts::find($id)->delete();

		return ['success' => true];
	}
}
