<?php

namespace App\Models;

use App\Models\Cms\Page;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Category
 *
 * @property int                                                                       $id
 * @property string                                                                  $name
 * @property string                                                                  $alias
 * @property string|null                                                             $img
 * @property int                                                                     $visible
 * @property float                                                                   $discount
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Subcategory[] $subcategories
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category query()
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Product[]     $products
 * @property-read int|null                                                           $products_count
 * @property-read int|null                                                           $subcategories_count
 * @property int                                                                     $is_new
 * @property int|null                                                                $page_id
 * @property-read \App\Models\Cms\Page                                               $page
 * @property-read mixed                                                              $url
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $ikea_id
 * @property string|null $link
 */
class Category extends Model {
	protected $table = 'categories';

	protected $fillable = [
		'name',
		'alias',
		'img',
		'visible',
		'discount',
		'is_new',
		'link',
		'ikea_id',
	];

	protected $dates = [
		'created_at',
		'updated_at'
	];

	protected $casts =[
		'visible'  => 'boolean',
		'discount' => 'float',
		'is_new'   => 'boolean',
	];

	public function subcategories() {
		return $this->belongsToMany(Subcategory::class, 'categories_subcategories_visible','category_id', 'subcategory_id');
	}

	public function getUrlAttribute(){
		return '/catalog/' . $this->alias;
	}

	public function page(){
		return $this->hasOne(Page::class, 'id', 'page_id');
	}

	public function products(){
		return $this->belongsToMany(Product::class, 'products_categories_visible', 'category_id', 'product_id');
	}
}
