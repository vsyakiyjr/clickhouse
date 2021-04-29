<?php

namespace App\Models;

use App\Api\Structures\Cart;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Order
 *
 * @property int                      $id
 * @property int                      $order_num
 * @property string                   $email
 * @property string                   $phone
 * @property string                   $name
 * @property string                   $user_id
 * @property string                   $delivery_date
 * @property string                   $delivery_city
 * @property int                      $delivery_type
 * @property string|null              $delivery_address
 * @property string|null              $delivery_house
 * @property float                    $delivery_cost
 * @property float                    $total
 * @property Carbon|null              $created_at
 * @property Carbon|null              $updated_at
 * @property string|null              $vendor_code
 * @property float                    $exchange
 * @property int|null                 $floor
 * @property float|null               $commission
 * @property string|null              $comment
 * @property string|null              $price_byn
 * @property float                    $fix_commission
 * @property float                    $without_commission
 * @property Collection|OrderDetail[] $details
 * @property-read int|null            $details_count
 * @property string|null              $delivery_stairs
 * @property string|null              $delivery_apartment
 * @property bool                     $delivery_assemble
 * @property bool                     $delivery_lifting
 * @property array                    $cart_details
 * @property Cart|null                $cart
 * @property int|null                 $delivery_floor
 * @property string					  $promo_code
 * @property string					  $promo_discount
 * @property-read float				  $total_with_promo
 * @method static Builder|Order newModelQuery()
 * @method static Builder|Order newQuery()
 * @method static Builder|Order query()
 * @mixin Eloquent
 * @property-read mixed $commission_in_byn
 * @property-read mixed $commission_in_rub
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Order onlyTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Order withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Order withoutTrashed()
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class Order extends Model {
    use SoftDeletes;

	protected $table = 'orders';

	protected $fillable = [
		'order_num',
		'email',
		'phone',
		'name',
		'user_id',
		'delivery_date',
		'delivery_city',
		'delivery_type',
		'delivery_address',
		'delivery_house',
		'delivery_stairs',
		'delivery_apartment',
		'delivery_floor',
		'delivery_lifting',
		'delivery_assemble',
		'delivery_cost',

		'total',
		'vendor_code',
		'exchange',
		'without_commission',
		'commission',
		'comment',
		'price_byn',
		'fix_commission',
		'cart_details',
		'promo_code',
		'promo_discount',
	];

	protected $dates = [
	    'deleted_at'
    ];

	protected $casts = [
		'delivery_cost'      => 'float',
		'total'              => 'float',
		'fix_commission'     => 'float',
		'without_commission' => 'float',
		'exchange'           => 'float',
		'price_byn'          => 'json',
		'cart_details'       => 'json',
		'delivery_lifting'   => 'boolean',
		'delivery_assemble'  => 'boolean',
	];

	protected $appends = [
		'total_with_promo',
	];

	public function details() {
		return $this->hasMany(OrderDetail::class, 'order_id', 'id');
	}

	public function getCartAttribute(){
		if(empty($this->cart_details)) {
			return null;
		}

		return new Cart($this->cart_details);
	}

	public function setCartAttribute(Cart $cart){
		$this->cart_details = $cart->toArray();
	}

	public function getCommissionInBynAttribute(){
		$cart = $this->cart;

		return $cart->totalPriceByn - $cart->totalRawPriceByn;
	}

	public function getCommissionInRubAttribute(){
		$cart = $this->cart;

		return $cart->totalPriceRub - $cart->totalRawPriceRub;
	}

	public function getTotalWithPromoAttribute(): float{
		$cart = $this->cart;

		if(!$cart){
			return $this->price_byn['total'];
		}

		$commission = $cart->totalPriceRub - $cart->totalRawPriceRub;

		$discountedCommissionPriceRub = PromoCode::calculatePriceWithPromo($commission, $this->promo_discount);
		$totalPriceInByn = ceil(rubInByn($cart->totalRawPriceRub + $discountedCommissionPriceRub, true));

		return $this->delivery_cost + $totalPriceInByn;
	}

}
