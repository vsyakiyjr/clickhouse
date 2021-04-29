<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;

/**
 * App\Models\PromoCode
 *
 * @property int 			$id
 * @property string 		$code
 * @property string 		$discount
 * @property Carbon|null 	$valid_from
 * @property Carbon|null 	$valid_to
 * @property string|null 	$deleted_at
 * @property string|null 	$comment
 * @property-read string	$status		active|pending|expired
 * @method static Builder|PromoCode isActive()
 * @package App\Models
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PromoCode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PromoCode newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PromoCode onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PromoCode query()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PromoCode withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PromoCode withoutTrashed()
 * @mixin \Eloquent
 * @property int $usage_limit
 * @property int $usage_count
 */
class PromoCode extends Model {
	protected $table = 'promo_codes';

	public $timestamps = false;

	protected $dates = [
		'valid_from',
		'valid_to',
	];

	protected $fillable = [
		'code',
		'discount',
		'valid_from',
		'valid_to',
		'comment',
		'usage_limit',
		'usage_count',
	];

	protected $casts = [
		'usage_limit' => 'int',
		'usage_count' => 'int',
	];

	protected $appends = [
		'status',
	];

	protected $dateFormat = 'Y-m-d';

	/**
	 * @param Builder $query
	 * @return mixed
	 */
	public function scopeIsActive($query){
		return $query->where(static function($query){
			$today = Carbon::now();
			$query->where(static function($q){
				$q->whereNull('valid_from')->whereNull('valid_to');
			})->orWhere(static function($q) use($today){
				$q->whereNull('valid_from')->where('valid_to', '>=', $today);
			})->orWhere(static function($q) use($today){
				$q->where('valid_from' , '<=', $today)->whereNull('valid_to');
			})->orWhere(static function($q) use($today){
				$q->where('valid_from' , '<=', $today)->where('valid_to', '>=', $today);
			});
		})->whereRaw('1 = case 
							when usage_limit is null then 1 
							when usage_limit > usage_count then 1 
							else 0 
							end
						 ');
	}

	/**
	 * @return string
	 */
	public function getStatusAttribute(): string {
		$today = Carbon::now();

		if ($this->valid_from && $this->valid_from > $today) {
			return 'pending';
		}
		if ($this->valid_to && $today > $this->valid_to) {
			return 'expired';
		}

		if($this->usage_limit && $this->usage_limit == $this->usage_count){
			return 'used';
		}

		return 'active';
	}

	/**
	 * @param $promoCode
	 *
	 * @return PromoCode
	 */
	public static function getByCode($promoCode){
		return self::where('code', $promoCode)->isActive()->orderBy('id', 'desc')->first();
	}

	/**
	 * @param $promoCode
	 * @return string|null
	 */
	public static function getDiscountIfActive($promoCode): ?string{
		return static::getByCode($promoCode)->discount ?? null;
	}

	/**
	 * @param float  $priceInRub
	 * @param string $promoDiscount
	 *
	 * @return float
	 */
	public static function calculatePriceWithPromo($priceInRub, $promoDiscount): float{
		if (!$promoDiscount) {
			return $priceInRub;
		}

		if (strpos($promoDiscount, 'BYN') !== false) {
			$discountValue = bynToRub(str_replace('BYN', '', $promoDiscount));
		} elseif (strpos($promoDiscount, '%') !== false) {
			$discountPercent = str_replace('%', '', $promoDiscount);
			$discountValue = $priceInRub * ($discountPercent / 100);
		} else {
			$discountValue = 0;
		}

		return round(max($priceInRub - $discountValue, 0), 2);
	}
}
