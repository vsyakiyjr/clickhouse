<?php
$mainPageClass = 'auth-page';

?>

@extends('redesign.basic')

@section('content')
<div class="auth_page login_page">
    <form class="form-horizontal login__form" method="POST" action="{{ route('password.email') }}">
        @csrf

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
                   type="email"
                   class="form-control"
                   placeholder="Email"
                   name="email"
                   value="{{ old('email') }}"
                   required
            />

            @if ($errors->has('email'))
                <span class="help-block">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
            @endif
        </div>

        <div class="form-group">
            <div class="input_field_btn">
                <button type="submit" class="btn btn-orange btn-block btn-lg">
                    Сбросить пароль
                </button>
            </div>
        </div>
        <div class="additional_btns d-flex justify-content-end align-items-center">
            <span class="go_to_register d-flex align-items-center">
                <span>Уже зарегистрированы?</span>
                <a class="auth_footer_link" href="{{ route('login') }}">Вход</a>
            </span>
        </div>
    </form>
</div>
@endsection
