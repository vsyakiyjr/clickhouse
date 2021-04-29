<?php
/**
 * Created by PhpStorm.
 * User: baduser
 * Date: 26.05.2018
 * Time: 22:33
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Customers
 *
 * @property int                             $id
 * @property string                          $name
 * @property string                          $phone
 * @property string                          $email
 * @property string|null                     $address
 * @property string|null                     $apt
 * @property \Illuminate\Support\Carbon      $updated_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Customers newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Customers newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Customers query()
 * @mixin \Eloquent
 */
class Customers extends Model {
	protected $table = 'customers';

	protected $fillable = [
		'name',
		'phone',
		'email',
		'address',
		'apt',
	];
}