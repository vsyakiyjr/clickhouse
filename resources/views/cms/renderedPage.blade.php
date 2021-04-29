@extends('redesign.basic')

<?php /** @var \App\Models\Cms\Page $page */ ?>

@section('title', $page->getTitle())

{{-- @section('body-class', 'has-mobile') --}}

@section('content')
	@include("cms.blocks.genericArticle")
@endsection

@push('css')
	<link rel="stylesheet" href="/cms-assets/cms.css?t={{time()}}" media="all" />
@endpush