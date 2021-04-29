<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;

/**
 * App\IkeaFamily
 *
 * @property int                                                                 $id
 * @property string                                                              $start_date
 * @property string                                                              $finish_date
 * @property \Illuminate\Support\Carbon|null                                     $created_at
 * @property \Illuminate\Support\Carbon|null                                     $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Product[] $products
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\IkeaFamily newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\IkeaFamily newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\IkeaFamily query()
 * @mixin \Eloquent
 * @property-read int|null                                                       $products_count
 */
class IkeaFamily extends Model {
	protected $fillable = [
		'id',
		'price',
		'start_date',
		'finish_date',
	];

	public function products() {
		return $this->hasMany(Product::class);
	}
}
