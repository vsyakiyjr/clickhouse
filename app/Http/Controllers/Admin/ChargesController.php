<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Charge;
use Illuminate\Http\Request;
use Validator;

class ChargesController extends Controller {
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function lists() {
		return Charge::orderBy('id')->get();
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @param Request $request
	 *
	 * @return void
	 */
	public function store(Request $request) {
		$charges = $request->all();

		foreach ($charges as &$entity) {

			$validator = Validator::make($entity, [
				'total_from' => [
					'nullable',
					'numeric',
				],
				'total_to'   => [
					'nullable',
					'numeric',
				],
				'type'       => [
					'required',
					'in:fixed,percentage',
				],
				'amount'     => [
					'required',
					'numeric',
				],
			]);

			$entity['errors'] = false;

			if ($validator->fails()) {
				$entity['errors'] = $validator->errors();
			} else {
				$charge = Charge::firstOrNew(['id' => $entity['id']], [
					'total_from' => $entity['total_from'],
					'total_to'   => $entity['total_to'],
					'type'       => $entity['type'],
					'amount'     => $entity['amount'],
				]);

				$charge->total_from = $entity['total_from'];
				$charge->total_to = $entity['total_to'];
				$charge->type = $entity['type'];
				$charge->amount = $entity['amount'];

				$charge->save();
			}
		}

		return $charges;
	}

	public function add() {
		$charge = Charge::create([
			'total_from' => 0,
			'total_to'   => 0,
			'amount'     => 0,
			'type'       => 'fixed',
		]);

		return [
			'success' => true,
			'entity'  => $charge,
		];
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param int $id
	 *
	 * @return \Illuminate\Http\Response
	 * @throws \Exception
	 */
	public function destroy($id) {
		Charge::find($id)->delete();

		return ['success' => true];
	}
}
