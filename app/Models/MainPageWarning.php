<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\MainPageWarning
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $title
 * @property string $description
 * @property bool $visible
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MainPageWarning newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MainPageWarning newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MainPageWarning query()
 * @mixin \Eloquent
 */
class MainPageWarning extends Model {
	protected $table = 'main_page_warning';

	protected $fillable = [
		'title',
		'description',
		'visible',
	];

	protected $casts = [
		'visible' => 'boolean',
	];
}
