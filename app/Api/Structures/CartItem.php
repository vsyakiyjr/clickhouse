<?php

namespace App\Api\Structures;

use App\Models\Product;

class CartItem extends DataStructure {
	/** @var Product $model */
	public $model;

	public $product_id;

	public $qty;

	/** @var float закупочная цена, RUB */
	public $price;
	public $price_with_qty;
	/** @var float закупочная цена, BYN */
	public $price_byn;
	public $price_byn_with_qty;

	/** @var float цена для оплаты клиентом с учётом комиссии, RUB */
	public $price_order;
	public $price_order_with_qty;

	/** @var float цена для оплаты клиентом с учётом комиссии, BYN */
	public $price_order_byn;
	public $price_order_byn_with_qty;

	public $vendor_code;

	/** @var float комиссия за товар, RUB */
	public $commission_amount_rub;
	/** @var float комиссия за товар, BYN */
	public $commission_amount_byn;

	/** @var int комиссия в процентах (если применённое правило в %) */
	public $commission_percent;

	public $fixed_price = false;

	/** @var int Timestamp когда товар был добавлен в корзину */
	public $added_at;

	/**
	 * Pass model, producit
	 *
	 * @param $data
	 */
	public function seed($data) {
		$this->model           = $data['model'] instanceof Product ? $data['model'] : new Product($data['model']);
		$this->product_id      = $this->model->id;
		$this->vendor_code     = $this->model->vendor_code;
		$this->added_at = $data['added_at'] ?? time();

		$this->price           = (float)($this->model->family_price ?: $this->model->price);

		$this->fixed_price     = $data['fixed_price'] ?? (boolean)$this->model->fixed_price;
		$this->price_order     = $this->fixed_price ? $this->model->fixed_price_in_rub : $this->price;
		$this->price_byn       = rubInByn($this->price);

		//		$this->price_order     = round((float) $data['price_order'], 2);
//		$this->price_order_byn = rubInByn($this->price_order);

		if(empty($data['price_with_qty'])) {
			$this->setQty($data['qty']);
		} else {
			// deserializing, all calculated data persists
			$this->qty = $data['qty'];
			$this->price_with_qty = $data['price_with_qty'] ?? 0;
			$this->price_byn_with_qty = $data['price_byn_with_qty'] ?? 0;
			$this->price_order_with_qty = $data['price_order_with_qty'] ?? 0;
			$this->price_order = $data['price_order'] ?? 0;
			$this->price_order_byn = $data['price_order_byn'] ?? 0;
			$this->price_order_byn_with_qty = $data['price_order_byn_with_qty'] ?? 0;

			$this->commission_amount_rub = $data['commission_amount_rub'] ?? 0;
			$this->commission_amount_byn = $data['commission_amount_byn'] ?? 0;
			$this->commission_percent = $data['commission_percent'] ?? 0;
		}
	}

	/**
	 * Set qty and update price with qty
	 *
	 * @param $qty
	 */
	public function setQty($qty){
		$this->qty                      = (int)$qty;
		$this->price_with_qty           = $this->price * $this->qty;
		$this->price_byn_with_qty       = $this->price_byn * $this->qty;

		$this->price_order_with_qty     = $this->price_order * $this->qty;
		$this->price_order_byn_with_qty = $this->price_order_byn * $this->qty;
	}

	/**
	 * Update price order with commission
	 *
	 * @param $commissionAmountRub
	 */
	public function setCommission($commission, $type){

		if($this->fixed_price){
			$this->commission_amount_rub = ceil($this->model->discount);
			$this->commission_amount_byn = ceil(rubInByn($this->model->discount));
			$this->commission_percent = 0;
			$this->price_order = $this->model->fixed_price_in_rub;
			$this->price_order_byn = ceil(rubInByn($this->price_order));
		} else {
			if($type === 'fixed'){
				// фиксированная комиссия определяется на весь заказ
				$this->commission_amount_rub = 0;
				$this->commission_percent = 0;
			} else {
				$this->commission_amount_rub = $this->price * $commission / 100;
				$this->commission_percent = $commission;
			}

			$this->commission_amount_byn = rubInByn($this->commission_amount_rub);
			$this->price_order = $this->price + $this->commission_amount_rub;
			$this->price_order_byn = rubInByn($this->price_order);
			$this->price_order_with_qty = $this->price_order * $this->qty;
			$this->price_order_byn_with_qty = rubInByn($this->price_order_with_qty);
		}

		$this->setQty($this->qty);
	}
}
