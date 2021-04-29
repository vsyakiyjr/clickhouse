<?php

namespace App\Http\Controllers;

use App\Helpers\Calendar;
use App\Mail\CallbackRequestMail;
use App\Mail\ManagerOrderCreated;
use App\Mail\OrderCreated;
use App\Mail\OrderRequestMail;
use App\Models\CartModel;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\PromoCode;
use App\Models\Setting;
use App\Services\ExchangeService;
use App\Api\Structures\Cart;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Mail;
use Validator;

class OrdersController {


	/**
	 * Страница корзины - оформление заказа
	 *
	 * @param Request $request
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function order(Request $request) {

		return view('redesign.pages.checkout');
	}

	/**
	 * Create order
	 *
	 * @param Request $request
	 *
	 * @return array
	 * @throws \Exception
	 */
	public function create(Request $request) {
		$validator = Validator::make($request->all(), [
			'email'            => 'required|email',
			'phone'            => 'required',
			'cart'             => 'required',
//			'delivery_type'    => 'required',
//			'delivery_address' => 'required_if:delivery_type,1',
//			'delivery_house'   => 'required_if:delivery_type,1',
		]);

		if ($validator->fails()) {
			return [
				'success' => false,
				'errors'  => $validator->errors(),
			];
		}

		$cartFromRequest = $request->get('cart');
		$cartModel = getCartModel();
		$cart = $cartModel->cart;

		if(empty($cart->items)){
			$cart = new Cart($cartFromRequest);
		}

		$product = [];

		$totalPriceByn = $cart->totalPriceByn;
		$city = $request->get('city');
		$cities = $this->getCities();

		$deliveryCost = 0;
		foreach ($cities as $deliveryCity) {
			if ($deliveryCity['name'] !== $city) {
				continue;
			}

			if($deliveryCity['freeShippingFrom']){
				$deliveryCost =  $totalPriceByn < $deliveryCity['freeShippingFrom'] ? $deliveryCity['price'] : 0;
			} else {
				$deliveryCost = $deliveryCity['price'];
			}
		}

		$isOnlyFixed = empty($cart->itemsIdsWithCommonPrice);

		$userId = Auth::id() || 0;

		$date = new \DateTime('now');
		$num = $date->format('YmdHis');

		$priceBYN = [
			'delivery_cost'          => $deliveryCost,
			'total'                  => $totalPriceByn + $deliveryCost,
			'total_without_delivery' => $totalPriceByn,
		];

		$commission = $cart->charge->amount;

		$promoCode = $request->get('promo_code');

		$promoCodeModel = PromoCode::getByCode($promoCode);

		if($promoCodeModel){
			$promoCodeModel->usage_count++;
			$promoCodeModel->save();
			$promoDiscount = PromoCode::getDiscountIfActive($promoCode);
		} else {
			$promoDiscount = null;
		}

		$data = [
			'order_num'          => $num,
			'user_id'            => $userId,
			'email'              => $request->email,
			'phone'              => $request->phone,
			'name'               => $request->name ? $request->name : '',
			'delivery_date'      => Calendar::nextDelivery(false),
			'delivery_city'      => $city,
			'delivery_type'      => 1,
			'delivery_address'   => $request->delivery_address,
			'delivery_house'     => $request->delivery_house,
			'delivery_stairs'    => $request->delivery_stairs,
			'delivery_apartment' => $request->delivery_apartment,
			'delivery_floor'     => $request->delivery_floor,
			'delivery_assemble'  => !!$request->delivery_assemble,
			'delivery_lifting'   => !!$request->delivery_lifting,
			'delivery_cost'      => $deliveryCost,
			'total'              => $cart->totalPriceRub,
			'vendor_code'        => json_encode($product),
			'exchange'           => (new ExchangeService())->getRate(),
			'without_commission' => $cart->totalRawPriceRub,
			'floor'              => 0,
			'comment'            => $request->get('comment'),
			'commission'         => $commission,
			'price_byn'          => $priceBYN,
			'fix_commission'     => ($isOnlyFixed ? $cart->totalChargeRub : 0) ?? 0,
			'cart_details'       => $cart->toArray(),
			'promo_code'		 => $promoCode,
			'promo_discount'	 => $promoDiscount,
		];

		/** @var Order $order */
		$order = Order::create($data);

		foreach ($cart->items as $item){
			try {
				// for backward compatibility on admin orders create order details
				// options: {"fix_price": 0, "price_BYN": 197.97, "price_byn": 227.66, "commission": 15, "vendor_code": "60368415", "delivery_cost": 899.85}
				/** @var OrderDetail $detail */
				$detail = $order->details()->create([
					'price'       => $item->price,
					'qty'         => $item->qty,
					'product_id'  => $item->product_id,
					'vendor_code' => $item->vendor_code,
					'options' => [
						'fix_price'     => $item->fixed_price ? $item->price_order : 0,
						'price_BYN'     => $item->price_byn,
						'price_byn'     => $item->price_order_byn,
						'vendor_code'   => $item->vendor_code,
						'delivery_cost' => $item->commission_amount_rub,
					]
				]);

				$detail->model = $item['model'];
				$detail->save();

				$product = Product::find($item->product_id);
				$product->priority += 10;
				$product->save();
			} catch (\Throwable $e){
				// suppress integer overflow
			}
		}

		$mailable = new ManagerOrderCreated($order);

		if (env('APP_ENV') !== 'local') {
			Mail::to(env('SUPPORT_EMAIL'))->send($mailable);
			Mail::to($order->email)->send(new OrderCreated($order));
		}

		$cartModel->cart = new Cart();
		$cartModel->save();

//		$order->deleted_at = Carbon::now();
        $order->save();

		return [
			'success' => true,
			'total' => $order->total_with_promo,
		];
	}

