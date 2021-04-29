<?php
$uri = trim(Request::getRequestUri(), '/');

?>

@extends('redesign.basic')
@section('content')
    <div class="info-page {{$uri}}">
        <div class="container">
            <div class="info-page__inner">
                <div class="content_item__sidebar">
                    @include('layouts.sidebar.pages')
                </div>
                <div class="info-page__wrap">
                    <div class="info-page__col">
                        <h1>{{ $page->title }}</h1>
                        <div class="info-page__text">
                            <p>{{$page->text}}</p>
                        </div>
                        <div class="info-page__img-mobile">
                            <img src="/images/redesign/{{ $page->alias }}.svg" alt="{{ $page->alias }}">
                        </div>
                        @foreach($page->blocks as $block)
                            <div class="info-page__faq">
                                <h2>{{$block->title}} <img src="/images/redesign/icons/arrow-down.svg" alt="arrow"></h2>
                                <div class="info-page__faq-hidden">
                                    <p><?php echo nl2br($block->text); ?></p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="info-page__img">
                        <img src="/images/redesign/{{ $page->alias }}.svg" alt="{{ $page->alias }}">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection