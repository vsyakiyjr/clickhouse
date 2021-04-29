<?php

namespace App\Models\Cms;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App;
use Illuminate\Support\Str;

/**
 * App\Models\Cms\PagePreview
 *
 * @property integer           $id
 * @property string            $alias
 * @property integer           $parent_directory_id
 * @property string            $title
 * @property boolean           $enabled
 * @property string            $image_path
 * @property \Carbon\Carbon    $created_at
 * @property \Carbon\Carbon    $news_date
 * @property string            $text_preview
 * @property-read Directory    $parent
 * @method static Builder|PagePreview byParentDirectory($parentDirectoryId)
 * @method string getTitle($locale = null)					get translated title
 * @method string getTextPreview($locale = null)			get translated text preview
 * @mixin \Eloquent
 * @property string $parent_directory
 * @property string                                                          $description
 * @property string|null                                                     $keywords
 * @property string|null                                                     $breadcrumbs_title
 * @property string|null                                                     $content
 * @property int                                                             $editable
 * @property string|null                                                     $image_path_original
 * @property int                                                             $priority
 * @property string                                                          $updated_at
 * @property float|null                                                      $sitemap_priority
 * @property string|null                                                     $template_name
 * @property-read mixed                                                      $price
 * @property string $host
 * @property int $force_noindex
 * @property string|null $fullpath
 * @property int $include_to_index
 * @property int|null $parent_page_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cms\PagePreview newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cms\PagePreview newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cms\PagePreview query()
 * @property int $view_count
 */
class PagePreview extends Model {

	public $timestamps = false;

	protected $table = 'cms__pages';

	protected $dates = [
		'created_at',
		'news_date'
	];

	static $fields = [
		'id',
		'alias',
		'parent_directory_id',
		'title',
		'image_path',
		'priority',
		'created_at',
		'enabled',
		'text_preview',
		'news_date',
		'view_count',
	];

	protected $translatableFields = [
		'title',
		'text_preview',
	];

	/**
	 * Pages are located in specific directory
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	function parent() {
		return $this->belongsTo(Directory::class, 'parent_directory_id', 'id');
	}

	function scopeByParentDirectory($query, $parentDirectoryId) {
		return $query->where(['parent_directory_id' => $parentDirectoryId]);
	}

	function firstTextBlock() {
		return $this->getTextPreview();
	}

	/**
	 * Get full path to page
	 * @return string
	 */
	function getPath() {
		// return $this->fullpath;

		$parent = $this->parent->fullpath;
		//Do not add slash between parent and alias if parent is root

		$alias = $this->alias == 'index' ? '' : $this->alias;

		return $parent === "/" ? $parent . $alias : $parent . '/' . $alias;
	}


	/**
	 * Get translation for page field
	 *
	 * @param string      $field
	 * @param string|null $locale
	 *
	 * @return mixed|null
	 */
	public function getFieldTranslation(string $field, $locale = null){
		if(!isset($this->$field)){
			return null;
		}

		return $this->$field;
	}

	/**
	 * Handle dynamic method calls for translatable fields getters.
	 *
	 * @param  string  $method			like 'getTitle', 'getBreadcrumbsTitle'
	 * @param  array  $parameters
	 * @return mixed
	 */
	public function __call($method, $parameters){

		$field = substr(Str::snake($method), 4);

		if(substr($method, 0, 3) == 'get' && in_array($field, $this->translatableFields)){
			return $this->getFieldTranslation($field, $parameters[0] ?? null);
		}

		return parent::__call($method, $parameters);
	}
}
