<?php

namespace App\Http\Controllers;

use App\Mail\Callback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class CallbackContrtoller extends Controller {
	public function send(Request $request) {
		$attributes = [
			'phone' => $request->phone,
		];

		Mail::send(new Callback($attributes));

		return response()->json(['success' => true]);
	}
}
