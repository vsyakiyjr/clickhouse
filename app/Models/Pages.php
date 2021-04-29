<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PageBlock;

/**
 * App\Models\Pages
 *
 * @property int         $id
 * @property string      $alias
 * @property string|null $title
 * @property string|null $description
 * @property string|null $text
 * @property int         $visible
 * @property string|null $head
 * @property string|null $seo_title
 * @property string|null $seo_description
 * @property string|null $seo_keywords
 * @property-read mixed  $errors
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pages newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pages newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pages query()
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PageBlock[] $blocks
 * @property-read int|null $blocks_count
 */
class Pages extends Model {
	public $timestamps = false;

	protected $table = 'pages';

	protected $appends = ['errors'];

    public function blocks () {
        return $this->hasMany(PageBlock::class, 'page_id');
    }

	public function getErrorsAttribute($value) {
		return false;
	}
}
