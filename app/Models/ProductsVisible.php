<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ProductsVisible
 *
 * @property int $id
 * @property int $product_id
 * @property int $category_id
 * @property int $sub_category_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Category|null $category
 * @property-read \App\Models\Product|null $product
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductsVisible newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductsVisible newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductsVisible query()
 * @mixin \Eloquent
 */
class ProductsVisible extends Model
{
    protected $fillable = [
        'product_id',
        'category_id',
        'sub_category_id',
    ];

    public function product() {
        return $this->hasOne(Product::class, 'product_id');
    }

    public function category() {
        return $this->hasOne(Category::class, 'category_id');
    }
}
