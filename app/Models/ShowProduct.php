<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ShowProduct
 *
 * @property int                             $id
 * @property string                          $vendor_code
 * @property string                          $place
 * @property integer                         $show_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShowProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShowProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShowProduct query()
 * @mixin \Eloquent
 * @property-read \App\Models\Product        $product
 */
class ShowProduct extends Model {

	protected $table = 'show_products';

	public static function headerProducts() {
		$data = self::leftJoin('products', 'show_products.vendor_code', '=', 'products.vendor_code')
			->orderBy('show_order')
			->where('place', 'header');

		return $data;
	}

	public function product(){
		return $this->hasOne(Product::class, 'vendor_code', 'vendor_code');
	}

	public static function productsForMain(){
		return static::with('product')
		             ->where('place', 'main')
		             ->orderBy('show_order')
					 ->get();
	}

	public static function productsForHeader(){
		return static::with('product')
		             ->where('place', 'header')
		             ->orderBy('show_order')
		             ->get();
	}

	public static function mainProducts() {
		$data = self::leftJoin('products', 'show_products.vendor_code', '=', 'products.vendor_code')
					->orderBy('show_order')
		            ->where('place', 'main');

		return $data;
	}
}
