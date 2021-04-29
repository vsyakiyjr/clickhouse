<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Discounts
 *
 * @property int $id
 * @property int $from
 * @property int $to
 * @property int $percentage
 * @property int $category_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Discounts newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Discounts newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Discounts query()
 * @mixin \Eloquent
 */
class Discounts extends Model {
	public $timestamps = false;

	protected $table = 'discounts';

	protected $fillable = [
		'from',
		'to',
		'percentage',
	];
}
