<?php
$settings = App\Models\Setting::where('key', 'contacts')->first();
$data = $settings ? json_decode($settings->value, true) : [];
$firstPhone = $data['phones'][0] ?? '';
$email = $data['emails'][0] ?? '';
$instagram = $data['social']['instagram']['link'] ?? 'https://www.instagram.com/scandimania.by';
/** @var \App\Models\DeliveranceDays $nearestDeliveryDateObject */
?>

@extends('redesign.basic')

@section('content')
		@include('redesign.parts.main_carousel')

		<section class="banner">
			<div class="header-alert alert-bottom">
				<div class="container">
					<h2>Ближайшая поставка: <span>{{$nearestDeliveryDayLocale}}</span></h2>
					<p>
						Доставки по Минску с
						{{$nearestDeliveryDateObject->minsk_date_from_formatted ?? ""}} по {{$nearestDeliveryDateObject->minsk_date_to_formatted ?? ''}},
						по РБ с {{$nearestDeliveryDateObject->country_date_from_formatted ?? ''}} по {{$nearestDeliveryDateObject->country_date_to_formatted ?? ""}}
					</p>
				</div>
			</div>
		</section>

		<div id="orderModal">
			<order-request />
		</div>
		<div id="blocker" class="blocker"></div>

		<section class="count-block" style="background-image: url('images/redesign/count-bg.jpg');">
			<div class="container">
				<h2>Стоимость доставки</h2>
				<h3>Выберите валюту</h3>
				<div class="delivery-pricing-currency-switch">
					<currency-switch view="tabs"></currency-switch>
				</div>
				<div class="count-block__sum">При сумме заказа<span>*</span></div>
				<?php
				$chargesChunks = App\Models\Charge::all();
				/** @var App\Models\Charge $charge */
			?>
			<div class="delivery-pricing-row">
				@foreach($chargesChunks as $charge)
						<div class="delivery-charge">
							<div class="amount">
								@if($charge->total_from)
									<span class="from">
										<span class="do">от</span>
										<span class="price-amount RUB">{{$charge->total_from}} <span class="minuss">-</span></span>
										<span class="price-amount BYN">{{rubInByn($charge->total_from, true)}} <span class="minuss">-</span></span>
									</span>
								@endif
								@if($charge->total_to)
									<span class="to">до</span>
									<span class="price-amount RUB">{{ $charge->total_to }}</span>
										<span class="price-amount BYN">{{ rubInByn($charge->total_to, true) }}</span>
								@endif
							</div>
							<div class="charge">
								@if($charge->type == 'fixed')
								<span class="price-amount RUB"> <span class="dots">...</span> <span class="change-value">{{ $charge->amount }}</span> <span class="price">RUB</span></span>
									<span class="price-amount BYN"><span class="dots">...</span><span class="change-value">{{ rubInByn($charge->amount, true) }}</span> <span class="price">BYN</span></span>
								@else
									<span class="dots">...</span>
									{{ $charge->amount }}<span class="percent">% <span class="otsumm">От суммы <br> заказа</span></span>
								@endif
							</div>
						</div>
				@endforeach
				<div class="count-block__alert">
					<span>*</span>Тарифы действуют только при заказе через каталог нашего сайта. <br>
 					Условия распространяются на все товары кроме диванов, хрупких товаров и товаров по фиксированной цене.
				</div>
			</div>
			</div>
		</section>

		<section class="how-order">
			<div class="container">
				<h2 class="how-order__title">Как сделать заказ?</h2>
				<div class="how-order__wrap">
					<div class="how-order__inner">
						<div class="how-order__col">
							<h4 class="first-h4">Первый способ <span>Стоимость доставки <br> меньше</span></h4>
							<ul>
								<li><span>1.</span>Выберите товары в каталоге или поиске</li>
								<li><span>2.</span>Перейдите в корзину и оформите заказ
								<p class="first-p">Цены товаров соответсвуют <br>
									ценам на сайте <a href="https://ikea.ru/" target="_blank">ikea.ru</a></p></li>
							</ul>
						</div>
						<div class="how-order__img">
							<img src="images/redesign/icons/woman.svg" alt="order">
						</div>
					</div>
					<div class="how-order__inner how-order__second">
						<div class="how-order__img">
							<img src="images/redesign/icons/woman2.svg" alt="order">
						</div>
						<div class="how-order__col">
							<h4>Второй способ <span class="how-order__sc">Удобно, если у вас готов
								список товаров</span></h4>
							<ul>
								<li><span>1.</span>Вставьте список покупок
									в виде файла или текста <a href="#" id="openOrderModal">сюда</a></li>
								<li><span>2.</span>Следуйте дальнейшим
									инструкциям <p class="order__list">Или пришлите список товаров
										на почту <a href="mailto:{{ $email }}">{{ $email }}</a></p></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</section>

		<div class="items-with-cart-container ikea-family">
			<section class="discount">
				<div class="container">
					<h2 class="discount-title">Скидки <span>IKEA Family</span></h2>
						<div class="discount-wrap">
							@foreach($ikeaFamily as $product)
								@if($product)
									@include('redesign.parts.product_card', ['item' => $product])
								@endif
							@endforeach
						</div>
					</div>
					<div class="ikea-show-more">
						<a href="/search?search=ikea_family&full=true">Показать ещё</a>
					</div>
			</section>
			<div class="right-part">
				<div class="attach-sticky-here">
					<div class="cart">
						<cart type="widget"></cart>
					</div>
				</div>
			</div>
		</div>

		<section class="popular-products">
			<div class="container">
					@include('redesign.items-with-cart', ['title' => 'Популярные товары', 'hideCart' => true])
			</div>
		</section>

@endsection
