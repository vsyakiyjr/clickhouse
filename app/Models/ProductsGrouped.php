<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ProductsGrouped
 *
 * @property int                                                                       $id
 * @property string                                                                    $parent_vendor_code
 * @property string                                                                    $grouped_vendor_code
 * @method static Builder|Product newModelQuery()
 * @method static Builder|Product newQuery()
 * @method static Builder|Product query()
 * @mixin \Eloquent
 */
class ProductsGrouped extends Model {
	protected $table = 'products_grouped';
	public $timestamps = false;

    protected $fillable = [
        'parent_vendor_code',
        'grouped_vendor_code',
    ];
}
