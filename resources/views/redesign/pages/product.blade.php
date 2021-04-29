<?php
use App\Models\Product;
/** @var Product $product */

?>

@extends('redesign.basic')

@section('content')
<section class="product-page" data-product-id="{{ $product->id }}">
	<div class="container">
		@include('redesign.parts.breadcrumbs')
		<div class="product-page__wrap">
			<div class="product-page__about">
				<div class="product-page__slider">
					<?php
					try {
						$images = json_decode($product->gallery, true)['large'];
					} catch (\Throwable $e){
						$images = [];
					}
					$images = empty($images) ? [$product->photo] : $images;
					?>

					@if($images)
						@foreach($images ?? [] as $additional)
							<div class="product-page__slider-item">
								<img {{ imgSrc($additional) }}>
							</div>
						@endforeach
					@endif
				</div>
				<button class="product-page__more">Больше изображений</button>
			</div>
			<div class="product-info">
				<h1 class="product-info__title">{{ $product->name }}</h1>
				<div class="product-info-wrapper">
					<p class="product-info__articul">Артикульный номер: {{ $product->vendor_code_with_dots }}</p>
					<div class="product-info__value product-mobile">
						<span>Количество:</span>
						<div class="qty-selector">
							<div class="qty-minus"><img src="/images/redesign/icons/minus.svg" alt="minus"></div>
							<input type="text" max="999" pattern="[0-9]+" class="qty-value" value="1">
							<div class="qty-plus"><img src="/images/redesign/icons/plus.svg" alt="plus"></div>
						</div>
					</div>
				</div>
				<div class="product-info__desc">
					{{ $product->type }}
					@if($attributesCombination && is_array($attributesCombination))
						{{ implode(', ', $attributesCombination) }}
					@endif
				</div>
				<div class="line combinations">
						<div style="display: none !important;" class="attributes-combination">
							{{ json_encode($product->attributes) }}
						</div>

						<div style="display: none !important;" class="possible-attributes-combination">
							{{ json_encode($possibleAttributes) }}
						</div>

						@if(!empty($product->possible_attributes))
							@foreach($product->possible_attributes ?? [] as $attributeName => $attributeValues)
								@if(count($attributeValues) > 1)
									<div class="attribute">
										<span class="line-label">{{ mb_convert_case($attributeName, MB_CASE_TITLE) }}:</span>
										<select class="attribute-select form-control"
												name="{{$attributeName}}">
											@foreach($attributeValues ?? [] as $attributeValue)
												<option {{ !empty($product->attributes[$attributeName]) && $product->attributes[$attributeName] == $attributeValue ? 'selected' : '' }}
														value="{{$attributeValue}}">
													{{ $attributeValue }}
												</option>
											@endforeach
										</select>
										<span class="select-arrow"><img src="/images/redesign/icons/arrow-down.svg" alt="arrow-down"></span>
									</div>
								@else
									<div class="d-flex quantity_box">
										@if($attributeName !== 'размеры' && $attributeName !== 'размер')
											<span class="line-label">
												{{ mb_convert_case($attributeName, MB_CASE_TITLE) }}:&nbsp;
											</span>

											{{ implode(':  ', $attributeValues ?? []) }}
										@endif
									</div>
								@endif
							@endforeach
						@endif
					</div>
				<div class="product-info__price">
					<p>Стоимость товара:</p>
					<div class="price-amount RUB"
						data-rub-price="{{ $product->family_price ? $product->family_price :  $product->price }}"
						style="display: none">
						<div id="priceRub"></div>
						<span>RUB</span>
					</div>
					<div class="price-amount BYN" style="display: none">
						{{ $product->family_price ? rubInByn($product->family_price, true) : rubInByn($product->price, true) }}
						<span>BYN</span></div>
				</div>
				<div class="product-info__value">
					<span class="product-desktop">Количество:</span>
					<div class="qty-selector product-desktop">
						<div class="qty-minus"><img src="/images/redesign/icons/minus.svg" alt="minus"></div>
						<input type="text" max="999" pattern="[0-9]+" class="qty-value" value="1">
						<div class="qty-plus"><img src="/images/redesign/icons/plus.svg" alt="plus"></div>
					</div>
					<div class="product-info__deliv">
						@if($product->fixed_price)
						<p>фикс. цена {{ ceil($product->fixed_price) }} BYN</p>
							@else
								+ комиссия за доставку
						@endif
						<span id="delivModal"><svg width="10" height="10" viewBox="0 0 10 10" fill="none"
								xmlns="http://www.w3.org/2000/svg">
								<circle cx="5" cy="5" r="4.25" fill="white" stroke="#828282" stroke-width="0.5" />
								<path
									d="M4.61081 3.76H5.37881V8H4.61081V3.76ZM4.99481 2.944C4.84548 2.944 4.72015 2.896 4.61881 2.8C4.52281 2.704 4.47481 2.58667 4.47481 2.448C4.47481 2.30933 4.52281 2.192 4.61881 2.096C4.72015 1.99467 4.84548 1.944 4.99481 1.944C5.14415 1.944 5.26681 1.992 5.36281 2.088C5.46415 2.17867 5.51481 2.29333 5.51481 2.432C5.51481 2.576 5.46415 2.69867 5.36281 2.8C5.26681 2.896 5.14415 2.944 4.99481 2.944Z"
									fill="#828282" />
							</svg></span>
					</div>
				</div>
				<button class="button add-to-cart">Добавить</button>
			</div>
		</div>
		<div class="product-page__about-mobile-btn">
			<span id="mobileMoreInfo">Больше информации</span>
			<span class="product-page__mobile-arrow">
				<svg width="9" height="6" viewBox="0 0 9 6" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path fill-rule="evenodd" clip-rule="evenodd" d="M1.49927 3.00055L2.99963 4.49972L4.5 6L6.00037 4.49972L7.49963 3.00055L9 1.50027L7.49963 -1.56168e-06L6.00037 1.50027L4.5 3.00055L2.99963 1.50027L1.49927 -2.08625e-06L1.34705e-06 1.50027L1.49927 3.00055Z" fill="#82828280"/>
				</svg>
			</span>
		</div>
		<div class="product-page__about-inner">
			<div class="product-page__about-info">
				<div class="product-page__select">
					<span>
						Основное
						<img src="/images/redesign/arrow-down.svg" alt="arrow-down">
					</span>
					<ul>
						<div class="benefits">Полезная информация</div>
						<p>{{ $product->benefit }}</p>
						<div class="good_to_know">Полезно знать</div>
						<p>{{ $product->good_to_know }}</p>
						<div class="sold_separately">Продается отдельно</div>
						<p>{{ $product->sold_separately }}</p>
					</ul>
				</div>
				<div class="product-page__select">
					<span>
						Материал
						<img src="/images/redesign/arrow-down.svg" alt="arrow-down">
					</span>
					<ul>
						<div class="font-family-demibold">Описание и размеры товара</div>
						<p>{{ $product->cust_materials }}</p>
					</ul>
				</div>
				<div class="product-page__select">
					<span>
						Сборка
						<img src="/images/redesign/arrow-down.svg" alt="arrow-down">
					</span>
					<ul>
						@if($product->attachments_list)
							<div class="attachment">
								{{ $product->attachments_list[0]['name'] }}
							</div>
								@foreach($product->attachments_list[0]['atcharray'] as $atch)
								<a href="{{ $atch['attachmentPath'] }}" class="btn-link btn-red"
									target="_blank">Приложение № {{ $loop->index + 1 }}
								</a>
								<br>
							@endforeach
						@endif
					</ul>
				</div>
				<div class="product-page__select">
					<span>
						Упаковка
						<img src="/images/redesign/arrow-down.svg" alt="arrow-down">
					</span>
					<ul>
					<table class="table">
						<thead class="thead-dark">
							<tr>
								<th scope="col">Артикул</th>
								<th scope="col">К-во упаковок</th>
								<th scope="col">Ширина</th>
								<th scope="col">Высота</th>
								<th scope="col">Длина</th>
								<th scope="col">Вес</th>
							</tr>
						</thead>
						<tbody>
							@foreach($product->package_info ?: [] as $pkg)
						<tr>
							<th scope="row">{{$pkg['articleNumber']}}</th>
							<td>{{$pkg['quantity'] ?? 1}}</td>
							@foreach(['widthMet', 'heightMet', 'lengthMet', 'weightMet'] as $prop)
							@if(isset($pkg['pkgInfo'][$prop]))
							<td>{{$pkg['pkgInfo'][$prop]}}</td>
							@endif
							@endforeach
						</tr>
							@endforeach
						</tbody>
					</table>
					</ul>
				</div>
				<div class="product-page__select">
					<span>
						Размер
						<img src="/images/redesign/arrow-down.svg" alt="arrow-down">
					</span>
					<ul>
						@if($product->sizes_original)
						{!! $product->sizes_original !!}
						@else
						<p>Ширина: {{$product->width}}</p>
						<p>Высота: {{$product->length}}</p>
						<p>Глубина: {{$product->height}}</p>
						@endif
					</ul>
				</div>
			</div>
			<div class="product-page__about-desc">
				<span>{{ $product->description }}</span>
				<button id="showMoreText"><img src="/images/redesign/arrow-down.svg" alt="arrow-down"></button>
			</div>
		</div>
		@if($additionalProducts)
			<h3 class="additional-header">С этим товаром также заказывают</h3>

			<div class="additional-products">
				@foreach($additionalProducts ?? [] as $addProduct)
					@include('redesign.parts.product_card', ['item' => $addProduct])
				@endforeach
			</div>
		@endif

		{{-- чтобы товары добавлялись в корзину --}}
		<div>
			<cart type="widget"></cart>
		</div>

		<div style="display: none;" id="modalDelivInfo" class="modal modal-why">
			<div class="modal-content">
				<div class="modal-close jqmodal-close">
					<img src="/images/redesign/icons/modal-close.svg" alt="close">
				</div>
				<div style="text-align:left;" class="modal-title">В итоговую стоимость заказа <br> входит:</div>
				<p>Закупочная цена товаров в магазине IKEA</p>
				<p>Комиссия за доставку из магазина IKEA до склада в Минске</p>
				<p>Услуга подъема на этаж (необходимо согласовать заранее)</p>
				<p>Курьерские услуги за доставку со склада в Минске до места назначения</p>
				<a href="#" class="modal-more">Узнать больше</a>
			</div>
		</div>
	</div>
</section>
@endsection
