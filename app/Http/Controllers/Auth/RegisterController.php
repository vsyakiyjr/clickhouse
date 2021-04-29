<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\Register;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller {
	/*
	|--------------------------------------------------------------------------
	| Register Controller
	|--------------------------------------------------------------------------
	|
	| This controller handles the registration of new users as well as their
	| validation and creation. By default this controller uses a trait to
	| provide this functionality without requiring any additional code.
	|
	*/

	use RegistersUsers;

	/**
	 * Where to redirect users after registration.
	 *
	 * @var string
	 */
	protected $redirectTo = '/';

	/**
	 * Create a new controller instance.
	 */
	public function __construct() {
		$this->middleware('guest');
	}

	/**
	 * Handle a registration request for the application.
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function register(Request $request) {
		$this->validator($request->all())->validate();

		event(new Registered($user = $this->create($request->all())));

		// $this->guard()->login($user);

		return $this->registered($request, $user)
			?: redirect($this->redirectPath());
	}

	/**
	 * Get a validator for an incoming registration request.
	 *
	 * @param array $data
	 *
	 * @return \Illuminate\Contracts\Validation\Validator
	 */
	protected function validator(array $data) {
		return Validator::make($data, [
			'phone'    => 'required',
			'email'    => 'required|string|email|max:255|unique:users',
			'password' => 'required|string|min:6|confirmed',
			'terms'    => 'required',
		]);
	}

	/**
	 * Create a new user instance after a valid registration.
	 *
	 * @param array $data
	 *
	 * @return User
	 */
	protected function create(array $data) {
		$user = User::create([
			'phone'    => $data['phone'],
			'email'    => $data['email'],
			'password' => bcrypt($data['password']),
		]);

		$user->notify(new Register($user));
		//Mail::to($user->email)->send(new Register($user));

		Auth::logout();

		return $user;
	}

	public function activate(Request $request) {
		if ($request->id) {
			$user = User::find($request->id);
			$user->active = true;
			$user->save();

			return view('auth.login', ['activate' => true]);
		}

		return redirect('/');
	}

	public function showRegistrationForm() {
		return view('auth.register', ['noHeader' => true]);
	}
}
