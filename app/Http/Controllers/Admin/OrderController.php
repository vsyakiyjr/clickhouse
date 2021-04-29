<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Charge;
use App\Models\OrderDetail;
use App\Models\Order;
use App\Models\Product;
use App\Services\ExchangeService;
use Cart;
use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;
use Validator;

class OrderController extends Controller {
	/**
	 * @var ExchangeService
	 */
	private $exchange;

	private const ReportByClients = 'byClients';
	private const ReportByProducts = 'byProducts';
	private const ReportFull = 'all';

	public function __construct(ExchangeService $exchange) {

		$this->exchange = $exchange;
	}

	public function email($date) {
		$data = array_merge(
			$this->collectOrdersData($date, self::ReportFull),
			$this->getTotalInfo($date)
		);

		Mail::to(Config::get('mail.from.address'))->send(new \App\Mail\Orders($data));
	}

	/**
	 * @param string $date
	 * @param string $reportType
	 * @param string $searchStr
	 * @return array
	 */
	private function collectOrdersData($date, $reportType, $searchStr = ''): array{
		$ordersByVendorCodes = [];
		$ordersByClients = [];

		$orders = Order::with('details')->where('delivery_date', $date);

		if ($searchStr) {
			$orders->where(static function($q) use($searchStr){
				$searchLike = "%{$searchStr}%";
				$q->where('email', 'LIKE', $searchLike)
					->orWhere('name', 'LIKE', $searchLike)
					->orWhere('phone', 'LIKE', $searchLike)
					->orWhere('delivery_address', 'LIKE', $searchLike)
					->orWhere('order_num', '=', $searchStr)
					->orWhere('id', '=', $searchStr);
			});
		}

		$orders = $orders->orderBy('id', 'desc')->get();

		foreach ($orders as $order) {
			// TODO: in future use cart_details property for all orders
			// TODO: try get order details or fallback to carts for old orders

			if ($order->details->isEmpty()) {
				$order->details = $this->buildProductsListFallback($order); // for frontend only
			}

			if ($reportType !== self::ReportByClients) {
				foreach ($order->details as $product) {
					if (isset($ordersByVendorCodes[$product['vendor_code']])) {
						$ordersByVendorCodes[$product['vendor_code']]['count'] += $product['qty'];
					} else {
						$ordersByVendorCodes[$product['vendor_code']] = [
							'product' => $product['model'],
							'count'   => $product['qty'],
						];
					}
				}
			}

			if ($reportType !== self::ReportByProducts) {
				$ordersByClients[$order->phone][] = $order; // will be flatten later, to keep original sorting order
			}
		}

		$ordersByVendorCodes = array_filter(array_values($ordersByVendorCodes), static function($a){
			return (bool)$a['product'];
		});

		$ordersByClients = Arr::flatten($ordersByClients, 1);

		return [
			'vendor_codes'  => $ordersByVendorCodes,
			'clients'       => $ordersByClients,
		];
	}

	/**
	 * @param string $date
	 * @return array
	 */
	public function getTotalInfo(string $date): array{
		return [
			'product_total' => $this->getProductsTotalSum($date),
			'clients_count' => $this->getClientsCount($date),
			'cities'        => $this->getCities($date),
		];
	}

	/**
	 * @param string $date
	 * @return float
	 */
	private function getProductsTotalSum(string $date): float{
		return Order::where('delivery_date', $date)->sum('without_commission');
	}

	/**
	 * @param string $date
	 * @return int
	 */
	private function getClientsCount(string $date): int{
		$clients = Order
			::select(['user_id'])
			->where('delivery_date', $date)
			->where('user_id', '>=', '0')
			->get();

		if ($clients->isEmpty()) {
			return 0;
		}

		$newClients = [];
		$registeredClients = [];
		foreach ($clients as $client) {
			if ($client->user_id === '0') {
				$newClients[] = $client->user_id;
			} else {
				$registeredClients[$client->user_id] = $client->user_id;
			}
		}

		return count($newClients) + count($registeredClients);
	}

	/**
	 * @param string $date
	 * @return array
	 */
	private function getCities(string $date): array{
		return Order
			::select(['delivery_city'])
			->where('delivery_date', $date)
			->distinct()
			->pluck('delivery_city')
			->toArray();
	}

