@extends('redesign.basic')

@section('content')
	<section class="category">
		<div class="container">

			@if(isset($withCatalogDivisions) && $withCatalogDivisions)
				<div class="catalog-divisions-container">
					@if(!empty($title))
						<h4 class="category-title">{{$title}}</h4>
					@endif
					@include('redesign.parts.catalog_divisions')
				</div>
			@endif
		</div>
	</section>
@endsection