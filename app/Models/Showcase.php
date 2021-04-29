<?php
/**
 * Created by PhpStorm.
 * User: baduser
 * Date: 13.05.2018
 * Time: 14:06
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Showcase
 *
 * @property int    $id
 * @property string $vendor_code
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Showcase newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Showcase newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Showcase query()
 * @mixin \Eloquent
 */
class Showcase extends Model {
	protected $table = 'showcase';

	public static
	function fillProducts() {
		$data = self::leftJoin('products', 'showcase.vendor_code', '=', 'products.vendor_code')
		            ->get();

		return $data;
	}
}