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
		table {
			width: 100%;
			border: 1px solid #333;
			border-collapse: collapse;
		}

		th,td {
			border: 1px solid #333;
			padding: 5px;
		}

		.text-right {
			text-align: right;
		}

		.text-center {
			text-align: center;
		}

		.text-nowrap {
			white-space: nowrap;
		}

		body {
			color: #333;
			width: 666px;
			font-family: Roboto, 'sans-serif';
		}

		@media print {
			tr,td,th {
				page-break-inside: avoid;
			}
		}
		.previous-price {
			text-decoration: line-through;
		}
	</style>
</head>
<body>
<link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap" rel="stylesheet">

<div class="order-info">
	<h2 class="main-title">Поступил заказ №{{$order->id}}!
		<span style="font-size: 0.9em">{{date('d.m.Y H:i:s')}}</span>
	</h2>

	<h3>
		Закупочная цена: {{number_format($order->without_commission, 2)}} RUB
	</h3>

	<h3>
		Комиссия:
		@if($order->fix_commission)
			{{$order->fix_commission}} RUB
		@else
			{{$order->commission}} %
		@endif
	</h3>

	<h3>
		К оплате: {{ $order->total_with_promo }} BYN
		@if($order->promo_discount)
			<br>
			Применён промокод {{ $order->promo_code }}, скидка {{ $order->promo_discount }}
			<br>
			Цена до скидки: <span class="previous-price">{{ $order->price_byn['total'] }} BYN</span>
		@endif
	</h3>

	@if($order->comment)
		<h3>
			Комментарий: {{$order->comment}}
		</h3>
	@endif

	<h3>Услуга сборки: {{ $order->delivery_assemble ? 'Да' : 'Нет'}}</h3>
	<h3>Услуга подъёма на этаж: {{ $order->delivery_lifting ? 'Да' : 'Нет'}}</h3>

	<div class="products">
		<h3 class="title">Товары:</h3>
		<table>
			<thead>
				<tr>
					<th>
						#
					</th>
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
						Количество
					</th>
					<th>
						Комиссия
					</th>
					<th>
						Итого, RUB
					</th>
					<th>
						Итого, BYN
					</th>
				</tr>
			</thead>
			<tbody>
				@foreach($order->cart->items as $cartItem)
					<tr>
						<td>{{$loop->index + 1}}</td>
						<td>
							<strong>{{ $cartItem->model->name }}</strong><br>
							{{ $cartItem->model->type }}<br>
							{{implode(', ', json_decode($cartItem->model->color, true) ?? [])}}
						</td>
						<td class="text-center">
							{{ $cartItem->vendor_code }}
						</td>
						<td class="text-right">
							{{ $cartItem->price }}
						</td>
						<td class="text-center">
							{{ $cartItem->qty }}
						</td>
						<td class="text-right">
							{{ $cartItem->fixed_price ? $cartItem->price_order_byn . ' BYN' : ($order->cart->charge->amount . '%') }}
						</td>
						<td class="text-right">
							{{ $cartItem->price_order_with_qty }}
						</td>
						<td class="text-right">
							{{ $cartItem->price_order_byn_with_qty }}
						</td>
					</tr>
				@endforeach
			</tbody>
			<tfoot>
				<tr>
					<th colspan="3">Итого:</th>
					<th style="white-space: nowrap">
						{{ number_format($order->without_commission, 2, '.', ' ') }}
					</th>
					<th class="text-nowrap">

					</th>
					<th></th>
					<th class="text-right text-nowrap">
						{{ $order->cart->totalPriceRub }}
					</th>
					<th class="text-right text-nowrap">
						<u>{{ $order->cart->totalPriceByn }}</u>
					</th>
				</tr>
			</tfoot>
		</table>
	</div>

	<div class="delivery">
		<h3 class="title">Доставка:</h3>
		<table>
			<tr>
				<th>
					Дата
				</th>
				<td>
					{{ $order->delivery_date }}
				</td>
			</tr>
			<tr>
				<th>
					Адрес
				</th>
				<td>
					город {{ $order->delivery_city }}, {{$order->delivery_address}}, д. {{$order->delivery_house}}
					@if($order->delivery_stairs)
						, подъезд {{$order->delivery_stairs}}
					@endif
					@if($order->delivery_apartment)
						, квартира {{$order->delivery_apartment}}
					@endif
				</td>
			</tr>
			<tr>
				<th>
					Стоимость
				</th>
				<td>
					{{ $order->delivery_cost }}
				</td>
			</tr>
			<tr>
				<td colspan="2">
					К оплате с доставкой: {{ $order->total_with_promo }}
					@if($order->promo_discount)
						<br>
						Применён промокод {{ $order->promo_code }}, скидка {{ $order->promo_discount }}
						<br>
						Цена до скидки: <span class="previous-price">{{ $order->price_byn['total'] }} BYN</span>
					@endif
				</td>
			</tr>
		</table>
	</div>

	<div class="title contacts">
		<h3 class="contacts-title">Контакты:</h3>
		<table>
			<tr>
				<th>Телефон:</th>
				<td><a href="tel:{{$order->phone}}">{{$order->phone}}</a></td>
			</tr>
			<tr>
				<th>Email:</th>
				<td><a href="mailto:{{$order->email}}">{{$order->email}}</a></td>
			</tr>
			<tr>
				<th>Имя:</th>
				<td>{{$order->name}}</td>
			</tr>
		</table>
	</div>
</div>
</body>
</html>
