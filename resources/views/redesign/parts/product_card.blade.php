<div class="product product-card {{cartHasItem($item->id) ? 'added-to-cart' : ''}}" data-product-id="{{$item->id}}" >
	
	<a class="image" href="/catalog/product/{{$item->vendor_code}}">
		<img {{imgSrc($item->photo)}}
			 alt="{{$item->name}}"
			 class="goodImg-size"
		/>
	</a>
	@if($item->family_discount_percent)
	<div class="product-discount">
		<p>-{{$item->family_discount_percent}}%</p>
		<div class="price-amount old-price RUB" data-rub-price="{{ $item->price }}" style="display: none"><div id="priceRub"></div> RUB</div>
	  <div class="price-amount old-price BYN" style="display: none">{{ rubInByn($item->price, true) }} BYN</div>
	</div>
	@endif
	<div class="price">
		{{--TODO wtf old copypaste, refactor--}}
		@if($item->family_price)
	  <div class="price-amount with-discount RUB" data-rub-price="{{ ceil($item->price_final) }}" style="display: none"><div id="priceRub"></div> <span>RUB</span></div>
      <div class="price-amount with-discount BYN" style="display: none">{{ rubInByn($item->price_final, true) }} <span>BYN</span></div>

			@if($item->fixed_price)
				<div class="price-type">
					Фикс. цена {{ $item->fixed_price }} <span>BYN</span>
				</div>
			@else
				<div class="price-type">
					+ комиссия за доставку
				</div>
			@endif
		@else
			<div class="price-amount RUB" data-rub-price="{{$item->price}}" style="display: none"><div id="priceRub"></div> <span>RUB</span></div>
			<div class="price-amount BYN" style="display: none">{{ rubInByn($item->price, true) }} <span>BYN</span></div>
			@if($item->fixed_price)
				<div class="price-type">
					Фикс. цена {{ $item->fixed_price }} <span>BYN</span>
				</div>
			@else
				<div class="price-type">
					+ комиссия за доставку
				</div>
			@endif
		@endif
	</div>
	<div class="title">
		{{$item->name}}
	</div>

	<div class="description">
		{{$item->type}}

		@foreach((array)$item->attributes as $attributeName => $attributeValue)
			<span class="catalog_attr catalog_term">{{$attributeValue}}</span>
		@endforeach

	{{--		@if($item->color)--}}
	{{--			{{implode(', ', json_decode($item->color, true) ?? [])}}--}}
	{{--		@endif--}}
	</div>

	<div class="add-to-cart _add_to_cart">
		В корзину
	</div>

</div>
