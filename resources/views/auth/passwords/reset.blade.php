<?php
$mainPageClass = 'auth-page';

?>

@extends('redesign.basic')

@section('content')

<div style="height: 91vh;" class="auth_page login_page">
    <reset-password></reset-password>
    <!-- <form class="form-horizontal login__form" method="POST" action="{{ route('password.request') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}" />

        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">

            <input id="email"
                   type="email"
                   class="form-control"
                   name="email"
                   placeholder='E-Mail'
                   value="{{ $email ?? old('email') }}"
                   required
                   autofocus
            />

            @if ($errors->has('email'))
                <span class="help-block">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
            @endif
        </div>

        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">

            <input id="password"
                   type="password"
                   class="form-control"
                   placeholder="Пароль"
                   name="password"
                   required
            />

            @if($errors->has('password'))
                <span class="help-block">
                    <strong>{{ $errors->first('password') }}</strong>
                </span>
            @endif
        </div>

        <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
            <input id="password-confirm"
                   type="password"
                   class="form-control"
                   placeholder="Пароль еще раз"
                   name="password_confirmation"
                   required
            />

            @if($errors->has('password_confirmation'))
                <span class="help-block">
                    <strong>{{ $errors->first('password_confirmation') }}</strong>
                </span>
            @endif
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-block btn-orange btn-lg">
                Сбросить пароль
            </button>
        </div>

        <div class="additional_buttons">
            <span>Уже зарегистрированы?</span>
            <a class="auth_footer_link" href="{{ route('login') }}">Вход</a>
        </div>
    </form> -->
</div>
@endsection
