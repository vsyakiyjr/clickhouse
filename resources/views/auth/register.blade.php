<?php
$mainPageClass = 'auth-page';

?>

@extends('redesign.basic')

@section('content')

<div class="auth_page login_page">
    <form class="form-horizontal login__form" method="POST" action="{{ route('register') }}">
        @csrf

        <div class="text-center logo_box">
            <a href="/">
                <img src="/images/logo.png" alt="" class="jumboLogo-size logo_box__img">
            </a>
        </div>

        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">

            <input id="email"
                   type="email"
                   class="form-control"
                   autofocus
                   name="email"
                   placeholder='Email'
                   value="{{ old('email') }}"
                   required
            />

            @if ($errors->has('email'))
                <span class="help-block">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
            @endif
        </div>

        <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">

            <input id="phone"
                   type="tel"
                   class="form-control"
                   name="phone"
                   placeholder="Телефон"
                   value="{{ old('phone') }}"
                   required
            />

            @if($errors->has('phone'))
                <span class="help-block">
                        <strong>{{ $errors->first('phone') }}</strong>
                    </span>
            @endif
        </div>

        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">

            <input id="password" type="password" class="form-control" placeholder='Пароль' name="password" required />

            @if($errors->has('password'))
                <span class="help-block">
                    <strong>{{ $errors->first('password') }}</strong>
                </span>
            @endif
        </div>

        <div class="form-group">

            <input id="password-confirm"
                   type="password"
                   class="form-control"
                   placeholder='Повторите пароль'
                   name="password_confirmation"
                   required
            />
        </div>
        
        <div class="form-group terms">
            <input id="terms" type="checkbox" name="terms" value="1" />

            <label for="terms"
                   class='agree_text'>
                * Я даю согласие
                <a class="auth_footer_link"
                   href="">
                    на обработку моих персональных данных
                </a>
            </label>
            
            @if ($errors->has('terms'))
                <span class="help-block">
                    <strong>{{ $errors->first('terms') }}</strong>
                </span>
            @endif
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-orange btn-block btn-lg">
                Зарегистрироваться
            </button>
        </div>
        <div class="additional_buttons">
            <span>Уже зарегистрированы?</span>
            <a class="auth_footer_link" href="{{ route('login') }}">Вход</a>
        </div>
        <socials-auth />
    </form>
</div>

@endsection
