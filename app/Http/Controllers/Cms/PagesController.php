<?php

namespace App\Http\Controllers\Cms;

use App\Cms\Structures\CmsBlock;
use App\Exceptions\Cms\ExistingPageException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\IsAvailableRequest;
use App\Http\Requests\Cms\PageRequest;
use App\Http\Requests\FindCmsObjectRequest;
use App\Models\Cms\Directory;
use App\Models\Cms\Page;
use App\Models\Cms\PageRedirect;
use App\Models\Cms\PageView;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Models\Product;

/**
 * Class PagesController
 *
 * @package App\Http\Controllers\Cms
 */
class PagesController extends Controller {
	/**
	 * Save page content
	 *
	 * @param PageRequest $request
	 *
	 * @return Page
	 */
	protected $loadRelations = [
        'products',
		//		'history',
		//		'history.updatedBy',
	];

	public function store(PageRequest $request) {
		/** @var Page $page */
		$page = Page::create($request->all());
		if ($request->image) {
			$page->setImage($request->image);

			//			$page->image_path = storeImage($request->image, $page->alias, $page->parent_directory);
			//			$page->image_path_original = storeImage($request->image, $page->alias.'-o', $page->parent_directory,  1200);
		};

		$page->enabled = false;
		$page->save();
		$page->host = $request->get('host');

		return $page;
	}

