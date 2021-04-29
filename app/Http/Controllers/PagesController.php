<?php

namespace App\Http\Controllers;

use App\Models\Cms\Page;
use App\Models\Pages;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;

class PagesController extends Controller {
	/* public
	function __construct()
	{
	$this -> middleware('basic');
	}*/
	/*для вывода типичных страниц*/
	public function page($alias) {
		$page = Pages::where('alias', $alias)->with('blocks')->first();
		$seoParams = $this->getSeoParamsForPage($alias);

		$pageTitle = $seoParams['pageTitle'] ?: $page->seo_title;
		$pageDescription = $seoParams['pageDescription'] ?: $page->seo_description;
		$pageKeywords = $seoParams['pageKeywords'] ?: $page->seo_keywords;

		return view('info.page', [
			'page'            => $page,
			'pageTitle'       => $pageTitle,
			'pageDescription' => $pageDescription,
			'pageKeywords'    => $pageKeywords,
		]);
	}

	public function index(Request $request) {
		$catalog = Product::whereNotIn('family_price', [''])
		                  ->orWhere('new', 1)
		                  ->groupBy('mod_group')
		                  ->get();

		return view('index', ['catalog' => $catalog]);
	}

	public function contacts() {

		$seoParams = $this->getSeoParamsForPage('contacts');

		return view('info.contacts', [
			'pageTitle'       => $seoParams['pageTitle'],
			'pageDescription' => $seoParams['pageDescription'],
			'pageKeywords'    => $seoParams['pageKeywords'],
		]);
	}

	public function deliverance() {
		$page = Pages::where('alias', 'deliverance')->with('blocks')->first();
		$cities = Setting::where('key', 'cities')->first();

		$seoParams = $this->getSeoParamsForPage('deliverance');

		$pageTitle = $seoParams['pageTitle'] ?: $page->seo_title;
		$pageDescription = $seoParams['pageDescription'] ?: $page->seo_description;
		$pageKeywords = $seoParams['pageKeywords'] ?: $page->seo_keywords;

		if ($cities) {
			$cities = json_decode($cities->value, true);
		} else {
			$cities = [];
		}

		return view('info.deliverance', [
			'page'            => $page,
			'cities'          => $cities,
			'pageTitle'       => $pageTitle,
			'pageDescription' => $pageDescription,
			'pageKeywords'    => $pageKeywords,
		]);
	}

	public function message(Request $request) {
		return back()->with('message', 'success');
	}

	public function info() {
		$page = Pages::where('alias', 'deliverance')->get()->first();
		$seoParams = $this->getSeoParamsForPage('deliverance');

		$pageTitle = $seoParams['pageTitle'] ?: $page->seo_title;
		$pageDescription = $seoParams['pageDescription'] ?: $page->seo_description;
		$pageKeywords = $seoParams['pageKeywords'] ?: $page->seo_keywords;

		return view('info.page', [
			'page' => $page,
			'pageTitle'       => $pageTitle,
			'pageDescription' => $pageDescription,
			'pageKeywords'    => $pageKeywords,
		]);
	}

	protected function getSeoParamsForPage($alias){
		$pageTitle = '';
		$pageDescription = '';
		$pageKeywords = '';

		/** @var Page $cmsPage */
		$cmsPage = Page::where(['alias' => $alias])->first();

		if($cmsPage) {
			$pageTitle = $cmsPage->title;
			$pageDescription = $cmsPage->description;
			$pageKeywords = $cmsPage->keywords;
		}

		return compact('pageTitle', 'pageDescription', 'pageKeywords');
	}
}
