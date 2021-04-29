<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ParserSitemapsQueue
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $sitemap_url
 * @property int $queue_pos
 * @package App\Models
 * @property int $process_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ParserSitemapsQueue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ParserSitemapsQueue newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ParserSitemapsQueue query()
 * @mixin \Eloquent
 */
class ParserSitemapsQueue extends Model
{
	protected $table = 'parser_sitemaps_queue';

	protected $casts = [
		'process_id' => 'int'
	];

	protected $fillable = [
		'sitemap_url',
		'queue_pos'
	];
}
