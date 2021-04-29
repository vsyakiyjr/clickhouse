<?php
/** @var \App\Api\Structures\Cart $cart */
/** @var \App\Models\Order $order */

?>

<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport"
		  content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<meta http-equiv="X-UA-Compatible"
		  content="ie=edge">
	<title>Document</title>
	<style>

		@import url('https://fonts.googleapis.com/css?family=Montserrat:400,400i,700&display=swap&subset=cyrillic');

		.header {
			text-align: center;
			margin-bottom: 50px;
		}

		.order-info {
			padding: 30px 0;
		}

		h1 {
			text-align: center;
			font-size: 24px;
			font-weight: bold;
			margin: 0 0 8px 0;
		}

		.products {
			border-bottom: 1px dashed #333;
			border-top: 1px dashed #333;
			margin: 30px 0 20px;
			padding: 20px 0;
		}

		table {
			width: 100%;
			border-collapse: collapse;
		}

		th,td {
			padding: 5px;
		}

		td {
			font-size: 10px;
		}

		td:not(:first-child),
		th:not(:first-child) {
			text-align: center;
		}

		td:first-child,
		th:first-child {
			text-align: left;
		}

		.text-uppercase {
			text-transform: uppercase;
		}

		.text-right {
			text-align: right;
		}

		.text-center {
			text-align: center;
		}

		.greeting {
			font-size: 14px;
			text-align: center;
		}

		.total-price {
			font-size: 18px;
			font-weight: bold;
		}

		.delivery {
			padding: 20px 0;
			border-bottom: 1px dashed #333;
			margin-bottom: 12px;
		}

		body {
			padding: 60px;
			color: #333;
			font-size: 12px;
			max-width: 666px;
			font-family: 'Montserrat', sans-serif;
		}

		.footer{
			font-size: 14px;
			font-style: italic;
			text-align: center;
			margin: 50px 0 40px;
		}

		.contacts-link {
			font-size: 14px;
			color: #000;
			border: 1px solid #000;
			padding: 13px 53px;
			text-transform: uppercase;
		}

		@media print {
			tr,td,th {
				page-break-inside: avoid;
			}
		}

		@media all and (max-width: 767px) {
			body {
				padding: 10px;
				margin: 0;
			}

		}
		.previous-price {
			text-decoration: line-through;
		}
	</style>
</head>
<body>

<div class="order-info">

	<div class="header">
		<img style="width: 106px"
			 src="{{$message->embed(public_path('images/logo.png'))}}"
			 alt="Ikeamania.by logo"
		/>
	</div>

	<h1>Заказ размещён</h1>

	<div class="greeting">
		Привет{{',' . ($order->name ? ' ' . $order->name  : '')}}! Спасибо за Ваш заказ!
	</div>

	<div class="products">
		<table>
			<thead>
				<tr>
					<th>
						Название
					</th>
					<th>
						Артикул
					</th>
					<th>
						Цена, RUB
					</th>
					<th>
						Кол-во
					</th>
					<th>
						Итого, RUB
					</th>
					<th style="width: 75px;">
						Итого, BYN
					</th>
				</tr>
			</thead>
			<tbody>

				@foreach($order->cart->items as $cartItem)
					<tr>
						<td>
							<span class="text-uppercase">{{ $cartItem->model->name }}</span><br>
							{{ $cartItem->model->type }}<br>
							{{implode(', ', json_decode($cartItem->model->color, true) ?? [])}}
						</td>
						<td>
							{{ $cartItem->vendor_code }}
						</td>
						<td class="text-right">
							{{ $cartItem->price_order }}
						</td>
						<td>
							{{ $cartItem->qty }}
						</td>
						<td>
							{{ $cartItem->price_order_with_qty }}
						</td>
						<td>
							{{ $cartItem->price_order_byn_with_qty }}
						</td>
					</tr>
				@endforeach
			</tbody>
			<tfoot>
				<tr>
					<th colspan="5"></th>
					<th style="white-space: nowrap">
						{{ number_format($order->cart->totalPriceByn, 2, '.', ' ') }}
					</th>
				</tr>
			</tfoot>
		</table>
	</div>

	<div class="delivery">
		Адрес:
		<strong>
			{{ $order->delivery_address }}
			дом {{ $order->delivery_house }},
			подъезд {{ $order->delivery_stairs }}
			@if($order->delivery_apartment)
				, кв. {{ $order->delivery_apartment }}
			@endif
			@if($order->delivery_floor)
				, этаж {{ $order->delivery_floor }}
			@endif
		</strong>
		<br><br>
		Курьерские услуги: <strong>{{ $order->delivery_cost }} BYN</strong>
		@if($order->delivery_assemble)
			<br><strong>Услуга сборки</strong>
		@endif
		@if($order->delivery_lifting)
			<br><strong>Услуга подъёма на этаж</strong>
		@endif
	</div>

	<div class="total">
		К оплате: <span class="total-price">{{ number_format($order->total_with_promo, 2, '.', ' ') }} BYN</span>
		@if($order->promo_discount)
			<br>
			Применён промокод {{ $order->promo_code }}, скидка {{ $order->promo_discount }} на услуги доставки.
			<br>
			Цена до скидки: <span class="previous-price">{{ $order->price_byn['total'] }} BYN</span>
		@endif
	</div>

	<div class="footer">
		Заказ принят и будет направлен на сборку в магазин IKEA. <br>
		{{--Дата поставки {{ $nearestDeliveryDayCarbon->format('d.m') }}. С {{ $nearestDeliveryDayCarbon->format('d.m') }} по {{ $clientDeliveryDayTo->format('d.m') }} наши операторы свяжутся с Вами <br>
		и согласуют день и время доставки.--}}
	</div>

	<div class="text-center">
		<a href="https://ikeamania.by/contacts"
		   target="_blank"
		   class="contacts-link">
			Нужна помощь?
		</a>
	</div>

</div>
</body>
</html>
