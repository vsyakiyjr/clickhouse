@extends('redesign.basic')
@section('content')

	<section class="account-page">
		<div class="container">
		<h1>Личный кабинет</h1>

		<div class="account_page__inner d-flex">
			<div class="account-page__status">
				@if (session('status'))
					{{ session('status') }}
				@endif
			</div>
			<div class="account_page_item accoutnt_page_item__left">
				<div class="account_page_item__body">
					<form class="form-horizontal "
						  method="POST"
						  action="{{ route('account') }}">
						{{ csrf_field() }}
						<div class="input_field {{ $errors->has('email') ? ' has-error' : '' }}">
							<input type="email"
								   name="email"
								   placeholder="example@mail.com"
								   value="{{ old('email') ? old('email') : $user->email }}">
							@if ($errors->has('email'))
								<span class="error">{{ $errors->first('email') }}</span>
							@endif
						</div>
						<div class="input_field {{ $errors->has('phone') ? ' has-error' : '' }}">
							<input type="tel"
								   name="phone"
								   value="{{ old('phone') ? old('phone') : $user->phone }}"
								   placeholder="+ 375 (xx) xxx-xx-xx">
							@if ($errors->has('phone'))
								<span class="error">{{ $errors->first('phone') }}</span>
							@endif
						</div>
						<div class="input_field {{ $errors->has('password') ? ' has-error' : '' }}">
							<input type="password"
								   name="password"
								   placeholder="Новый пароль">
							@if ($errors->has('password'))
								<span class="error">{{ $errors->first('password') }}</span>
							@endif
						</div>
						<div class="input_field {{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
							<input type="password"
								   name="password_confirmation"
								   placeholder="Подтвердите пароль">
							@if ($errors->has('password_confirmation'))
								<span class="error">{{ $errors->first('password_confirmation') }}</span>
							@endif
						</div>
						<div class="input_field input_field__button">
							<button type="submit"
									class='btn btn-orange btn-xxl btn-lg'>Сохранить
							</button>
						</div>
						<a class="logout-link" href="/logout">Выйти</a>
					</form>
				</div>
			</div>
			</div>
		</div>
	</section>
@endsection
