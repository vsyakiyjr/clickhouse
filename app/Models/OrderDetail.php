<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\OrderDetail
 *
 * @property int                             $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int                             $order_id
 * @property float                           $price
 * @property int                             $qty
 * @property int|null                        $product_id
 * @property string                          $vendor_code
 * @property array|null                      $options
 * @property mixed|null                      $model
 * @property-read \App\Models\Product        $product
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderDetail query()
 * @mixin \Eloquent
 */
class OrderDetail extends Model {
	protected $table = 'order_details';

	protected $casts = [
		'price'   => 'float',
		'qty'     => 'int',
		'options' => 'json',
		'model'   => 'json',
	];

	protected $fillable = [
		'order_id',
		'price',
		'qty',
		'product_id',
		'vendor_code',
		'options',
		'model',
	];

	public function product() {
		return $this->hasOne(Product::class, 'id', 'product_id');
	}
}
