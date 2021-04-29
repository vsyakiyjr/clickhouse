<?php

namespace App\Http\Controllers;

use App\Helpers\Calendar;
use App\Mail\CallbackRequestMail;
use App\Mail\OrderRequestMail;
use App\Models\Category;
use App\Models\Cms\Page;
use App\Models\MainPagePlus;
use App\Models\MainPageSlide;
use App\Models\Product;
use App\Models\Setting;
use App\Models\ShowProduct;
use App\Models\Subcategory;
use App\Services\ExchangeService;
use App\Services\ParserService;
use App\StringNormalize;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Mail;
use function foo\func;

class CatalogController {

	/**
	 * Главная страница
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function home(){
		$nearestDeliveryDay = Calendar::nextDelivery();

		$nearestDeliveryDayLocale = $nearestDeliveryDay;

		$nearestDeliveryDateObject = Calendar::nextDelivery(false, true);

		$page = Page::find(['alias' => 'index', 'parent_directory' => '/'])->first();

		if($page){
			$pageTitle = $page->title;
			$pageDescription = str_replace(':nearestDeliveryDay', $nearestDeliveryDay, $page->description);
			$pageKeywords = $page->keywords;
		} else {
			$pageTitle = 'Доставка ИКЕА в Минске и РБ. Полный каталог товаров и мебели IKEA';
			$pageDescription = "Ближайшая поставка $nearestDeliveryDay. Комиссия от 6.5%. Без предоплаты. Работаем по всей Беларуси. Предоставляем услуги сборки и подъема на этаж.";
			$pageKeywords = 'купить ИКЕА, доставка икеа минск, икеа, ikea, икеа сайт, ikea by';
		}

		$catalog = Product::with(['categories'])
		                  ->leftJoin('show_products', 'show_products.vendor_code', '=', 'products.vendor_code')
		                  ->where('show_products.place', 'main')
		                  ->orderByRaw('isnull(show_products.show_order)')
		                  ->orderBy('priority', 'desc')
		                  ->orderBy('name')
		                  ->select('products.*')
		                  ->distinct()
		                  ->paginate(50);

		$categories = Category::with('subcategories')->where('visible', 1)->get();

		// todo grab from admin
		$pluses = array_column( MainPagePlus::orderBy('position')->get()->toArray(), 'plus');

		if(empty($pluses)){
			$pluses = [
				'Фиксированная стоимость доставки в регионы 19 BYN',
				'Профессиональная сборка  и установка',
				'При заказе свыше 250 BYN доставка по Минску бесплатная',
				'Нам доверяют более 50000  человек'
			];
		}

		$slidesHorizontal = array_column(MainPageSlide::where('type', '=', 'desktop')->orderBy('position')->get(['slide_path'])->toArray(), 'slide_path');

		if(empty($slidesHorizontal)){
			$slidesHorizontal = [
				'/slides/ikea-slide.png'
			];
		}

		$slidesVertical =  array_column(MainPageSlide::where('type', '=', 'mobile')->orderBy('position')->get(['slide_path'])->toArray(), 'slide_path');

		if(empty($slidesVertical)){
			$slidesVertical = [
				'/slides/ikea-slide-vertical.png'
			];
		}

		$exchange = new ExchangeService(); 

		$ikeaFamily = ShowProduct::productsForHeader()->map(function($item) {
			return $item->product;
		});

		$mainPageClass = 'main-page';

		$setting = Setting::where(['key' => 'contacts'])->first();
		$supportEmail = !empty($setting) ? json_decode($setting->value, true)['emails'][0] : array();

		return view('redesign.main', compact(
			'pageTitle',
			'nearestDeliveryDay',
			'pageDescription',
			'pageKeywords',
			'pluses',
			'slidesHorizontal',
			'slidesVertical',

			'catalog',
			'exchange',
			'categories',
			'supportEmail',
			'mainPageClass',

			'nearestDeliveryDayLocale',
			'nearestDeliveryDateObject',
			'ikeaFamily'
		));
	}

	/**
	 * Каталог - стартовая страница
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function catalog(){
		$pageTitle = 'ИКЕА с доставкой в Минск и по Беларуси';
		$pageDescription = 'ИКЕА с доставкой в Минск и по Беларуси';
		$pageKeywords = '';
		$shortText = '';

		$exchange = new ExchangeService();
		$catalogDivisions = $categories = Category::with('subcategories')->where(['visible' => 1])->get();
		$withBreadCrumbs = true;
		$withCatalogDivisions = true;
		$title = 'Каталог';

		$catalog = Product::where(['visible' => 1])
		                  ->orderBy('priority', 'desc')
		                  ->orderBy('name')
		                  ->distinct()
		                  ->paginate(12);

		return view('redesign.pages.category', compact(
			'pageTitle',
			'pageDescription',
			'pageKeywords',
			'shortText',
			'catalog',
			'catalogDivisions',

			'categories',
			'exchange',

			'title',
			'withBreadCrumbs',
			'withCatalogDivisions'
		));
	}

	/**
	 * Открыть страницу категории или субкатегории
	 *
	 * @param      $categoryAlias
	 * @param null $subcategoryAlias
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function categoryOrSubcategory($categoryAlias, $subcategoryAlias = null){
		/** @var Category $category */
		$category = Category::with(['subcategories'])->where('alias', '=', $categoryAlias)->first();
		$catalogDivisions = [];
		$subcategory = false;

