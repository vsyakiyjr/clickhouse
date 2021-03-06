<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LoginController extends Controller {
	/*
	|--------------------------------------------------------------------------
	| Login Controller
	|--------------------------------------------------------------------------
	|
	| This controller handles authenticating users for the application and
	| redirecting them to your home screen. The controller uses a trait
	| to conveniently provide its functionality to your applications.
	|
	*/

	use AuthenticatesUsers;

	/**
	 * Where to redirect users after login.
	 *
	 * @var string
	 */
	protected $redirectTo = '/';

	/**
	 * Create a new controller instance.
	 */
	public function __construct() {
		$this->middleware('guest')->except('logout');
	}

	public function showLoginForm() {
		return view('auth.login', ['noHeader' => true]);
	}

	public function authenticated(Request $request, $user) {
		$user->api_token = Str::random(100);
		$user->save();
	}

	protected function credentials(Request $request) {
		return array_merge($request->only($this->username(), 'password'), ['active' => 1]);
	}
}
