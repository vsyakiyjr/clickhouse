<?php

namespace App\Services;

use Cache;
use Illuminate\Support\Facades\DB;

class ExchangeService {
	/**
	 * @var float
	 */
	private $rate = 0;

	public function __construct() {
		$this->rate = static::getExchange();
	}

	/**
	 * @param int        $amount
	 * @param float|null $rate
	 *
	 * @return float|int
	 */
	public function convert(float $amount, float $rate = null) {
		if ($rate) {
			return $amount * $rate;
		}

		return ($amount * $this->rate);
	}

	/**
	 * @param int $amount
	 */
	public function reconvert(int $amount) {
		return floor($amount / $this->rate);
	}

	/**
	 * @param float $amount
	 */
	public function doubleConvert(float $amount) {
		$a = ceil($amount * $this->rate);
		$b = ($a / $this->rate);

		return $b;
	}

	public function getRate() {
		return $this->rate;
	}

	public static function getExchange(){
		$tabl_exchanges = \DB::table('exchanges')->select('echange_rates')->get();

		return $tabl_exchanges[0]->echange_rates;
	}
}
