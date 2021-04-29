<?php
namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\CreateDirectoryRequest;
use App\Http\Requests\Cms\IsAvailableRequest;
use App\Http\Requests\Cms\SearchWithinTreeRequest;
use App\Http\Requests\Cms\UpdateDirectoryRequest;
use App\Http\Requests\FindCmsObjectRequest;
use App\Models\Cms\Directory;
use App\Models\Cms\Page;
use Illuminate\Http\Request;

class DirectoriesController extends Controller {
	protected $litePageAttributesList = ['id', 'alias', 'title', 'fullpath', 'enabled', 'include_to_index', 'parent_page_id', 'view_count'];

	function rootDir(Request $request){
		$host = $request->get('host', getHostForCms());

		$rootDir = Directory::where(['host' => $host, 'fullpath' => '/'])
		                    ->get(['id'])
		                    ->first();

		return $rootDir;
	}

	/**
	 * Get site tree from given directory
	 *
	 * @param Directory $dir
	 *
	 * @return Directory
	 */
	function getTree(Directory $dir) {
		$dir->pages()->get($this->litePageAttributesList);

		foreach ($dir->pages as &$page){
			$page->nestedPages()->get($this->litePageAttributesList);
		}
		unset($page);

		foreach ($dir->directories as &$childDir) {
			$childDir->pages()->get($this->litePageAttributesList);

			foreach ($childDir->pages as &$page){
				$page->nestedPages()->get($this->litePageAttributesList);
			}
			unset($page);

			$childDir = $this->getTree($childDir);
		}

		return $dir;
	}

	function searchWithinTree(SearchWithinTreeRequest $request) {
		$searchResult = [
			'directories' => Directory::findBySearchQuery($request->search_query)->get(),
			'pages'       => Page::findBySearchQuery($request->search_query)->get($this->litePageAttributesList),
		];

		return $searchResult;
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param CreateDirectoryRequest $request
	 *
	 * @return Directory
	 */
	public function store(CreateDirectoryRequest $request) {
		$directory = Directory::create($request->all());

		Page::createIndexForDirectory($directory);

		return $directory;
	}

	/**
	 * Get directory contents
	 *
	 *
	 * @param $directoryId
	 *
	 * @return Directory
	 */
	public function show($directoryId) {
		$directory = Directory::find($directoryId);

		$directory->directories;

		if($directory->full_path != '/news') {
			foreach ($directory->pages as &$page) {
				$page->nestedPages;
			}
			unset($page);

			$sortedPages = $directory->pages->sortByDesc(function ($page) {
				return $page->nestedPages ? $page->nestedPages->count() : null;
			})->values()->all();


			$directory = $directory->toArray();

			$directory['pages'] = $sortedPages;
		}

		return $directory;
	}

	/**
	 * Update the specified resource in storage.
	 *
	* @param $directoryId
	* @param UpdateDirectoryRequest $request
	*
	* @return Directory
	*/
	public function update($directoryId, UpdateDirectoryRequest $request) {
		$directory = Directory::find($directoryId);
		$directory->update($request->all());

		return $directory;
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param $directoryId
	 *
	 * @return void
	 * @throws \Exception
	 */
	public function destroy($directoryId) {
		$directory = Directory::find($directoryId);

		$directory->delete();
	}

	/**
	 * Check is directory exists
	 *
	 * @param IsAvailableRequest $request
	 *
	 * @return array
	 */
	function aliasIsAvailable(IsAvailableRequest $request) {
		$dir = $this->getDirByPath($request->path);

		return ['available' => (bool) $dir];
	}

	/**
	 * Directory search autocomplete
	 *
	 * @param FindCmsObjectRequest $request
	 *
	 * @return mixed
	 */
	function findDirectory(FindCmsObjectRequest $request) {
		return [
			'data' => Directory::findBySearchQuery($request->get('query'), $request->get('host'))->take(10)->get(),
		];
	}

	/**
	 * Check page availability by full path
	 *
	 * @param string $path
	 *
	 * @return bool
	 */
	private function getDirByPath($path) : bool {
		$dir = Directory::where([
			"fullpath" => $path,
			"host" => getHostForCms()
		])->first();

		return (bool)$dir;
	}
}
