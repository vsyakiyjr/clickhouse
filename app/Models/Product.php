<?php

namespace App\Models;

use App\Services\ExchangeService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Product
 *
 * @property int                                                                       $id
 * @property string                                                                    $name
 * @property string                                                                    $vendor_code
 * @property string                                                                    $type
 * @property string                                                                    $description
 * @property string|null                                                               $color
 * @property string|null                                                               $height
 * @property string|null                                                               $width
 * @property string|null                                                               $length
 * @property string|null                                                               $weight
 * @property string|null                                                               $depth
 * @property string|null                                                               $packaging
 * @property string                                                                    $photo
 * @property string|null                                                               $gallery
 * @property bool                                                                      $fixed_price
 * @property float                                                                     $price
 * @property string|null                                                               $family_price
 * @property string|null                                                               $family_offers_start
 * @property string|null                                                               $family_offers_end
 * @property float                                                                     $discount
 * @property int                                                                       $visible
 * @property int|null                                                                  $available
 * @property bool|null                                                                 $quantity_controll
 * @property int|null                                                                  $quantity
 * @property bool|null                                                                 $new
 * @property bool                                                                      $popular
 * @property string                                                                    $link
 * @property string|null                                                               $mod_group
 * @property Carbon                                                					   $updated_at
 * @property Carbon                                                					   $created_at
 * @property string|null                                                               $benefit
 * @property string|null                                                               $good_to_know
 * @property string|null                                                               $sold_separately
 * @property string|null                                                               $cust_materials
 * @property string|null                                                               $attachments
 * @property string|null                                                               $pkg_info
 * @property string|null                                                               $additional_products
 * @property string|null                                                               $size
 * @property int|null                                                                  $qty_orders
 * @property bool                                                                 $price_family
 * @property int|null                                                             $ikea_family_id
 * @property array|null                                                           $possible_attributes
 * @property array|null                                                           $attributes
 * @property string|null                                                          $sizes_original
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Category[] $categories
 * @property-read mixed                                                           $category
 * @property-read mixed                                                           $current_price
 * @property-read mixed                                                           $errors
 * @property-read mixed                                                           $price_final
 * @property-read mixed                                                              $family_discount_percent
 * @property-read mixed                                                              $price_order
 * @property-read mixed                                                              $price_with_discount
 * @property-read mixed                                                              $price_with_fixed
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Subcategory[] $subcategories
 * @property-read array                                                              $package_info
 * @property-read array                                                              $attachments_list
 * @property-read array                                                              $vendor_code_with_dots
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product query()
 * @mixin \Eloquent
 * @property-read int|null                                                           $categories_count
 * @property-read int|null                                                           $subcategories_count
 * @property int $priority
 * @property int $cat_ikea_id
 * @property Carbon $parsed_at
 * @property boolean $parsed_at_this_run
 * @property-read Cat $cat
 * @property-read mixed $fixed_price_in_rub
 * @property string|null $name_fuzzy
 * @property string|null $type_fuzzy
 * @property string|null $description_fuzzy
 * @property-read \App\Models\IkeaFamily $ikeaFamilies
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ProductsVisible[] $visibles
 * @property-read int|null $visibles_count
 */
class Product extends Model {
	protected $table = 'products';

	protected $fillable = [
		'id',
		'name',
		'vendor_code',
		'type',
		'description',
		'color',
		'height',
		'width',
		'length',
		'weight',
		'depth',
		'packaging',
		'photo',
		'gallery',
		'quantity_controll',
		'quantity',
		'fixed_price',
		'price',
		'price_promo',
		'visible',
		'new',
		'link',
		'mod_group',
		'family_price',
		'ikea_family_id',
		'family_offers_start',
		'family_offers_end',
		'discount',
		'available',
		'benefit',
		'good_to_know',
		'sold_separately',
		'cust_materials',
		'attachments',
		'pkg_info',
		'additional_products',
		'size',
		'qty_orders',
		'attributes',
		'possible_attributes',
		'sizes_original',
		'priority',
		'cat_ikea_id',
		'parsed_at',
		'parsed_at_this_run',

		'name_fuzzy',
		'type_fuzzy',
		'description_fuzzy',
	];