		if(!$category){
			abort(404);
		}

		if($subcategoryAlias){
			/** @var Subcategory $subCategory */
			$subCategory = Subcategory::where('alias', '=', $subcategoryAlias)->first();

			if(!$subCategory) {
				abort(404);
			}

			$builder = $subCategory->products();
			$seoName = $subCategory->name;
			$page = $subCategory->page;

		} else {
			$builder = $category->products();
			$seoName = $category->name;
			$subCategory = null;

			$page = $category->page;
			$catalogDivisions = $category->subcategories;
		}

		$catalog = $builder->where(['visible' => 1])
		                   ->orderBy('priority', 'desc')
		                   ->orderBy('name')
							 ->distinct()
							 ->whereDoesntHave('visibles', function($q) use ($category, $subCategory) {
								 $q->where('category_id',  $category->id);
								 if ($subCategory) {
									 $q->where('sub_category_id',  $subCategory->id);
								 }
							 })
		                   ->paginate($catalogDivisions ? 12 : 150);

		/** @var Page $page */
		if($page) {
			$pageTitle       = $page->title;
			$pageDescription = $page->description;
			$pageKeywords    = $page->keywords;
			$shortText = $page->text_preview;
		} else {
			$pageTitle = "Купить {$seoName} в Минске";
			$pageDescription = "{$seoName} ИКЕА с доставкой в Минск и по Беларуси";
			$pageKeywords = '';
			$shortText = '';
		}

		$exchange = new ExchangeService();
		$categories = Category::with('subcategories')->where(['visible' => 1])->get();
		$withBreadCrumbs = true;
		$withCatalogDivisions = true;