	/**
	 * Get page by id
	 *
	 * @return Page
	 */
	public function show($pageId) {
		$page = Page::find($pageId);

		return $page->load($this->loadRelations);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param             $pageId
	 * @param PageRequest $request
	 *
	 * @return Page
	 * @throws ExistingPageException
	 */
	public function update($pageId, PageRequest $request) {
		$newAlias = $request->get('alias');
		$existingPage = Page::where([
			'alias'            => $newAlias,
			'parent_directory' => $request->get('parent_directory'),
			'host'             => $request->get('host'),
		])->where('id', '!=', $pageId)
		                    ->first();

		if ($existingPage) {
			throw new ExistingPageException("Страница с таким алиасом уже существует! ID: $existingPage->id");
		}

		/** @var Page $page */
		$page = Page::find($pageId);

		$originalAlias = $page->getOriginal('alias');
		if ($originalAlias !== $newAlias) {
			PageRedirect::create([
				'alias'      => "/$originalAlias",
				'page_id'    => $pageId,
				'match_type' => 'full',
			]);

			PageRedirect::where('alias', '=', "/$newAlias")->update([
				'deleted_at' => Carbon::now(),
				'comment'    => 'deleted to prevent endless looped redirect',
			]);
		}

		$data = $request->all();

		if (!empty($data['news_date'])) {
			$data['news_date'] = Carbon::parse($data['news_date'])->format('Y-m-d');
		}

		$page->update($data);

		if (!empty($request->image)) {
			if ($request->image == 'remove') {
				//				$page->image_path = null;
				//				$page->image_path_original = null;

				$page->removeImage();
			} else {
				$page->setImage($request->image);
				//$page->image_path = storeImage($request->image, $page->alias);
			}
		}

		$page->keywords = $page->keywords ?? $page->getKeywords();
		$page->save();

        if (isset($request->products)) {
            $exsitingIds = [];
            $orders = [];
            foreach ($request->products as $product) {
                if ($product['id'] && ($product['pivot']['order'] || $product['order'])) {
                    $exsitingIds[] = (int)$product['id'];
                    $orders[(int)$product['id']] = (int)($product['pivot']['order'] ?? $product['order']);
                }
            }
            foreach ($page->products as $product) {
                $page->products()->detach($product->id);
            }
            $products = $page->products()->get();
            foreach (Product::whereIn('id', $exsitingIds)->get() as $product) {
                if (!$products->contains($product)) {
                    $page->products()->save($product, ['order' => $orders[$product->id]]);
                }
            }
        }
		$this->buildSitemap();

		return $page->fresh()->load($this->loadRelations);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param $pageId
	 *
	 * @return Page
	 *
	 * @throws \Exception
	 */
	public function destroy($pageId) {
		$page = Page::find($pageId);
		$page->delete();
		$this->buildSitemap();

		return $page;
	}

	/**
	 * Get available and enabled page by path or throw 404
	 *
	 * @param $path
	 *
	 * @return Page|RedirectResponse|View|void
	 */
	public function renderPage($path) {
		$domain = request()->getHost();
		$showNoIndexNoFollowMeta = false;

		$decodedPath = trim(urldecode($path));
		if ($path !== $decodedPath) {
			return redirect($decodedPath, 301);
		}

		if ($path === 'index') {
			return redirect('/', 301);
		}

		if (preg_match('/[A-Z]/', $path) && !Page::hasAnalyticsParams()) {
			//force all aliases to be lowercase
			return redirect(strtolower($path), 301);
		}

		$page = $this->getPageByPath($path);

		if (!$page) {
			return abort('404');
		}

		if ($page instanceof RedirectResponse) {
			return $page;
		}

		if ($page instanceof Directory) {
			$contentBlock = CmsBlock::create('DirectoryLinks', [
				'visible' => true,
				'config'  => json_encode([
					'directory_id' => $page->id,
					'columns'      => 1,
					'paginated'    => true,
				]),
			]);

			$showNoIndexMeta = !is_null(request()->query('page')) && request()->query('page') != 1;

			return view('cms.renderedDirectoryPage', [
				'page'                    => $page,
				'showNoIndexMeta'         => $showNoIndexMeta,
				'showNoIndexNoFollowMeta' => $showNoIndexNoFollowMeta,
				'directory'               => $page,
				'contentBlock'            => $contentBlock,
			]);
		} else {

            $page->load('products');
			$ip = $_SERVER['HTTP_X_REAL_IP'] ?? '';

			$existingPageView = PageView::where([
				'page_id' => $page->id,
				'visitor_ip' => $ip
			])->first();

			if(!$existingPageView){
				$page->view_count++;
				$page->save();

				PageView::create([
					'page_id' => $page->id,
					'visitor_ip' => $ip
				]);
			}
		}

		$dataForView = compact('page');
		$templateName = 'cms.renderedPage';

		$dataForView['showNoIndexNoFollowMeta'] = $showNoIndexNoFollowMeta;

		return view($templateName, $dataForView);
	}

	/**
	 * @param IsAvailableRequest $request
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function aliasIsAvailable(IsAvailableRequest $request) {
		$page = $this->getPageByPath($request->path);

		return response()->json(['available' => (bool) $page]);
	}

	/**
	 * Page search autocomplete
	 *
	 * @param FindCmsObjectRequest $request
	 *
	 * @return mixed
	 */
	public function findPage(FindCmsObjectRequest $request) {
		return [
			'data' => Page::findBySearchQuery($request->get('query'))->take(10)->get(),
		];
	}

	/**
	 * Rebuild sitemap.xml. Separate sitemap for landings, split others by 500 per file.
	 */
	public function buildSitemap() {
		$pagesList = Page::with(['parent'])->where('enabled', '=', 1)->get([
			'id',
			'alias',
			'parent_directory',
			'parent_directory_id',
			'enabled',
			'include_to_index',
			'sitemap_priority',
			'updated_at',
		])->chunk(500);

		$mainSitemap = new \DOMDocument('1.0', 'UTF-8');
		$mainSitemap->preserveWhiteSpace = true;
		$mainSitemap->formatOutput = true;
		$sitemapIndex = $mainSitemap->createElement('sitemapindex');
		$mainSitemap->appendChild($sitemapIndex);

		$sitemapIndex->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

		foreach ($pagesList as $index => $chunk) {

			$fileName = "sitemap-$index.xml";

			$this->buildSitemapFile($fileName, $chunk);

			// create entry in parent sitemap
			$sitemapElement = $mainSitemap->createElement('sitemap');
			$sitemapElement->appendChild($mainSitemap->createElement('loc', url($fileName)));
			$sitemapElement->appendChild($mainSitemap->createElement('lastmod', Carbon::now()->format('c')));
			$sitemapIndex->appendChild($sitemapElement);
		}

		$mainSitemap->save('sitemap.xml');
	}

	/**
	 * Build single sitemap file from given pages
	 *
	 * @param $fileName
	 * @param $pages
	 */
	protected function buildSitemapFile($fileName, $pages) {
		$dom = new \DOMDocument('1.0', 'UTF-8');
		$dom->preserveWhiteSpace = true;
		$dom->formatOutput = true;

		$urlset = $dom->createElement('urlset');
		$dom->appendChild($urlset);

		$urlset->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
		$urlset->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
		$urlset->setAttribute('xsi:schemaLocation', 'http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd');

		foreach ($pages as $page) {
			/** @var Page $page */
			if (!$page->include_to_index || !$page->enabled) {
				continue;
			}

			if ($page->parent_directory === "/" || $page->parent->transparent) {
				$page->parent_directory = '';
			}

			$link = $page->alias === "index" ? $page->parent_directory : $page->parent_directory . '/' . $page->alias;
			$link = "https://{$_SERVER['HTTP_HOST']}$link";

			$modifyed = $page->updated_at->format('c');

			$url = $dom->createElement('url');
			$url->appendChild($dom->createElement('loc', $link));
			$url->appendChild($dom->createElement('lastmod', $modifyed));
			$url->appendChild($dom->createElement('priority', $page->sitemap_priority));
			$url->appendChild($dom->createElement('changefreq', 'daily'));

			$urlset->appendChild($url);
		}

		$dom->save($fileName);
	}

	/**
	 * Check page availability by full path
	 *
	 * @param string $path
	 *
	 * @return Page
	 */
	private function getPageByPath($path) {
		return Page::findByPath($path);
	}
}
