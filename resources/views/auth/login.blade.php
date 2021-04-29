<?php
$mainPageClass = 'auth-page';

?>
@extends('redesign.basic')

@section('content')

<div class="auth_page login_page">
    <form class="form-horizontal login__form" method="POST" action="{{ route('login') }}">
        @csrf

        @if(isset($activate) && $activate)
            <div class='activate-success'>
                Подравляем ваш аккаунт активирован, теперь вы можете войти в личный кабинет!
            </div>
        @endif
        <div class="text-center logo_box">
            <a href="/">
                <img src="/images/logo.png" alt="" class="jumboLogo-size logo_box__img">
            </a>
            {{--<div class="text-center logo_box__text">
                <span>Доставка товаров из IKEA по</span>
                <img src="/images/icons/by_flag.png" style="width: 20px" class="mb-1">
            </div>--}}
        </div>

        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
            <input id="email"
                   class="form-control"
                   type="email"
                   name="email"
                   value="{{ old('email') }}"
                   placeholder='E-mail'
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
                   class="form-control"
                   type="password"
                   name="password"
                   placeholder='Пароль'
                   required
            />

            @if ($errors->has('password'))
                <span class="help-block">
                    <strong>{{ $errors->first('password') }}</strong>
                </span>
            @endif
        </div>

        <div class="form-group hidden col-md-6 col-md-offset-4">
            <label for="remember">
                Запомнить меня
            </label>
            <input id="remember" type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }} />
        </div>

        <div class="form-group">
            <div class="input_field_btn">
                <button type="submit" class="btn btn-orange btn-lg btn-block">Войти</button>
            </div>
        </div>
        <div class="additional_btns d-flex justify-content-between align-items-center">
            <a class="auth_footer_link" href="{{ route('password.request') }}">Забыли пароль?</a>
            <span class="go_to_register d-flex align-items-center">
                <span class="not-registered">Еще не зарегистрированы?</span>
                <a class="auth_footer_link" href="{{ route('register') }}">Регистрация</a>
            </span>
        </div>
        <socials-auth />
    </form>
</div>
@endsection
