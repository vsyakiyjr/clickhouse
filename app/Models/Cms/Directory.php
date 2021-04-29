<?php

namespace App\Models\Cms;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use App;

/**
 * Class CmsDirectory
 *
 * @property integer        $id
 * @property string         $fullpath
 * @property string         $parent_directory
 * @property string         $description
 * @property string         $description_uk
 * @property string         $description_en
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property boolean        $transparent
 * @property boolean        $special
 * @property boolean        $show_siblings
 * @property Collection|Page[]        $pages
 * @property-read Collection|PagePreview[] $page_previews
 * @property-read Collection|Directory[]   $directories
 * @property-read Directory                $parent
 * @method static Builder|Directory whereId($value)
 * @method static Builder|Directory whereFullPath($value)
 * @method static Builder|Directory whereParentDirectory($value)
 * @method static Builder|Directory whereDescription($value)
 * @method static Builder|Directory whereCreatedAt($value)
 * @method static Builder|Directory whereUpdatedAt($value)
 * @method static Builder|Directory findBySearchQuery($searchQuery, $host)
 * @mixin \Eloquent
 * @property string $host
 * @property-read mixed $full_path
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Cms\PagePreview[] $pagePreviews
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cms\Directory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cms\Directory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cms\Directory query()
 * @property-read int|null $directories_count
 * @property-read int|null $page_previews_count
 * @property-read int|null $pages_count
 * @property-read mixed $collapsed
 */
class Directory extends Model {
	public $timestamps = true;

	protected $table = 'cms__directories';

	protected $fillable = [
		'fullpath',
		'parent_directory',
		'description',
		'description_uk',
		'description_en',
	];

	protected $appends = [
		'collapsed'
	];

	protected $guarded = [];

	public function getFullPathAttribute(){
		return $this->transparent ? '' : ($this->attributes['fullpath'] ?? '');
	}

	// for cms tree
	public function getCollapsedAttribute(){
		return false;
	}

	/**
	 * Directory may have many pages in it
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function pages() {
		return $this->hasMany(Page::class, 'parent_directory_id', 'id')
					->whereNull('parent_page_id')
//					->where('created_at' ,'>=', Carbon::now()->subMonth(2)->startOfMonth())
					->orderBy('created_at', 'desc')
//		            ->orderBy('priority', 'asc')
			;
	}

	/**
	 * Load page previews instead of whole pages to show widgets on main page and for pagination
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function pagePreviews() {

		return $this->hasMany(PagePreview::class, 'parent_directory_id', 'id')
					->select(PagePreview::$fields)
					->whereNull('parent_page_id')
					->where('enabled', 1)
//					->orderBy('priority', 'asc')
					->orderBy('news_date', 'desc')
					->orderBy('created_at', 'desc');
	}

	/**
	 * Directory may include other directories
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function directories() {
		return $this->hasMany(Directory::class, 'parent_directory', 'fullpath')
					->where(['host' => $this->host])
		            ->orderBy('special', 'desc');
	}

	/**
	 * Directory has one parent directory
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function parent() {
		return $this->hasOne(Directory::class, 'fullpath', 'parent_directory')
		            ->where(['host' => $this->host]);
	}

	/**
	 * Get gesting details for directory. Used for breadcrumbs building
	 *
	 * @param Directory $directory
	 * @param array $chain
	 *
	 * @return array
	 */
	function getNestingChain(Directory $directory, $chain = []){
		$chain[] = ['name' => $directory->getDescription(),'link'=> $directory->fullpath];

		if ($directory->parent_directory === 'System'){
			return $chain;
		}

		return $this->getNestingChain($directory->parent, $chain);
	}

	/**
	 * Find directory by full path
	 *
	 * @param \Illuminate\Database\Eloquent\Builder $query
	 * @param                                       $searchQuery
	 *
	 * @param                                       $host
	 *
	 * @return \Illuminate\Database\Eloquent\Builder|static
	 */
	function scopeFindBySearchQuery($query, $searchQuery, $host = null) {
		$queryTrans = convertLayout($searchQuery);

		$host = $host ?? getHostForCms();

		return $query
			->where('fullpath', '!=', '/')
			->where('host', '=', $host)
			->where(function ($q) use($searchQuery, $queryTrans) {
				$q->where(  'fullpath',    'LIKE', "%$searchQuery%")
				  ->orWhere('description', 'LIKE', "%$searchQuery%")
				  ->orWhere('description', 'LIKE', "%$queryTrans%")
				;
			});
	}


	function getDescription ($lang = null){
		if(empty($lang) || !is_string($lang)){
			$lang = App::getLocale();
		}

		$fallbackDescription = $this->attributes["description_".config('app.fallback_locale')] ?? $this->attributes["description"];

		return $this->attributes["description_$lang"] ?? $fallbackDescription;
	}

	function getTitle($lang = null){
		return $this->getDescription($lang);
	}

	function getKeywords($lang = null){
		return '';
	}
}