	/**
	 * Fallback for old orders with empty "details" property
	 *
	 * @param Order $order
	 * @return array
	 */
	private function buildProductsListFallback(Order $order): array{
		$productsList = [];

		Cart::instance('o' . $order->id)->restore('o' . $order->id);

		$cartContent = Cart::content();
		$exchange = $this->exchange;

		$cartContent = $cartContent->isEmpty() ? array_map(function($codeAndQty) use($exchange, $order){
			// [{"S69275662":"1"},{"30384860":"1"}]
			$code = array_keys($codeAndQty)[0];
			$qty = $codeAndQty[$code];

			/* @var Product $product*/
			$product = Product::where(['vendor_code' => $code])->first();
			if (!$product) {
				return null;
			}

			$charge = Charge::getForPrice((float)Cart::total() + $product->current_price * $qty);
			$product_price = $this->getFullProductCost($product, $charge);

			return (object) [
				'model'   => $product,
				'qty'     => $qty,
				'options' => (object) [
					'vendor_code'   => $product->vendor_code,
					'fix_price'     => $product->discount == 0 ? 0 : ceil($product->fixed_price),
					'price_BYN'     => $product->discount ? ceil($exchange->convert($product->price_order)) : round($exchange->convert($product->price_order), 2),
					'delivery_cost' => ceil($product_price - $product->current_price),
					'commission'    => $order->commission,
				],
			];
		}, json_decode($order->vendor_code, true)) : $cartContent;

		foreach ($cartContent as $product) {
			if (!$product) {
				continue;
			}

			$item = [
				'price'       => $product->model->price,
				'qty'         => $product->qty,
				'vendor_code' => $product->model->vendor_code,
				'options'     => $product->options,
				'model'       => $product->model,
			];

			$productsList[] = $item;
		}

		return $productsList;
	}

	private function getFullProductCost(Product $product, Charge $charge) {
		if ($product->discount != 0) {
			return $product->current_price + $product->discount;
		}

		return $charge->type === 'fixed' ? $product->current_price : ceil($product->current_price + $product->current_price * $charge->amount / 100);
	}

	/**
	 * @param Request $request
	 * @param string $date
	 * @return array
	 */
	public function index(Request $request, $date): array{
		$reportType = $request->get('type');
		$data = $this->collectOrdersData($date, $reportType, $request->get('search'));

		if ($reportType === self::ReportByProducts) {
			$reportItems = $data['vendor_codes'];
		} elseif ($reportType === self::ReportByClients) {
			$reportItems = $data['clients'];
		} else {
			$reportItems = [];
		}

		$pagination = $request->get('pagination');

		$pagination['totalItems'] = count($reportItems);
		$pagination['totalPages'] = ceil($pagination['totalItems'] / $pagination['itemsPerPage']);

		$reportPage = array_chunk($reportItems, $pagination['itemsPerPage'])[$pagination['page'] - 1] ?? [];

		return [
			'success'       => true,
			'orders'		=> $reportPage,
			'pagination'	=> $pagination,
		];
	}

	public function updateProduct(Request $request) {

		$validator = Validator::make($request->all(), [
			'order_id'   => 'required',
			'product_id' => 'required',
			'count'      => 'required',
		]);

		if ($validator->fails()) {
			return [
				'success' => false,
				'errors'  => $validator->errors(),
			];
		}
		$fix = $request->has('fix') ? $request->get('fix') : 0;
		$product_id = $request->get('product_id');
		$product_code = $request->get('product_code');
		$order_id = $request->get('order_id');
		$count = $request->get('count');

		$this->updateOneProduct($order_id, $product_id, $count, $fix, $product_code);

		return ['success' => true];
	}

	/**
	 * @param     $order_id
	 * @param     $productVendorCode
	 * @param     $count
	 * @param     $fix
	 * @param     $product_code
	 * @param int $exchangeValue
	 * @param int $commission
	 */
	private function updateOneProduct($order_id, $productVendorCode, $count, $fix, $exchangeValue = 0, $commission = 0) {
		$exchange = $this->exchange;

		$order = Order::find($order_id);
		if ($order->details()->count() > 0) {
			// for new orders
			/** @var OrderDetail $detail */
			$detail = $order->details()->where('vendor_code', '=', $productVendorCode)->first();
			$detail->qty = $count;
			$options = $detail->options;

			if($fix != 0){
				$detail->price = $exchange->reconvert($fix);
				if ($exchangeValue) {
					$detail->price = $fix / $exchangeValue;
				}

				$options['fix_price'] = $fix;
			} elseif ($commission != 0) {
				$detail->price = $detail->model->current_price + $detail->model->current_price * ($commission / 100);
				$options['commission'] = $commission;
				$options['fix_price'] = $fix;
			}
			if ($exchangeValue) {
				$options['price_BYN'] = round($exchange->convert($detail->price, $exchangeValue), 2);
			}
			$options['delivery_cost'] = ceil($detail->price - $detail->model['current_price']);

			$detail->options = $options;
			$detail->save();

			$totalPriceInRub = 0;
			$total_without_commision = 0;

			foreach ($order->details as $detail){
				$totalPriceInRub += $detail->price * $detail->qty;
				$total_without_commision += $detail->model['price'] * $detail->qty;
			}

			$total = $totalPriceInRub * $order->exchange;
		} else {
			Cart::instance('o' . $order_id)->restore('o' . $order_id);

			foreach (Cart::content() as $product) {
				if ($product->model->vendor_code === $productVendorCode) {
					$product->qty = $count;
					if ($fix != 0) {
						$product->price = $exchange->reconvert($fix);

						if ($exchangeValue) {
							$product->price = $fix / $exchangeValue;
						}

						$product->options['fix_price'] = $fix;
					} elseif ($commission != 0) {
						$product->price = $product->model->current_price + $product->model->current_price * ($commission / 100);
						$product->options['commission'] = $commission;
						$product->options['fix_price'] = $fix;
					}
					if ($exchangeValue) {
						$product->options['price_BYN'] = round($exchange->convert($product->price, $exchangeValue), 2);
					}
					$product->options['delivery_cost'] = ceil($product->price - $product->model->current_price);
				}
			}
			// $this->adjustItemPricesDueToCharges();
			$total_without_commision = $this->calculateTotalWithoutCommission();
			$total = Cart::total();
			Cart::store('o' . $order_id);
		}

		$products = json_decode($order->vendor_code, true);
		$counter = 0;

		foreach ($products as $product) {

			if (array_key_exists($productVendorCode, $product)) {
				$products[$counter][$productVendorCode] = $count;
			}
			$counter++;
		}

		$order->vendor_code = json_encode($products);
		$order->total = $total + $order->delivery_cost;
		$order->without_commission = $total_without_commision;
		$order->save();
	}

