<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Cities
 *
 * @property int         $id
 * @property string|null $alias
 * @property string      $name
 * @property int|null    $visible
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cities newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cities newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cities query()
 * @mixin \Eloquent
 */
class Cities extends Model {
	protected $table = 'cities';
}
