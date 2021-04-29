<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Charge
 *
 * @package App\Models
 * @mixin \Eloquent
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property float  $total_from Total in RUB from
 * @property float  $total_to Total in RUB to
 * @property float  $type fixed/percentage
 * @property float  $amount Charges to apply in BYN
 * @property int    $id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Charge newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Charge newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Charge query()
 */
class Charge extends Model {
	protected $table = 'charges';

	protected $fillable = [
		'id',
		'total_from',
		'total_to',
		'type',
		'amount',
	];

	protected $casts = [
		'total_from' => 'float',
		'total_to'   => 'float',
		'amount'     => 'float',
	];

	protected $dates = [
		'created_at',
		'updated_at',
	];

	/**
	 * @param $price
	 *
	 * @return Charge
	 */
	public static function getForPrice($price) {
		$price = (float) $price;

		$charge = static::where(function ($q) use ($price) {
			/** @var Builder $q */
			return $q->where('total_from', '<=', $price)
			         ->where('total_to', '>', $price);
		})
        ->orWhere(function ($q) use ($price) {
            /** @var Builder $q */
            return $q->whereNull('total_from')
                     ->where('total_to', '>', $price);
        })->orWhere(function ($q) use ($price) {
			/** @var Builder $q */
			return $q->whereNull('total_to')->where('total_from', '<', $price);
		})->orderBy('id')
          ->get()
          ->first();

		$charge = $charge ?? new Charge(['amount' => 0]);

		return $charge;
	}

	public static function getForPriceWithNext($price) {
		$price = (float) $price;

		$charge = static::where(function ($q) use ($price) {
			/** @var Builder $q */
			return $q->where('total_from', '<=', $price)
			         ->where('total_to', '>', [$price]);
		})
        ->orWhere(function ($q) use ($price) {
            /** @var Builder $q */
            return $q->whereNull('total_from')
                     ->where('total_to', '>', [$price]);
        })->orWhere(function ($q) use ($price) {
			/** @var Builder $q */
			return $q->whereNull('total_to')
			         ->where('total_from', '<', $price);
		})->orderBy('id')
	      ->get()
	      ->first();

		return $charge;
	}

	public static function getNext(Charge $charge) {
		if ($charge->total_to === null) {
			return null;
		}

		$nextCharge = self::query()->where('total_from', '>=', $charge->total_to)->where('id', '!=', $charge->id)->first();

		return $nextCharge;
	}

	public function calculate($price) {
		if($this->type === "percentage"){
			return $price * $this->amount / 100;
		}

		// for fixed price
		return 0;
	}
}
