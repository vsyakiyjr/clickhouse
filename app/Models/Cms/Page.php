<?php

namespace App\Models\Cms;

use App\Cms\Structures\CmsBlock;
use App\Exceptions\Api\IncorrectParameterException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use App;
use DB;
use Auth;
use Illuminate\Support\Str;
use App\Models\Product;

/**
 * Class CmsPage
 *
 * @property integer           $id
 * @property string            $alias
 * @property string            $parent_directory
 * @property integer           $parent_directory_id
 * @property string            $title
 * @property string            $description
 * @property string            $keywords
 * @property string            $breadcrumbs_title
 * @property \SimpleXMLElement $content
 * @property boolean           $enabled
 * @property boolean           $include_to_index
 * @property boolean           $editable        Service pages should not be shown for edit
 * @property string            $image_path
 * @property string            $image_path_original
 * @property integer           $priority
 * @property \Carbon\Carbon    $created_at
 * @property \Carbon\Carbon    $updated_at
 * @property float                             $sitemap_priority
 * @property string                            $template_name
 * @property string                            $text_preview
 * @property-read Directory                    $parent
 * @property-read Collection|PageHistory[]     $history
 * @property-read Collection|PageRedirect[]    $redirects
 * @method static Builder|Page findBySearchQuery($searchQuery)
 * @method string getTitle($locale = null)					get translated title
 * @method string getDescription($locale = null)			get translated description
 * @method string getBreadcrumbsTitle($locale = null)		get translated breadcrumbs title
 * @method string getTextPreview($locale = null)			get translated text preview
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|Page enabled()
 * @method static \Illuminate\Database\Eloquent\Builder|Page findByAlias($alias, $parentDirectory)
 * @property string                                               $host
 * @property bool                                                 $force_noindex
 * @property string|null            $fullpath
 * @property int|null               $parent_page_id
 * @property-read Collection|Page[] $nestedPages
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cms\Page newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cms\Page newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cms\Page query()
 * @property \Illuminate\Support\Carbon|null $news_date
 * @property int $view_count
 * @property-read int|null $history_count
 * @property-read int|null $nested_pages_count
 * @property-read int|null $redirects_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Product[] $products
 * @property-read int|null $products_count
 */
class Page extends Model {
	use ContentAttribute;

	public $timestamps = true;

