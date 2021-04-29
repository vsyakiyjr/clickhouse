<?php

namespace App\Api\Structures;

use App\Helpers\Calendar;
use App\Models\Charge;
use App\Models\Product;

class Cart extends DataStructure {
	/** @var CartItem[] */
	public $items = [];

	public $itemsCount = 0;

	public $itemsIdsWithFixedPrice = [];
	public $itemsIdsWithCommonPrice = [];

	/** @var float */
	public $totalPriceRub = 0.00;
	public $totalPriceByn = 0.00;

	/** @var float Закупочная стоимость */
	public $totalRawPriceRub = 0.00;
	public $totalRawPriceByn = 0.00;

	/** @var Charge */
	public $charge;

	/** @var float Общая комиссия за весь заказ, RUB */
	public $totalChargeRub;

	/** @var float Общая комиссия за весь заказ, BYN */
	public $totalChargeByn;

	/** @var Charge */
	public $nextCharge;

	/** @var float */
	public $rubBeforeNextCharge = 0;

	/** @var float */
	public $bynBeforeNextCharge = 0;

	public $nearestDeliveryDayLocale;

	/** @var string id of associated model */
	public $id;

	public function seed($data) {

		$nearestDeliveryDay = Calendar::nextDelivery();
		$this->nearestDeliveryDayLocale = $nearestDeliveryDay;

		$this->itemsCount = $data['itemsCount'] ?? 0;

		$this->itemsIdsWithFixedPrice = $data['itemsIdsWithFixedPrice'] ?? [];
		$this->itemsIdsWithCommonPrice = $data['itemsIdsWithCommonPrice'] ?? [];
		$this->rubBeforeNextCharge = $data['rubBeforeNextCharge'] ?? '';
		$this->bynBeforeNextCharge = $data['bynBeforeNextCharge'] ?? '';

		$this->totalPriceByn = (float)($data['totalPriceByn'] ?? 0);
		$this->totalPriceRub = (float)($data['totalPriceRub'] ?? 0);
		$this->totalRawPriceByn = (float)($data['totalRawPriceByn'] ?? 0);
		$this->totalRawPriceRub = (float)($data['totalRawPriceRub'] ?? 0);

		$updateCartNeeded = false;
		foreach ($data['items'] ?? [] as $item) {
			$this->items[$item['product_id']] = new CartItem($item);
			if($this->items[$item['product_id']]->qty <= 0){
			    $this->removeItem($item['product_id']);
                $updateCartNeeded = true;
            }
		}

		if($updateCartNeeded){
		    $this->postUpdateCart();
        }

		if(!empty($data['charge'])) {
			$this->charge = $data['charge'] instanceof Charge ? $data['charge'] : new Charge($data['charge']);
		}


		if (!empty($data['nextCharge'])) {
			$this->nextCharge = $data['nextCharge'] instanceof Charge ? $data['nextCharge'] : new Charge($data['nextCharge']);
		}

		$this->roundPrices();
	}

	/**
	 * Add item to cart
	 *
	 * @param Product $product
	 * @param int     $qty
	 */
	public function addItem(Product $product, $qty = 1){
		$cartItem = $this->items[$product->id] ?? new CartItem([
			'model' => $product,
			'qty'   => 0,
		]);

		$cartItem->setQty($qty);

		$this->items[$product->id] = $cartItem;

		if ($product->fixed_price) {
			$this->itemsIdsWithFixedPrice[$product->id] = $product->id;
		} else {
			$this->itemsIdsWithCommonPrice[$product->id] = $product->id;
		}

		$this->postUpdateCart();
	}

	/**
	 * Change qty
	 *
	 * @param $productId
	 * @param $qty
	 *
	 * @return void|null
	 */
	public function changeQty($productId, $qty){
		if($qty <= 0){
			return $this->removeItem($productId);
		}

		$cartItem = $this->items[$productId] ?? null;

		if($cartItem) {
			$cartItem->setQty($qty);
			$this->items[$productId] = $cartItem;
		} else {
			$product = Product::find($productId);

			if (!$product) {
				// trying to add non-existing product
				return null;
			}

			$this->addItem($product, $qty);
		}

		$this->postUpdateCart();
	}

	/**
	 * @param $productId
	 */
	public function removeItem($productId){
		unset($this->items[$productId]);
		unset($this->itemsIdsWithFixedPrice[$productId]);
		unset($this->itemsIdsWithCommonPrice[$productId]);

		$this->postUpdateCart();
	}

	/**
	 *
	 */
	protected function updateTotals(){
		$this->totalPriceRub = 0;
		$this->totalPriceByn = 0;

		$this->totalChargeByn = 0;
		$this->totalRawPriceRub = 0;
		$this->totalRawPriceByn = 0;
		$this->totalChargeRub = 0;

		foreach ($this->itemsIdsWithCommonPrice as $itemId) {
			$cartItem = $this->items[$itemId];
			$this->totalPriceRub += $cartItem->price_order_with_qty;
			$this->totalRawPriceRub += $cartItem->price_with_qty;

			$this->totalChargeRub += $cartItem->commission_amount_rub;
		}
		unset($itemId);

		foreach ($this->itemsIdsWithFixedPrice as $itemId){
			$cartItem = $this->items[$itemId];
			$this->totalPriceRub += $cartItem->price_order_with_qty;
			$this->totalRawPriceRub += $cartItem->price_with_qty;
		}
		unset($itemId);


		if($this->charge && $this->charge->type === 'fixed') {
			$this->totalChargeRub = $this->charge->amount;

			$this->totalPriceRub += $this->totalChargeRub;
		} else {
			$this->totalChargeRub = round($this->totalChargeRub, 2);
		}

		$this->totalPriceByn = ceil(rubInByn($this->totalPriceRub));
		$this->totalChargeByn = ceil(rubInByn($this->totalChargeRub));
		$this->totalRawPriceByn = ceil(rubInByn($this->totalRawPriceRub));
	}

	protected function updateItemsCount(){
		$itemsCount = 0;

		foreach ($this->items as $item){
			$itemsCount += $item->qty;
		}

		$this->itemsCount = $itemsCount;
	}

	protected function roundPrices(){
		foreach ($this->items as &$item){
			foreach (['price', 'price_byn', 'price_order', 'price_order_byn', 'price_order_byn_with_qty', 'price_order_with_qty'] as $prop){
				$item->{$prop} = round($item->{$prop}, 2);
			}
		}
	}

	/**
	 *
	 */
	protected function setCharges(){
		if(empty($this->itemsIdsWithCommonPrice)){
			$this->charge = new Charge(['type' => 'percentage', 'amount' => 0]);
		} else {
			//комиссия определяется от закупочной стоимости товаров
			$this->charge = Charge::getForPrice($this->totalRawPriceRub);
		}

		foreach ($this->items as $item){
			$item->setCommission($this->charge->amount, $this->charge->type);
		}

		$this->nextCharge = Charge::getNext($this->charge);

		if($this->nextCharge){
			// комиссия определяется от закупочной стоимости товаров, поэтому сумму до следующей комиссии надо определять от закупочной стоимости
			$this->rubBeforeNextCharge = $this->nextCharge->total_from - $this->totalRawPriceRub;
			$this->bynBeforeNextCharge = rubInByn($this->rubBeforeNextCharge);
		} else {
			$this->rubBeforeNextCharge = 0;
			$this->bynBeforeNextCharge = 0;
		}

		// update total price with commissions set
		$this->updateTotals();
	}

	protected function postUpdateCart() {
		$this->updateTotals();
		$this->setCharges();
		$this->updateItemsCount();
		$this->roundPrices();
	}
}