	private function calculateTotalWithoutCommission() {
		$price = 0;

		foreach (Cart::content() as $product) {
			$price += $product->model->current_price * $product->qty;
		}

		return $price;
	}

	public function deleteProduct(Request $request) {
		$validator = Validator::make($request->all(), [
			'order_id'   => 'required',
			'product_id' => 'required',
		]);

		if ($validator->fails()) {
			return [
				'success' => false,
				'errors'  => $validator->errors(),
			];
		}

		$product_id = $request->get('product_id');
		$product_code = $request->get('product_code');
		$order_id = $request->get('order_id');
		Cart::instance('o' . $order_id)->restore('o' . $order_id);

		foreach (Cart::content() as $product) {
			if ($product->model->id === $product_id) {
				Cart::remove($product->rowId);
			}
		}
		// $this->adjustItemPricesDueToCharges();
		$total = Cart::total();

		Cart::store('o' . $order_id);

		$order = Order::query()->findOrNew($request->get('order_id'));
		$products = json_decode($order->vendor_code, true);
		$counter = 0;

		foreach ($products as $product) {

			if (array_key_exists($product_code, $product)) {
				unset($products[$counter]);
			}
			$counter++;
		}
		$order->vendor_code = json_encode($products);
		$order->total = $total;
		$order->save();

		return ['success' => true];
	}

	public function update(Request $request) {
		$validator = Validator::make($request->all(), [
			'id'         => 'required',
			'delivery_cost'    => 'required|numeric',
			'delivery_address' => 'required',
			'delivery_city'    => 'required',
			'email'            => 'email',
			'phone'            => 'required',
		]);

		if ($validator->fails()) {
			return [
				'success' => false,
				'errors'  => $validator->errors(),
			];
		}
		$products = $request->get('products');
		$fixedCommission = $request->get('fix_commission');
		$order_id = $request->get('id');
		$order = Order::find($order_id);
		foreach ($products as $product) {
			$this->updateOneProduct($order_id, $product['vendor_code'], $product['qty'], ($product['fix'] ?? $product['options']['fix_price']), $request->get('exchange'), ($product['commission'] ?? $product['options']['commission']));
		}
		$exchange = $this->exchange;
		if ($request->get('commission') != 0) {
			$charge = new Charge([
				'type'   => 'percentage',
				'amount' => $request->get('commission'),
			]);
			$this->adjustItemPricesDueToCharges($charge);
			$order->commission = $request->get('commission');
		} else {
			// $this->adjustItemPricesDueToCharges();
			$chargeTemp = Charge::getForPrice(Cart::instance('o' . $order_id)->total());
			if ($chargeTemp->type === 'fixed') {
				$order->fix_commission = $chargeTemp->amount;
				$order->commission = 0;
			} else {
				$order->fix_commission = 0;
				$order->commission = $chargeTemp->amount;
			}
		}
		$total = Cart::instance('o' . $order_id)->total()
			+ $exchange->reconvert($request->get('delivery_cost'))
			+ $exchange->reconvert($request->get('floor'))
			+ ($fixedCommission);

		//        $total = $order->total
		//            -$exchange->reconvert($order->delivery_cost)
		//            +$exchange->reconvert($request->get('delivery_cost'))
		//            +$exchange->reconvert($request->get('floor'))
		//            -$exchange->reconvert($order->floor);
		$price_byn = $order->price_byn;
		$price_byn['total'] = ceil($exchange->convert($total, $request->get('exchange')));
		$price_byn['floor'] = $request->get('floor');
		$order->total = $total;
		$order->delivery_cost = $request->get('delivery_cost');
		$order->delivery_address = $request->get('delivery_address');
		$order->delivery_city = $request->get('delivery_city');
		$order->email = $request->get('email');
		$order->name = $request->get('name');
		$order->phone = $request->get('phone');
		$order->floor = $request->get('floor');
		$order->fix_commission = $fixedCommission;
		$order->comment = $request->get('comment');
		$order->price_byn = $price_byn;
		$order->exchange = $request->get('exchange');

		$order->save();

		return ['success' => true];
	}