	protected $casts = [
		'attributes'          => 'array',
		'possible_attributes' => 'array',
		'quantity'            => 'int',
		'discount'            => 'float',
		'qty_orders'          => 'int',
		'priority'            => 'int',
		'price'               => 'float',
		'new'                 => 'boolean',
		'popular'             => 'boolean',
		'price_family'        => 'boolean',
		'fixed_price'         => 'boolean',
		'quantity_controll'   => 'boolean',
		'parsed_at_this_run'  => 'boolean',
	];

	protected $dates = [
		'updated_at',
		'created_at',
		'parsed_at',
	];

	protected $appends = [
		'price_final',
		'price_with_discount',
		'errors',
		'current_price',
		'price_order',
	];

	public function ikeaFamilies() {
		return $this->belongsTo(IkeaFamily::class);
	}

	public function categories() {
		return $this->belongsToMany(Category::class, 'products_categories', 'product_id', 'category_id');
	}

	public function subcategories() {
		return $this->belongsToMany(Subcategory::class, 'products_subcategories', 'product_id', 'subcategory_id');
	}

	public function cat(){
		return $this->hasOne(Cat::class, 'id', 'cat_ikea_id');
	}

	public function getCategoryAttribute() {
		return $this->categories->first();
	}

	public function getAttachmentsListAttribute(){
		if(!$this->attachments || empty(json_decode($this->attachments, true))){
			return false;
		}

		return json_decode($this->attachments, true);
	}

	public function getPackageInfoAttribute(){
		if(!$this->pkg_info || empty(json_decode($this->pkg_info, true))) {
			return false;
		}

		return json_decode($this->pkg_info, true);
	}

	public function getFamilyPriceAttribute($value) {
		return (float) preg_replace("/[^0-9]/", "", $this->attributes['family_price']);
	}

	public function getPriceFinalAttribute($value) {
		$special = $this->family_price;

		return $special ? $special : $this->price_with_discount;
	}

	public function getErrorsAttribute(){
		return false;
	}

	/**
	 * Закупочная цена
	 *
	 * @return float|string|null
	 */
	public function getCurrentPriceAttribute() {
		$price = $this->family_price;

		if (!$price) {
			$price = $this->price;
		}

		return $price;
	}

	public function getPriceOrderAttribute() {

		if($this->fixed_price_in_rub){
			return $this->price;
		}

		$charge = \Session::get('charge');

		$price = $this->family_price;
		if (!$price) {
			return $this->price_with_discount;
		}
		$fixedCharge = $this->discount;

		if ($fixedCharge) {
			$price += $fixedCharge;
		} elseif ($charge) {
			$price += $charge->calculate($price);
		}

		return $price;
	}

	public function getPriceWithFixedAttribute() {
		$price = $this->price;

		$fixedCharge = $this->discount;

		if ($fixedCharge) {
			$price += $fixedCharge;
		}

		return $price;
	}

	public function getPriceWithDiscountAttribute($value) {
		/** @var Charge $charge */
		$charge = \Session::get('charge');

		$price = $this->price;

		$fixedCharge = $this->discount;

		if ($fixedCharge) {
			$price += $fixedCharge;
		} elseif ($charge) {
			$price += $charge->calculate($price);
		}

		return $price;
	}

	public function getFixedPriceAttribute() {
		$price = $this->price;

		$fixedCharge = $this->discount;

		if ($fixedCharge) {
			$price += $fixedCharge;
		} else {
			$price = 0;
		}

		return ceil(app()->make(ExchangeService::class)->convert($price));
	}

	public function getFixedPriceInRubAttribute() {
		$price = $this->price;

		$fixedCharge = $this->discount;

		if ($fixedCharge) {
			$price += $fixedCharge;
		} else {
			$price = 0;
		}

		return 	$price;
	}

	public function getFamilyDiscountPercentAttribute(){
		if($this->price_with_discount == 0) {
			return 0;
		}

		return round((1 - $this->price_final / $this->price_with_discount) * 100);
	}

	public function getVendorCodeWithDotsAttribute() {
		$vendorCode = $this->vendor_code;
		$vendorCodeVisible = preg_replace('/[^0-9]/', '', $vendorCode);
		$vendorCodeVisible = substr_replace($vendorCodeVisible, ".", 3, 0);
		$vendorCodeVisible = substr_replace($vendorCodeVisible, ".", 7, 0);

		return $vendorCodeVisible;
	}

	public function visibles() {
		return $this->hasMany(ProductsVisible::class, 'product_id', 'id');
	}
}