		return view('redesign.pages.category', compact(
			'pageTitle',
			'pageDescription',
			'pageKeywords',
			'shortText',
			'catalog',

			'categories',
			'exchange',
			'category',
			'catalogDivisions',

			'subCategory',
			'withBreadCrumbs',
			'withCatalogDivisions'
		));
	}

	/**
	 * Страница товара
	 *
	 * @param $vendor string Артикул
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function product($vendor) {
		$exchanges = ExchangeService::getExchange();
		$vendorWithoutS = ltrim($vendor, 's');

		if($vendorWithoutS !== $vendor){
			return redirect()->away("/catalog/product/$vendorWithoutS");
		}

		$product = Product::with(['categories'])->where('vendor_code', $vendorWithoutS)->first();

		if(!$product){
			abort(404);
		}

		if(!$product->subcategories || $product->subcategories->isEmpty()){
			ParserService::parseProductPage($product->vendor_code, $product->link, null, null, null, true);
		}

		$attributesCombination = (json_decode($product->color));
		$possibleModifications = Product::where([
			'mod_group' => $product->mod_group,
			'visible' => 1,
			'name' => $product->name
		])->get();

		$additionalProductsCodes = Arr::flatten(json_decode($product->additional_products, true) ?? []);

		$additionalProducts = Product::whereIn('vendor_code', $additionalProductsCodes)->get()->unique('vendor_code');
		$possibleAttributes = [];

		foreach ($possibleModifications as $possibleModification) {

			$possibleAttributes[$possibleModification->vendor_code] = $possibleModification->attributes;
		}

		$pageTitle = "Купить {$product->name} ({$product->vendor_code}) в Минске";
		$pageDescription = $product->description;
		$pageKeywords = '';

		try {
			$product->priority++;
			$product->save();
		} catch (\Throwable $e){
			// suppress integer overflow
		}

		$exchange = new ExchangeService();
		$categories = Category::with('subcategories')->where(['visible' => 1])->get();
		$category = $product->categories->first();

		return view('redesign.pages.product', [
			'categories'            => $categories,
			'exchange'              => $exchange,
			'category'              => $category,
			'subCategory'           => $product->subcategories->first(),

			'product'               => $product,
			'possibleAttributes'    => $possibleAttributes,
			'additionalProducts'    => $additionalProducts,
			'attributesCombination' => $attributesCombination,
			'exchanges'             => $exchanges,
			'pageTitle'             => $pageTitle,
			'pageDescription'       => $pageDescription,
			'pageKeywords'          => $pageKeywords,
		]);
	}

	/*новые (по полю new)*/

	public static function available() {
		$exchanges = ExchangeService::getExchange();
		$catalog = Product::whereNull('available')
		                  ->where('visible', 1)
		                  ->orderBy('priority', 'desc')
		                  ->orderBy('name')
		                  ->paginate(50);

		//******************************

		$arr_name_numberKey = [];

		$buff_value = 0;
		foreach ($catalog as $key => $value) {

			if ($buff_value === $value->name) {
				$arr_name_numberKey["$buff_value"][] = $value;
			} else {

				$buff_value = $value->name;
				$arr_name_numberKey["$buff_value"][] = $value;
			}
		}

		$end_product = array_pop($arr_name_numberKey);

		//return $arr_name_numberKey;
		return view('catalog.category', [
			'catalog'            => $catalog,
			//'category' => $GLOBALS['category'],
			'arr_name_numberKey' => $arr_name_numberKey,
			'end_product'        => $end_product,
			'exchanges'          => $exchanges,

		]);

		//*****************************

		//        return view('catalog.category', ['catalog' => $catalog,
		//            'exchanges' =>$exchanges,
		//            ]);
	}


	public static function showNew() {
		$exchanges = ExchangeService::getExchange();
		$catalog = Product::where('new', 1)
		                  ->where('visible', 1)
		                  ->orderBy('name')
		                  ->orderBy('priority', 'desc')
		                  ->paginate(50);
		//******************************

		$arr_numberKey_name = [];
		$arr_name_numberKey = [];

		$buff_value = 0;
		foreach ($catalog as $key => $value) {

			if ($buff_value === $value->name) {
				$arr_name_numberKey["$buff_value"][] = $value;
			} else {

				$buff_value = $value->name;
				$arr_name_numberKey["$buff_value"][] = $value;
			}
		}

		$newCategories = '';

		foreach ($catalog as $category) {
			$newCategories .= ",$category->name";
		}

		$year = date('Y');

		$end_product = array_pop($arr_name_numberKey);

		//return $arr_name_numberKey;
		return view('redesign.pages.category', [
			'catalog'            => $catalog,
			//'category' => $GLOBALS['category'],
			'arr_name_numberKey' => $arr_name_numberKey,
			'end_product'        => $end_product,
			'exchanges'          => $exchanges,
			'exchange'           => new ExchangeService(),
			'pageTitle'          => "Новинки ИКЕА (IKEA) c доставкой в Минск и по всей Беларуси",
			'pageDescription'    => "Новинки ИКЕА. $newCategories",
			'pageKeywords'       => "новинки ИКЕА, новинки икеа $year, икеа $year",

		]);
	}

	/**
	 * Поиск товаров по артикулу или по названию.
	 * Если передать параметр full, вернётся страница с постраничными результатами поиска
	 *
	 * @param Request $request
	 *
	 * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
	 */
	public function search(Request $request){
		$originalSearch = $request->get('search');
		$renderFullPage = $request->get('full');

		$originalSearch = stripEmoji($originalSearch);

		$productsBuilder = Product::where(['visible' => 1]);

		if ($originalSearch === 'ikea_family') {
			$productsBuilder->whereRaw('family_price is not null and char_length(family_price) > 0')->where('visible', 1);
		} else {
			$productsBuilder = $this->applySearchConditions($productsBuilder, $originalSearch);
		}

		$productsBuilder->orderBy('priority', 'desc');

		if($renderFullPage) {
			$exchange = new ExchangeService();
			$categories = Category::with('subcategories')->where(['visible' => 1])->get();

			return view('redesign.pages.search', [
				'catalog'    => $productsBuilder->paginate(120)
				                                ->appends(request()->except('page')),
				'search'     => $originalSearch,
				'categories' => $categories,
				'exchange'   => $exchange,
			]);
		}

		return $productsBuilder->limit(4)->get();
	}

	/**
	 * @param Request $request
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public static function showCheck(Request $request) {
		$message = '';
		if ($request->get('id') && $request->get('quantity')) {
			$id = $request->get('id');
			$quantity = $request->get('quantity');
			$message = 'Результат поиска по артикулу ' . $id . ':  ' . $quantity . ' шт';
		}
		if ($request->get('id') && $request->get('info')) {
			$message = $request->get('info');
		}

		return view('check', ['message' => $message]);
	}

	/**
	 * @param $categoryAlias
	 * @deprecated
	 * @return array
	 */
	private static function getSubCategoryIds($categoryAlias) {
		$category = Category::query()
		                    ->where('alias', $categoryAlias)
		                    ->firstOrFail();

		return Subcategory::query()
		                  ->where('category_id', $category->getKey())
		                  ->get(['id'])
		                  ->pluck('id')
		                  ->toArray();
	}

	protected function applySearchConditions($builder, $originalSearch){
		$searchVendorCode = str_replace(['.', '', 's'], '', $originalSearch);
		$searchFuzzy = StringNormalize::stringToFuzzy($originalSearch);
		$searchLayout = convertLayout(str_replace(['.', ''], '', $originalSearch));

		$tryVendorCode = (clone $builder)->where(['vendor_code'=> $searchVendorCode])->orWhere([
			'vendor_code' => 's' . $searchVendorCode
		]);

		if($tryVendorCode->count() > 0){
			return $tryVendorCode;
		}

		//2. название/часть названия
		$tryByName = (clone $builder)->where('name', 'like', "%$originalSearch%");

		if($tryByName->count() > 0){
			return $tryByName;
		}

		//3. описание/часть описания
		$tryByDesc = (clone $builder)->where('type', 'like', "%$originalSearch%");

		if($tryByDesc->count() > 0){
			return $tryByDesc;
		}

		//6. 2/3+размер с единицами измерения и без(«стеллаж 70», «мальм комод 200 см», «мальм комод 200см»)
		$wordsParts = explode(' ', $searchFuzzy);

		$sizes = array_filter($wordsParts, function ($w) {
			return (boolean)strpbrk($w, '1234567890');
		});

		$nameOrCategory = array_filter($wordsParts, function ($w) {
			return !strpbrk($w, '1234567890') && mb_strlen($w) > 2;
		});

		$tryByNameAndDescAndSizes = (clone $builder)->where(function ($q) use ($sizes, $nameOrCategory) {
			$q->where(function ($q) use ($sizes) {
				foreach ($sizes as $size){
					$q->where('type_fuzzy', 'like', "%$size%")
					;
				}
			})->where(function ($q) use ($nameOrCategory) {
				foreach ($nameOrCategory as $wordsPart) {
					$q->where(function ($q) use($wordsPart) {
						$q->where('name_fuzzy', 'like', "%$wordsPart%")
						  ->orwhere('type_fuzzy', 'like', "%$wordsPart%")
						;
					});
				}
			});
		});

		if($tryByNameAndDescAndSizes->count() > 0){
			return $tryByNameAndDescAndSizes;
		}

		//4. название/часть названия + описание/часть описания («каллакс черный») и наоборот
		//5. часть описания + часть описания («стеллаж белый») и наоборот
		$tryByNameAndDesc = (clone $builder)->where(function ($q) use($nameOrCategory) {
			foreach ($nameOrCategory as $wordsPart) {
				$q->where(function ($q) use($wordsPart) {
					$q->where('name_fuzzy', 'like', "%$wordsPart%")
					  ->orwhere('type_fuzzy', 'like', "%$wordsPart%")
					;
				});
			}
		});

		if($tryByNameAndDesc->count() > 0){
			return $tryByNameAndDesc;
		}

		//4. название/часть названия + описание/часть описания («каллакс черный») и наоборот
		//5. часть описания + часть описания («стеллаж белый») и наоборот
		$tryByNameAndDescNotStrict = (clone $builder)->where(function ($q) use($nameOrCategory) {
			foreach ($nameOrCategory as $wordsPart) {
				$q->orwhere('name_fuzzy', 'like', "%$wordsPart%")
				  ->orwhere('type_fuzzy', 'like', "%$wordsPart%")
				;
			}
		});

		if($tryByNameAndDescNotStrict->count() > 0){
			return $tryByNameAndDesc;
		}

		// 7. категория/часть категории или подкатегория/часть подкатегории
		$tryByCategory = (clone $builder)->where(function ($q) use ($nameOrCategory) {
			foreach ($nameOrCategory as $wordsPart) {
					$q->whereHas('subcategories', function ($q) use($wordsPart) {
						$q->where('name', 'like', "%$wordsPart%");
					})->orwhereHas('categories', function ($q) use($wordsPart) {
						$q->where('name', 'like', "%$wordsPart%");
					})
				;
			}
		});

		return $tryByCategory;
	}
}
