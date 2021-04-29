<?php

namespace App\Http\Controllers;

use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Validator;

class AccountController extends Controller {
	public function index() {

		return view('redesign.pages.account', [
			'user'   => Auth::user(),
		]);
	}

	public function save(Request $request) {
		$validator = Validator::make($request->all(), [
			'email'    => 'required|email',
			'phone'    => 'required',
			'password' => 'confirmed',
		]);

		if ($validator->fails()) {
			return redirect('account')->withErrors($validator)->withInput();
		}

		/** @var User $user */
		$user = Auth::user();

		$user->email = $request->email;
		$user->phone = $request->phone;

		if ($request->password) {
			$user->password = bcrypt($request->password);
		}

		$user->save();

		return redirect('account')->with('status', 'Сохранено!');
	}
}
