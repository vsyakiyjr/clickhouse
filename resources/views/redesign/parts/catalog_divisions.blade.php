<div class="catalog-divisions">
	@foreach($catalogDivisions as $item)
		<ul class="category-list">
			<div class="category-list__head">
				<a href="{{$item->url}}">
					@if ($item->img)
						<img src="{{ $item->img }}" alt="{{ $item->name }}">
					 @else
					 	<img src="/images/redesign/catimg.jpg" alt="{{ $item->name }}">
					@endif
				</a>
			   <a class="catalog-division" href="{{$item->url}}">{{$item->name}}</a>
			</div>
			@if ($item->subcategories)
				@foreach ($item->subcategories as $subcat)
					<li><a href="{{ $subcat->url }}">{{ $subcat->name }}</a></li>
				@endforeach
			@endif
		</ul>
	@endforeach
</div>

@if (isset($catalog))
<div style="margin-top: 60px" class="discount-wrap">
	@foreach($catalog as $item)
		@include('redesign.parts.product_card', ['item' => $item])
	@endforeach
</div>
@endif


{{-- чтобы товары добавлялись в корзину --}}
<div>
	<cart type="widget"></cart>
</div>