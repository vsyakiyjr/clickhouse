@php
$settings = App\Models\Setting::where('key', 'contacts')->first();
$data = $settings ? json_decode($settings->value, true) : false;
$uri = trim(Request::getRequestUri(), '/');

@endphp

@extends('redesign.basic')
@section('content')
    <div class="info-page {{ $uri }}">
        <div class="container">
            <div class="info-page__inner">
                <div class="content_item__sidebar">
                    @include('layouts.sidebar.pages')
                </div>
                <div class="info-page__wrap">
                    <div class="info-page__col">
                        <h1>Контакты</h1>
                        <div class="info-page__img-mobile">
                            <img src="/images/redesign/contacts.svg" alt="contacts">
                        </div>
                        <div class="info-page__contacts">
                            <div>
                                <div class="info-page__contacts-icon">
                                    @if ($data)
                                        @foreach ($data['vibers'] as $viber)
                                            <a href="viber://chat?number={{ $viber }}" target="_blank">
                                                <img src="/images/redesign/icons/viber.svg" alt="viber">
                                            </a>
                                        @endforeach
                                    @endif
                                    <a href="https://api.whatsapp.com/send?phone=+375447721313" target="_blank">
                                        <img src="/images/redesign/icons/whatsapp.svg" alt="whatsapp">
                                    </a>
                                </div>
                                @if ($data)
                                    @foreach ($data['phones'] as $phone)
                                        <a href="tel:{{ $phone }}">{{ $phone }}</a>
                                    @endforeach
                                @endif
                            </div>
                            <div>
                                <div class="info-page__contacts-icon">
                                    <span><img src="/images/redesign/icons/icon-clock.svg" alt="clock"></span>
                                </div>
                                <p>{{ $data['work_time'] }}</p>
                            </div>
                        </div>
                        <div class="info-page__contacts mobile-st">
                            @if ($data)
                                <div>
                                    <div class="info-page__contacts-icon">
                                        <span><img src="/images/redesign/icons/mail.svg" alt="mail"></span>
                                    </div>
                                    @foreach ($data['emails'] as $email)
                                        <a href="mailto:{{ $email }}" target="_blank">{{ $email }}</a>
                                    @endforeach
                                </div>
                            @endif
                            @if($data['social']['instagram']['status'])
                            <div>
                                <div class="info-page__contacts-icon">
                                    <a href="{{ $data['social']['instagram']['link'] }}" target="_blank">
                                        <span><img src="/images/redesign/icons/insta.svg" alt="insta"></span>
                                    </a>
                                </div>
                            </div>
                            @endif
                            @if($data['social']['telegram']['status'])
                            <div>
                                <div class="info-page__contacts-icon">
                                <a href="{{ $data['social']['telegram']['link'] }}" target="_blank">
                                <span><img src="/images/redesign/icons/telegram.svg" alt="telegram"></span>
                                </a>
                                </div>
                            </div>
                            @endif
                        </div>
                        <div class="info-page__contacts mobile-st">
                            @if($data['social']['vkontakte']['status'])
                                <div>
                                    <div class="info-page__contacts-icon">
                                    <a href="{{ $data['social']['vkontakte']['link'] }}" target="_blank"><span><img src="/images/redesign/icons/vk.svg" alt="vk"></span>
                                    </a>
                                    </div>
                                </div>
                            @endif
                            @if($data['social']['facebook']['status'])
                            <div>
                                <div class="info-page__contacts-icon">
                                    <a href="{{ $data['social']['facebook']['link'] }}" target="_blank">
                                        <span><img src="/images/redesign/icons/fb.svg" alt="fb"></span>
                                    </a>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="info-page__img">
                        <img src="/images/redesign/contacts.svg" alt="contacts">
                    </div>
                </div>
            </div>
        </div>
        {{-- <div class="content_item content_item__catalog">
            <div class="contact_inner_wrap d-flex justify-content-between content_info_page">
                <div class="contact_item contact_item__left">
                    <h1 class="info_page_title">
                        <a href="/info" class="go-to-info-mobile">
                            <span class="fa fa-chevron-left"></span>
                        </a>

                        Контакты

                    </h1>
                    <div class="contactBlock">
                        <div class="contact_box d-flex">
                            <div class="icons-mobile">
                                <img src="/images/icons/phone_icon.png"
                                     width="25"
                                     alt="">

                                <img src="/images/icons/icon_viber.png"
                                     width="25"
                                     alt="">
                            </div>
                            
                            @if ($data)
                                <ul class="contact_list contact_phones">
                                    @foreach ($data['phones'] as $phone)
                                        <li><a href="tel:{{ $phone }}">{{ $phone }}</a></li>
                                    @endforeach
                                </ul>
                            @endif
                            <button class="btn call_back_btn _contacts_modal_trigger">Заказать звонок</button>
                        </div>
                        <div class="contact_box d-flex">
                            @if ($data)
                                <ul class="contact_list contact_viber">
                                    @foreach ($data['vibers'] as $viber)
                                        <li><a href="tel:{{ $viber }}">{{ $viber }} <span class="ttext_gray">(Viber)</span></a></li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                        <div class="contact_box d-flex">
                            @if ($data)
                                <ul class="contact_list contact_mail">
                                    @foreach ($data['emails'] as $email)
                                        <li><a href="mailto:{{ $email }}">{{ $email }}</a></li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>

                    </div>
                </div>
                <div class="contact_item contact_item__right">
                    <div class="contact_box d-flex">
                        @if ($data)
                            <ul class="contact_list contact_work_time">
                                <li><span>Рабочее время: {{ $data['work_time'] }}</span></li>
                            </ul>
                        @endif
                    </div>
                </div>

                <div class="contact_item inst">
                    <a class="inst-link"
                       ref="nofollow,norefferer"
                       target="_blank"
                       href="https://instagram.com/scandimania.by">
                        <img src="/images/icons/new/inst.png"
                             width="25"
                             alt="Ikeamania instagram">
                    </a>
                </div>
            </div>
        </div> --}}
    </div>
@endsection
