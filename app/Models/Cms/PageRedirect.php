<?php

namespace App\Models\Cms;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Cms\PageRedirect
 *
 * @property mixed       $alias
 * @property int         $page_id
 * @property string      $match_type
 * @property string|null $created_at
 * @property string|null $comment
 * @property-read Page   $page
 * @method static \Illuminate\Database\Eloquent\Builder|PageRedirect checkAll($string)
 * @method static \Illuminate\Database\Eloquent\Builder|PageRedirect checkEnd($string)
 * @method static \Illuminate\Database\Eloquent\Builder|PageRedirect checkFull($string)
 * @method static \Illuminate\Database\Eloquent\Builder|PageRedirect checkRegex($string)
 * @method static \Illuminate\Database\Eloquent\Builder|PageRedirect checkStart($string)
 * @mixin \Eloquent
 * @property int $id
 * @property \Carbon\Carbon|null $deleted_at
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|PageRedirect onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cms\PageRedirect newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cms\PageRedirect newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cms\PageRedirect query()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Cms\PageRedirect withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Cms\PageRedirect withoutTrashed()
 */
class PageRedirect extends Model {

	use SearchTypesScope;
	use SoftDeletes;

	protected $table = 'cms__pages_aliases';
	public $timestamps = false;

	protected $fillable = [
		'alias',
	    'page_id',
	    'match_type',
		'comment'
	];

	protected $searchColumn = 'alias';

	protected $dates = [
		'created_at',
		'deleted_at'
	];

	public function page(){
		return $this->belongsTo(Page::class);
	}
	
	public function matchTypeDesc() {
		return ;
	}

}