	private function adjustItemPricesDueToCharges(Charge $charge = null) {

		$items = Cart::content();
		$total = 0;

		foreach ($items as $key => $item) {
			/** @var Product $product */
			$product = $item->model;
			$price = $product->family_price ? $product->family_price : $product->price;

			if(!$product->fixed_price){
				$total += ceil($price * $item->qty);
			}

		}

		if (!$charge) {
			$charge = Charge::getForPrice($total);
		}

		if (!$charge || $charge->type === 'fixed') {
			return $charge->amount;
		}

		foreach ($items as $item) {
			$product = $item->model;
			$item->price = $this->getFullProductCost($product, $charge);

			$item->options->price_BYN = $product->discount
				? ceil($this->exchange->convert($product->price_order))
				: round($this->exchange->convert($this->getFullProductCost($product, $charge)), 2);
		}

		//        Cart::setContent($items);

		return 0;
	}

	public function delete(Request $request) {
		$validator = Validator::make($request->all(), [
			'order_id' => 'required',
		]);

		if ($validator->fails()) {
			return [
				'success' => false,
				'errors'  => $validator->errors(),
			];
		}

		$order_id = $request->get('order_id');

		$order = Order::query()->findOrNew($request->get('order_id'));
		Cart::instance('o' . $order_id)->restore('o' . $order_id);
		Cart::destroy();
		$order->delete();

		return ['success' => true];
	}

	public function addProduct(Request $request) {
		$product = Product::query()
		                  ->where('vendor_code', 'like', '%' . str_replace('.', '', $request->get('product_code')))
		                  ->firstOrFail();
		$order_id = $request->get('order_id');
		$order = Order::query()->find($order_id);
		Cart::instance('o' . $order_id)->restore('o' . $order_id);
		$charge = Charge::getForPrice(Cart::total() + $product->current_price * $request->get('quantity'));
		$exchange = $this->exchange;
		$product_price = $this->getFullProductCost($product, $charge);
		$data = [
			'id'      => $product->id,
			'name'    => $product->name,
			'qty'     => $request->get('quantity'),
			'price'   => $product->current_price,
			'weight' => 100,
			'options' => [
				'vendor_code'   => $product->vendor_code,
				'fix_price'     => $product->discount == 0 ? 0 : ceil($product->fixed_price),
				'price_BYN'     => $product->discount ? ceil($exchange->convert($product->price_order)) : round($exchange->convert($product->price_order), 2),
				'delivery_cost' => ceil($product_price - $product->current_price),
			],
		];

		if ($product) {
			$isExist = false;
			foreach (Cart::content() as $item) {
				if ($item->model->id === $product->getKey()) {
					$item->options->fix_price = $data['options']['fix_price'];
					$item->options->price_BYN = $data['options']['price_BYN'];
					$item->options->delivery_cost = $data['options']['delivery_cost'];
					$item->qty += $request->get('quantity');
					$isExist = true;
				}
			}
			if (!$isExist) {
				Cart::add($data)->associate('App\Models\Product');
			}

			//$this->adjustItemPricesDueToCharges();
			$total = Cart::total();
			$total_without_commision = $this->calculateTotalWithoutCommission();
			Cart::store('o' . $order_id);
			if ($charge) {
				if ($charge->type !== 'fixed') {
					$order->commission = $charge->amount;
					$order->fix_commission = 0;
				} else {
					$total += $charge->amount;
					$order->fix_commission = $charge->amount;
					$order->commission = 0;
				}
			}
			$price_byn = json_decode($order->price_byn);
			$price_byn->total = ceil($exchange->convert($total));
			$order->total = $total;
			$order->price_byn = json_encode($price_byn);
			$order->without_commission = $total_without_commision;
			$order->save();

			return [
				'success' => true,
				'count'   => Cart::count(),
				$product,
			];
		}

		return [
			'success' => false,
		];
	}
}