	protected $table = 'cms__pages';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'alias',
		'parent_directory',
		'parent_directory_id',
		'title',
		'description',
		'keywords',
		'content',
		'host',
		'enabled',
		'image_path',
		'priority',
		'sitemap_priority',
		'breadcrumbs_title',
		'template_name',
		'text_preview',
		'force_noindex',
		'include_to_index',
		'news_date'
	];

	protected $dates = [
		'created_at',
		'updated_at',
	];

	protected $guarded = [];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['editable'];

	/**
	 * Attributes automatic casts
	 *
	 * @var array
	 */
	protected $casts = [
		'enabled'             => 'boolean',
		'editable'            => 'boolean',
		'force_noindex'       => 'boolean',
		'include_to_index'    => 'boolean',
		'priority'            => 'integer',
		'parent_directory_id' => 'integer',
		'news_date'           => 'date',
		'sitemap_priority'    => 'float',
	];

	protected $translatableFields = [
		'title',
		'description',
		'keywords',
		'breadcrumbs_title',
		'content',
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

	/**
	 * History of changes for page
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	function history() {
		return $this->hasMany(PageHistory::class, 'page_id', 'id');
	}

	/**
	 * Aliases for 301 redirect
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	function redirects() {
		return $this->hasMany(PageRedirect::class, 'page_id', 'id');
	}

	function nestedPages(){
		return $this->hasMany(Page::class, 'parent_page_id', 'id');
	}

    /**
     * Relevant products
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function products () {
        return $this->belongsToMany(Product::class, 'cms__page_product', 'page_id', 'product_id')->withPivot('order')->orderBy('cms__page_product.order', 'DESC');
    }

	public function save(array $options = []) {

		$this->sitemap_priority  = $this->calculateSitemapPriority();

		$saveResults = parent::save();

		$this->refresh();
		$this->seedHistory();

		return $saveResults;
	}

	public function getSitemapPriorityAttribute(){
		return $this->calculateSitemapPriority();
	}

	private function calculateSitemapPriority() {
		if (isset($this->attributes['sitemap_priority'])) {
			return (float)$this->attributes['sitemap_priority'];
		}

		switch ($this->parent_directory) {
			case "/": {
				return $this->alias === "index" ? 1 : 0.8;
			}

			case "/news":
			case "/everyday_news": {
				return 0.1;
			}

			case "/aviabilety": {
				return 0.7;
			}

			default: {
				return 0.5;
			}
		}

	}

	/**
	 * Get full nesting details for page. Used for breadcrumbs building
	 * @return array
	 */
	function getNestingChain() {
		$parent = $this->parent;
		$nestingChain = array_reverse($parent->getNestingChain($parent));
		if ($this->alias !== 'index') {
			$nestingChain[] = [
				'name' => $this->getBreadcrumbsTitle() ?? $this->getTitle(),
				'link' => $this->getPath(),
			];
		}

		return $nestingChain;
	}

	/**
	 * Создание записи в истории бронировки из текущего состояния
	 */
	public function seedHistory() {
		$pageHistory = new PageHistory();

		if ($pageHistory->historyEntryExists($this)) {
			return false;
		}

		$pageHistory->revision = hash_hmac('md5', rand() . time() . $this->attributes['id'] . $this->attributes['content'], 'unique');

		foreach ($pageHistory->getFillable() as $attributeName) {
			if ($attributeName == 'page_id') {
				$pageHistory->page_id = $this->attributes['id'];
			} else {
				$pageHistory->attributes[$attributeName] = $this->attributes[$attributeName] ?? null;
			}
		}

		$user = Auth::user();
		$pageHistory->attributes['updated_by_user_id'] = $user ? $user->id : 27012; //alex@arttour.ua

		return $pageHistory->save();
	}

	/**
	 * Get full path to page
	 * @return string
	 */
	function getPath() {
		if($this->fullpath){
			return $this->fullpath;
		}

		$parent = $this->parent->fullpath;
		//Do not add slash between parent and alias if parent is root

		$alias = $this->alias == 'index' ? '' : $this->alias;

		return $parent === "/" ? $parent . $alias : $parent . '/' . $alias;
	}

	/**
	 * Check if page in category
	 *
	 * @param string $path
	 *
	 * @return bool
	 */
	public function isIn($path) {
		return $this->parent->fullpath == $path;
	}

	/**
	 * Check if page in News category
	 *
	 * @return bool
	 */
	public function isInNews() {
		return $this->isIn('/news');
	}

	/**
	 * Get sibling pages
	 *
	 * @param null $direction
	 * @param int  $limit
	 *
	 * @return PagePreview[]|collection
	 * @throws IncorrectParameterException
	 */
	function siblings($direction = null, $limit = 1) {
		if (!$direction) {
			$query = $this->parent->pagePreviews()->with('parent');

			if(!is_null($limit)){
				$query->take(abs($limit));
			}

			return $query->get();
			//return $this->parent->pagePreviews()->with('translations', 'parent')->take(abs($limit))->get();
			//return $this->parent->pages()->enabled();
		}

		if (!in_array($direction, [
			'>',
			'<',
		])
		) {
			throw new IncorrectParameterException('Incorrect direction for siblings');
		}

		return PagePreview::byParentDirectory($this->parent_directory_id)
		           ->where('id', $direction, $this->id)
		           ->where('enabled', '=', 1)
		           ->orderBy('id', $limit < 0 ? 'asc' : 'desc')
		           ->take(abs($limit))
		           ->with('parent')
		           ->get(PagePreview::$fields);
	}

	/**
	 * Returns text contents of first text block
	 * @return string
	 */
	function firstTextBlock() {
		$content = $this->getContent();

		if (is_null($content) || empty($content)) {
			return '';
		}

		foreach ($content['block'] as $block) {
			/** @var CmsBlock $block */
			if ($block->type == 'text') {
				return $block->content;
			}
		}

		return '';
	}

	/**
	 * @param string[] $includeTypes
	 * @param string[] $excludeTypes
	 *
	 * @param null     $forcePosition
	 *
	 * @return CmsBlock[]
	 */
	public function getVisibleBlocks($includeTypes = null, $excludeTypes = null, $forcePosition = null) {
		$blocks = [];

		if (!is_array($includeTypes)) {
			$includeTypes = [];
		}

		if (!is_array($excludeTypes)) {
			$excludeTypes = [];
		}

		$content = $this->getContent();

		if (empty($content)) {
			return $blocks;
		}

		foreach ($content['block'] as $block) {
			/** @var CmsBlock $block */

			if (!filter_var($block->visible, FILTER_VALIDATE_BOOLEAN)){
				// Skip NOT visible blocks
				continue;
			}

			if (!empty($excludeTypes) && in_array($block->type, $excludeTypes)){
				// Skip excluded blocks
				continue;
			}

			if (!empty($includeTypes) && !in_array($block->type, $includeTypes)){
				// Skip NOT included blocks
				continue;
			}

			if(!empty($forcePosition) && $forcePosition != $block->position){

				continue;
			}

			$blocks[] = $block;
		}

		return $blocks;
	}

	/**
	 * @param string[] $onlyTypes
	 *
	 * @param null     $position
	 *
	 * @return CmsBlock[]
	 */
	public function getVisibleBlocksOnly($onlyTypes = null, $position = null) {

		if (!empty($onlyTypes) && !is_array($onlyTypes)) {
			$onlyTypes = func_get_args();
		}

		return $this->getVisibleBlocks($onlyTypes, null, $position);
	}

	/**
	 * Create empty index page for directory
	 *
	 * @param Directory $directory
	 *
	 * @return Page
	 */
	public static function createIndexForDirectory(Directory $directory) {
		$page = new static();
		$page->alias = 'index';
		$page->title = $directory->description;
		$page->description = $directory->description;
		$page->parent_directory = $directory->fullpath;
		$page->parent_directory_id = $directory->id;
		$page->save();

		return $page->fresh();
	}

	/**
	 * Find page by path, e.g /path/to/page
	 *
	 * @param string $requestedPath
	 *
	 * @return null|Page|string
	 */
	static function findByPath($requestedPath) {
		// direct access to page
		$path = $requestedPath[0] === '/' ? $requestedPath : "/$requestedPath";
		$pathInfo = pathinfo($path);
		$dirName = str_replace(DIRECTORY_SEPARATOR, '/', $pathInfo['dirname']);

		$host = getHostForCms();

		// incorrect relative paths somewhere
		if($dirName == '/' . $host){
			return redirect()->away(str_replace_first("$host", '', $requestedPath), 301);
		}

		/** @var Directory $dir */
		$dir = Directory::where(["fullpath" => $dirName, 'host' => $host])->first();

		/** @var Page $page */
		$page = Page::findByAlias($pathInfo['basename'], $dirName)->where(['cms__pages.host' => $host])->first();

		if ($page && $dir) {
			// for transparent directories pages should be forced to upper nest level
			if ($dir->transparent) {
				return redirect( )->away($page->getPath(), 301);
			}

			return $page;
		}

		// maybe accessed index page for directory
		$requestedDir = Directory::where(['fullpath' => $path, 'host' => $host])->first();
		if ($requestedDir) {
			if ($requestedDir->fullpath === '/everyday_news') {
				return $requestedDir->pages()
				                    ->whereMonth('created_at', date('m'))
				                    ->whereDay(  'created_at', date('d'))->first();
			}
			return $requestedDir;
		}

		// try to find page by alias
		/** @var PageRedirect $page */
		$pageAlias = PageRedirect::checkAll($path)->first();

		if ($pageAlias && $pageAlias->page) {
			return redirect()->away($pageAlias->page->getPath(), 301);
		}
		//nothing's found
		return null;
	}

	/**
	 * @param Builder $query
	 *
	 * @return mixed
	 */
	function scopeEnabled($query) {
		return $query->where(['enabled' => 1]);
	}

	/**
	 * @param Builder $query
	 * @param $alias
	 * @param $parentDirectory
	 *
	 * @return mixed
	 */
	function scopeFindByAlias($query, $alias, $parentDirectory) {
		return $query
			->select('cms__pages.*')
			->join('cms__directories', 'cms__directories.id', '=', 'cms__pages.parent_directory_id')
			->where(function($query) use ($alias, $parentDirectory){
				/** @var Builder $query */
				$query->where([
					'cms__pages.enabled'			=> 1,
					'cms__pages.alias'				=> $alias,
					'cms__pages.parent_directory'	=> $parentDirectory,
				]);
			})->orWhere(function($query) use ($alias){
				/** @var Builder $query */
				$query->where([
					'cms__pages.enabled'			=> 1,
					'cms__pages.alias'				=> $alias,
					'cms__directories.transparent' 	=> 1,
				]);
			});
	}

	/**
	 * Find page by search query
	 *
	 * @param \Illuminate\Database\Eloquent\Builder $query
	 * @param                                       $searchQuery
	 *
	 * @return \Illuminate\Database\Eloquent\Builder
	 */
	function scopeFindBySearchQuery($query, $searchQuery) {
		$preparedSearchQuery = parse_url($searchQuery)['path'] ?? $searchQuery;
		$preparedSearchQuery = trim($preparedSearchQuery, '/');

		$searchQueryQuoted = DB::connection()->getPdo()->quote("%$preparedSearchQuery%");

		return $query->where('id', '=', $searchQuery)
					 ->orWhere('alias', 'LIKE', "%$preparedSearchQuery%")
					 ->orWhere('title', 'LIKE', "%$preparedSearchQuery%")
					 ->orWhere('description', 'LIKE', "%$preparedSearchQuery%")
					 ->orWhere('breadcrumbs_title', 'LIKE', "%$preparedSearchQuery%")
		             ->orWhere('text_preview', 'LIKE', "%$preparedSearchQuery%")
		             ->orWhereRaw("extractvalue(content, '/content/block[type=\"text\"]/title') like $searchQueryQuoted")
		             ->orWhereRaw("extractvalue(content, '/content/block[type=\"text\"]/content') like $searchQueryQuoted");
	}

	/**
	 * Store and set image
	 *
	 * @param string $image
	 */
	public function setImage($image) {
		$this->image_path = storeImage($image, $this->alias, $this->parent_directory);
		$this->image_path_original = storeImage($image, $this->alias . '-o', $this->parent_directory, 1200);
	}

	/**
	 * Remove image
	 *
	 */
	public function removeImage() {
		$this->image_path = null;
		$this->image_path_original = null;
	}

	static function stripGetParams($path){
		if ((!empty(request()->query()) && !request()->query('page')) || request()->query('page') == 1) {
			//strip get params to prevent unwanted pages to appear
			return redirect($path, 301);
		}
		return false;
	}

	static function hasAnalyticsParams(){
		$analyticsParams = [
			'utm_source',
			'utm_medium',
			'utm_campaign',
			'utm_term',
			'utm_content',
			'gclid',
			'yclid',
			'ref',
			'_openstat',
			'page'
		];

		foreach ($analyticsParams as $param) {
			if (request()->query($param)) {
				return true;
			}
		}

		return false;
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
		// todo: Make for Page and Directory 'Pageable interface' and implement common methods so Directory can be rendered like page (getDescription, getKeywords... methods)
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


	/**
	 * Get translated content
	 *
	 * @param string|null $locale
	 *
	 * @return mixed|null|\SimpleXMLElement
	 */
	public function getContent($locale = null){

		$content = $this->getFieldTranslation('content', $locale);

		if(empty($content) || empty($content['block'])){
			return $this->content;
		}

		return $content;
	}
	/**
	 * Get translated keywords
	 *
	 * @param string|null $locale
	 *
	 * @return string|null
	 */
	public function getKeywords($locale = null){

		return $this->getFieldTranslation('keywords', $locale);
	}
}
