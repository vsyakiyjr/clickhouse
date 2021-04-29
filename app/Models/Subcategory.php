<?php

namespace App\Models;

use App\Models\Cms\Page;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Subcategory
 *
 * @property int                                                                 $id
 * @property string|null                                                         $name
 * @property string|null                                                         $alias
 * @property string|null                                                         $link
 * @property int|null                                                            $category_id
 * @property int|null                                                            $new
 * @property-read \App\Models\Category|null                                      $category
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Product[] $products
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subcategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subcategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Subcategory query()
 * @mixin \Eloquent
 * @property-read int|null                                                       $products_count
 * @property int|null                                                            $page_id
 * @property-read \App\Models\Cms\Page                                           $page
 * @property-read mixed                                                          $url
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property int $visible
 * @property string|null $ikea_id
 * @property string $img
 */
class Subcategory extends Model {
	protected $table = 'subcategories';

	protected $fillable =[
		'name',
		'link',
		'category_id',
		'new',
		'alias',
		'ikea_id',
        'img'
	];

	protected $dates = [
		'created_at',
		'updated_at'
	];

	protected $casts = [
		'new'     => 'boolean',
		'visible' => 'boolean',
	];

	protected $appends = [
		'products_count'
	];

	public function getUrlAttribute(){
		return '/catalog/' . $this->category->alias . '/' . $this->alias;
	}

	public function products() {
		return $this->belongsToMany(Product::class, 'products_subcategories', 'subcategory_id', 'product_id');
	}

	public function page(){
		return $this->hasOne(Page::class, 'id', 'page_id');
	}

	public function category() {
		return $this->belongsTo(Category::class, 'category_id', 'id');
	}

	public function getProductsCountAttribute(){
		return $this->products()->count();
	}
}
