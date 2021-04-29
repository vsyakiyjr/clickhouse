@extends('redesign.basic')

@section('content')
	<section class="search-section">
		<div class="container">
			@include('redesign.items-with-cart', ['title' => 'Результаты поиска'])
		</div>
	</section>
@endsection

@push('js')
	<script>
		if(fbq) {
			fbq('track', 'Search');
		}
	</script>
@endpush

{{-- чтобы товары добавлялись в корзину --}}
<div id="app">
	<div>
		<cart type="widget"></cart>
	</div>
</div>