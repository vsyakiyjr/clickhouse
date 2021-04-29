<?php

namespace App\Models\Cms;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Cms\PageView
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $page_id
 * @property string $visitor_ip
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cms\PageView newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cms\PageView newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cms\PageView query()
 * @mixin \Eloquent
 */
class PageView extends Model {
	protected $table = 'cms__page_views';

	protected $fillable = [
		'page_id',
		'visitor_ip'
	];
}
