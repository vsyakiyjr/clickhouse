@extends('redesign.basic')

@php /** @var \App\Models\Cms\Directory $directory */ @endphp

@section('title', $directory->getDescription())

@section('body-class', 'has-mobile')

@section('content')
	<div class="blog-page">
		<div class="container">
			{{-- @component('cms.pageBreadcrumbs', ['breadCrumbs' => array_reverse($directory->getNestingChain($directory))])@endcomponent --}}

			<h1 class="blog-page__title">{{$directory->getDescription()}}</h1>

			@include('cms.blocks.directoryLinks')
		</div>
	</div>
@endsection

@push('css')
	<link rel="stylesheet" href="/cms-assets/cms.css?t={{time()}}" media="all" />
@endpush
