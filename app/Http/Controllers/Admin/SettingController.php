<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Validator;

class SettingController extends Controller {
	public function index(Request $request) {
		return Setting::all();
	}

	/**
	 * Store resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		$validator = Validator::make($request->all(), [
			'key'   => 'required',
			'value' => 'required',
		]);

		if ($validator->fails()) {
			return [
				'success' => false,
				'errors'  => $validator->errors(),
			];
		}

		$setting = Setting::firstOrNew(['key' => $request->key], [
			'key'   => $request->key,
			'value' => $request->value,
		]);

		$setting->key = $request->key;
		$setting->value = $request->value;

		$setting->save();

		return ['success' => true];
	}
}