	/**
	 * Preview email that will be sent for the order
	 *
	 * @param Request $request
	 *
	 * @return ManagerOrderCreated
	 */
	public function renderEmail(Request $request){
		$orderId = $request->get('id');
		$order = Order::find($orderId);

//		$mailable = new OrderCreated($order);
		$mailable = new ManagerOrderCreated($order);

//		Mail::to('ikeamaniaby@gmail.com')->send(new ManagerOrderCreated($order, $productsList));
//		Mail::to($order->email)->send(new OrderCreated($order, $productsList));

		return $mailable;
	}

	/** **  AJAX METHODS  ** **/

	public function getCities() {
		$cities = Setting::where('key', 'cities')->first();

		if ($cities) {
			return json_decode($cities->value, true);
		}

		return [];
	}

	/**
	 * Get actual cart content
	 *
	 * @return Cart
	 */
	public function getCartContent(Request $request) {
		$cart = getCartModel()->cart;

		$cart->nearestDeliveryDayLocale = $cart->nearestDeliveryDayLocale ?? $nearestDeliveryDay = Calendar::nextDelivery();

		return $cart;
	}

	/**
	 * Adding product to cart
	 *
	 * @param Request $request
	 *
	 * @return array
	 */
	public function addToCard(Request $request) {
		/** @var Product $product */
		$product = Product::find($request->get('product_id'));
		$qty = $request->get('quantity', 1);

		$cartModel = getCartModel();
		$cart = $cartModel->cart;

		if(!$product){
			return [
				'success' => false,
				'count'   => count($cart->items),
			];
		}

		/** @var Cart $cart */
		$cart->addItem($product, $qty);

		$cartModel->cart = $cart;
		$cartModel->save();

		return [
			'success' => true,
			'count'   => count($cart->items),
		];
	}

	/**
	 * Update product at cart
	 */
	public function changeQty(Request $request) {
		$productId = $request->get('id');
		$qty = $request->get('quantity');

		$cartModel = getCartModel();
		$cart = $cartModel->cart;

		$cart->changeQty($productId, $qty);

		$cartModel->cart = $cart;
		$cartModel->save();

		return [
			'success' => true,
			'count'   => count($cart->items),
		];
	}

	/**
	* Remove product from cart
	*/
	public function removeItemFromCart(Request $request) {
		$productId = $request->get('id');
		$cartModel = getCartModel();
		$cart = $cartModel->cart;

		$cart->removeItem($productId);

		$cartModel->cart = $cart;
		$cartModel->save();

		return [
			'success' => true,
			'count'   => count($cart->items),
		];
	}

	/**
	 * Clear cart
	 *
	 * @param Request $request
	 *
	 * @return array
	 */
	public function clearCart() {
		$cartModel = getCartModel();
		$cartModel->cart = new Cart();
		$cartModel->save();

		return [
			'success' => true,
			'count'   => 0,
		];
	}

	/**
	 * Callback request
	 *
	 * @param Request $request
	 *
	 * @return array
	 */
	public function callbackRequest(Request $request){
		$phone = $request->get('phone');

        $supportEmail = env('SUPPORT_EMAIL');
		if($supportEmail){
			Mail::to($supportEmail)->send(new CallbackRequestMail($phone));
		}

		return ['success' => true];
	}

	/**
	 * Отправить заявку на создание заказа на емеил менеджера
	 *
	 * @param Request $request
	 *
	 * @return array
	 */
	public function sendOrderRequest(Request $request){
		$name    = $request->get('name');
		$phone   = $request->get('phone');
		$email   = $request->get('email');
		$address = $request->get('address');
		$list = $request->get('list');

		$file = $request->file('file');

        $supportEmail = env('SUPPORT_EMAIL');

		if($supportEmail){
			Mail::to($supportEmail)->send(new OrderRequestMail($name, $phone, $email, $address, $list, $file));
		}

		return ['success' => 'true'];
	}

	public function getDiscount(Request $request){
		$discount = PromoCode::getDiscountIfActive($request->get('promo_code'));

		$cart = getCartModel()->cart;
		$commissionRub = $cart->totalPriceRub - $cart->totalRawPriceRub;
		$commissionWithDiscount = PromoCode::calculatePriceWithPromo($commissionRub, $discount);

		return [
			'discount' 	=> $discount,
			'new_total'	=> ceil(rubInByn($cart->totalRawPriceRub + $commissionWithDiscount))
		];
	}
}
