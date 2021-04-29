<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ParserStatus
 *
 * @property int                            $id
 * @property int                          $process_id
 * @property int                          $subcategory_id
 * @property string                       $vendor_code
 * @property string                       $url
 * @property int                          $total_subcategories
 * @property int                          $processed_subcategories
 * @property int                          $processed_products
 * @property int                          $total_products
 * @property-read \App\Models\Subcategory $subcategory
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ParserStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ParserStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ParserStatus query()
 * @mixin \Eloquent
 */
class ParserStatus extends Model {
	public $timestamps = false;

	protected $table = 'parser_status';

	protected $fillable = [
		'process_id',
		'subcategory_id',
		'vendor_code',
		'total_subcategories',
		'processed_subcategories',
		'processed_products',
		'total_products',
		'url',
	];

	public function subcategory() {
		return $this->belongsTo(Subcategory::class);
	}
}
