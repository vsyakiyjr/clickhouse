<?php

namespace App\Models\Cms;

use App\Cms\Structures\CmsBlock;
use Illuminate\Database\Eloquent\Model;
use App\Models\Cms\Page;
use App\Models\User;

/**
 * App\Models\Cms\PageHistory
 *
 * @property string $revision
 * @property integer                     $page_id
 * @property string                      $alias
 * @property string                      $parent_directory
 * @property integer                     $parent_directory_id
 * @property string                      $title
 * @property string                      $description
 * @property string                      $keywords
 * @property string                      $breadcrumbs_title
 * @property string                      $content
 * @property boolean                     $enabled
 * @property string                      $image_path
 * @property boolean                     $priority
 * @property string                      $updated_at
 * @property integer                     $updated_by_user_id
 * @property float                       $sitemap_priority
 * @property-read Page                   $page
 * @property-read \App\Models\User $updatedBy
 * @mixin \Eloquent
 * @property string $host
 * @property int $force_noindex
 * @property bool $include_to_index
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cms\PageHistory lastHistoryEntry($pageId)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cms\PageHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cms\PageHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cms\PageHistory query()
 */
class PageHistory extends Model {
	public $timestamps = false;

	public $table = 'cms__pages_history';

	protected $casts = [
		'enabled'          => 'boolean',
		'editable'         => 'boolean',
		'priority'         => 'integer',
		'sitemap_priority' => 'float',
	];

	protected $fillable = [
		'updated_by_user_id',
		'page_id',
		'alias',
		'parent_directory',
		'parent_directory_id',
		'title',
		'description',
		'content',
		'enabled',
		'image_path',
		'priority',
		'sitemap_priority',
		'breadcrumbs_title',
		'force_noindex'
	];

	/**
	 * For these columns changes are tracked
	 * @var array
	 */
	protected $historyColumns = [
		'alias',
		'title',
		'description',
		'keywords',
		'breadcrumbs_title',
		'content',
		'enabled',
		'image_path',
		'priority',
		'sitemap_priority',
		'force_noindex',
	];

	function page() {
		return $this->belongsTo(Page::class, 'page_id', 'id');
	}

	function updatedBy() {
		return $this->hasOne(User::class, 'id', 'updated_by_user_id');
	}

	/**
	 * Convert content value to SimpleXMLObject
	 *
	 * @param $content
	 *
	 * @return mixed
	 */
	function getContentAttribute($content) {
		try {
			$content = new \SimpleXMLElement($content, LIBXML_NOCDATA);
		} catch (\Exception $e) {
			$content = new \stdClass();
			$content->block = [];
		}

		$content = json_decode(json_encode($content), true);
		if (!empty($content['block'])) {
			$content['block'] = isset($content['block'][0]) ? $content['block'] : [$content['block']];
		} else {
			$content['block'] = [];
		}
		$cmsBlockCollection = [];

		foreach ($content['block'] as $block) {
			$cmsBlockCollection[] = CmsBlock::create($block['type'], $block);
		}

		$content['block'] = $cmsBlockCollection;

		return $content;
	}

	function historyEntryExists(Page $page) {

		/** @var PageHistory $lastHistoryEntry */
		$lastHistoryEntry = PageHistory::lastHistoryEntry($page->id)->first();

		if (is_null($lastHistoryEntry)) {
			return false;
		}

		$allFieldsAreTheSame = true;
		
		foreach ($this->historyColumns as $column) {
			$allFieldsAreTheSame = $lastHistoryEntry->getOriginal($column) == $page->getOriginal($column);
			if (!$allFieldsAreTheSame) {
				break;
			}
		}
		
		return $allFieldsAreTheSame;
	}

	/**
	 * @param \Illuminate\Database\Eloquent\Builder $query
	 */
	function scopeLastHistoryEntry($query, $pageId){
		return $query->where(['page_id' => $pageId])->orderBy('updated_at', 'desc')->get();
	}
}
