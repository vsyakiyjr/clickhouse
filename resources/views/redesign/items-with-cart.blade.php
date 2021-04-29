
		@if(!empty($shortText))
			<p style="color: #828282; font-size: 14px; font-weight: 400">
				{!! $shortText !!}
			</p>
		@endif

		<h4 class="popular-products__title">Популярное в категории</h4>
		<div class="discount-wrap">
				@foreach($catalog as $item)
					@include('redesign.parts.product_card', ['item' => $item])
				@endforeach
		</div>
</div>
