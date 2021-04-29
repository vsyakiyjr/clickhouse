<?php

namespace App\Services;

use App\Models\Category;
use App\Models\ParserSitemapsQueue;
use App\Models\ParserStatus;
use App\Models\Product;
use App\Models\ProductsGrouped;
use App\Models\Subcategory;
use App\StringNormalize;
use Carbon\Carbon;
use DB;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;

class ParserService {
    protected static $proxy = '';

    protected static $userAgent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.3904.70 Safari/537.36';

    public static function parseCategoriesAndSubcategories() {
        $client = new Client([
            'proxy'   => static::$proxy,
            'headers' => [
                'User-Agent' => static::$userAgent,
                'Referer'    => 'https://www.ikea.com/ru/ru/',
            ],
        ]);
        // https://www.ikea.com/ru/ru/header-footer/menu-products.html
        $navigationHtml = (string) $client->get('https://www.ikea.com/ru/ru/header-footer/menu-products.html')
                                          ->getBody()->getContents();
        $navigationHtmlFull = "
		<!doctype html>
		<html lang=\"ru\">
		<head>
		  <meta charset=\"UTF-8\">
		  <meta http-equiv=\"X-UA-Compatible\"
		        content=\"ie=edge\">
		  <title>Document</title>
		</head>
		<body>
		$navigationHtml
		</body>
		</html>";

        $crawler = new Crawler($navigationHtmlFull);
        $categories = $crawler->filterXPath('//body/li');

        foreach ($categories as $category) {
            $c = new Crawler($category);
            $categoryLink = $c->filter('a:not([data-tracking-label])');
            $categoryUrl =$categoryLink->attr('href');
            $categoryName = trim($categoryLink->text());
            $categoryIkeaId = self::extractIkeaId($categoryUrl);

            $existingCategory = Category::where(['name' => $categoryName,])
                                        ->orWhere(['ikea_id' => $categoryIkeaId])
                                        ->first()
            ;

            $existingCategory = $existingCategory ?? Category::create([
                    'name'    => $categoryName,
                    'link'    => $categoryUrl,
                    'alias'   => transliterate($categoryName),
                    'ikea_id' => $categoryIkeaId,
                    'is_new'  => 1,
                ]);

            $subcategoryHtml = $client->get($categoryUrl)->getBody()->getContents();
            $cc = new Crawler();
            $cc->addHtmlContent($subcategoryHtml);

            $subcategoriesLinks = $cc->filter('.vn__wrapper > nav a');

            foreach ($subcategoriesLinks as $subcategoryLinkItem) {
                $sc = new Crawler($subcategoryLinkItem);
                $subcategoryLink = $sc->attr('href');
                $subcategoryName = $sc->filter('img')->attr('alt');
                $subcategoryImage = $sc->filter('img')->attr('src');
                $subcategoryIkeaId = static::extractIkeaId($subcategoryLink);

                $existingSubcategory = Subcategory::where(['name' => $subcategoryName])->orWhere(['ikea_id' => $subcategoryIkeaId])->firstOrNew([]);
                $existingSubcategory->fill([
                    'name'        => $subcategoryName,
                    'link'        => $subcategoryLink,
                    'category_id' => $existingCategory->id,
                    'img'         => $subcategoryImage,
                    'new'         => !$existingSubcategory->exists,
                    'alias'       => transliterate($subcategoryName),
                    'ikea_id'     => $subcategoryIkeaId,
                ]);

                $existingSubcategory->save();

                try {
                    $existingCategory->subcategories()->attach($existingSubcategory->id);
                } catch (\Throwable $e) {

                }
            }
        }
    }

    /**
     * @param      $subcategoriesIds
     * @param null $processId
     *
     * @throws Exception
     */
    public static function clearProducts($subcategoriesIds, $processId = null) {
        $totalSubcategories = Subcategory::whereIn('id', $subcategoriesIds)->count();

        $totalProducts = Product::whereHas('subcategories', function ($q) use ($subcategoriesIds) {
            $q->whereIn('id', $subcategoriesIds);
        })->count();

        if (!is_null($processId)) {
            $processStatus = ParserStatus::create([
                'process_id'          => $processId,
                'total_subcategories' => $totalSubcategories,
                'total_products'      => $totalProducts,
            ]);
        } else {
            $processStatus = null;
        }

        libxml_use_internal_errors(true);

        foreach ($subcategoriesIds as $subcategoryId) {
            $subcategory = Subcategory::find($subcategoryId);

            $products = $subcategory->products;

            foreach ($products as $product) {
                /** @var Product $product */
                self::parseProductPage($product->vendor_code, $product->link, $subcategory, $processStatus, $product->mod_group);
            }

            if ($processStatus && $processStatus->exists) {
                $processStatus->processed_subcategories++;
                $processStatus->save();
            }
        }

        if ($processStatus && $processStatus->exists) {
            $processStatus->delete();
        }
    }

    /**
     * @return array
     */
    public static function getSitemapXmls(): array {
        $fullSitemapXml = (string) (new Client())->get('https://www.ikea.com/sitemaps/sitemap.xml')->getBody();
        $sitemapsList = json_decode(json_encode(new \SimpleXMLElement($fullSitemapXml)), true)['sitemap'];

        $ruruSitemapsUrls = [];
        foreach ($sitemapsList as $sitemapLoc) {
            if (strpos($sitemapLoc['loc'], '/prod-ru-RU_') !== false) {
                $ruruSitemapsUrls[] = $sitemapLoc['loc'];
            }
        }

        return $ruruSitemapsUrls;
    }

    /**
     * Clear products
     *
     * @param null $categoryId
     * @param null $subcategoryId
     */
    public static function clear($categoryId = null, $subcategoryId = null) {
        $php = config('app.phpInterpreter');
        $artisan = base_path('artisan');

        $builder = Subcategory::where(['visible' => 1])->orderBy('id');

        if ($subcategoryId) {
            $builder->where('id', '=', $subcategoryId);
        } elseif ($categoryId) {
            $builder->where('category_id', '=', $categoryId);
        }

        $subcategoriesChunks = $builder->get()->split(6);
        foreach ($subcategoriesChunks as $index => $subcategories) {
            $pid = $index + 1;
            $categoryIds = array_column($subcategories->toArray(), 'id');
            $categoryIdsImploded = implode(',', $categoryIds);

            $command = "$php $artisan parse:catalog --ids=$categoryIdsImploded --clear=1 --process=$pid >/dev/null 2>/dev/null &";
            \Log::debug('parser-start', ['cmd' => $command]);
            shell_exec($command);
        }
    }

    /**
     * Run parser
     */
    public static function run() {
        Product::query()->update(['parsed_at_this_run' => false]); // ВАЖНО! обнулять при каждом запуске
        $php = config('app.phpInterpreter');
        $artisan = base_path('artisan');

        $sitemapsUrls = ParserService::getSitemapXmls();
        $chunksSize = ceil(count($sitemapsUrls) / 6); // split into 6 threads
        $chunks = array_chunk($sitemapsUrls, $chunksSize);

        foreach ($chunks as $index => $chunk) {
            $pid = $index + 1;

            $sitemapStatusData = array_map(function ($s) use ($pid) {
                return [
                    'sitemap_url' => $s,
                    'process_id'  => $pid,
                ];
            }, $chunk);

            ParserSitemapsQueue::insert($sitemapStatusData);

            $command = "$php $artisan parse:catalog --process=$pid >/dev/null 2>/dev/null &";
            \Log::debug('parser-start', ['cmd' => $command]);
            shell_exec($command);
        }
    }

    /**
     * @param null $processId
     * @param null $singleSitemapUrl
     *
     * @throws Exception
     */
    public static function saveProductsFromSitemaps($processId, $singleSitemapUrl = null) {
        $sitemaps = $singleSitemapUrl ? [$singleSitemapUrl] : ParserSitemapsQueue::where(['process_id' => $processId])
                                                                                 ->pluck('sitemap_url');
        $productsUrls = [];

        foreach ($sitemaps as $sitemapUrl) {
            $sitemapXml = (string) (new Client())->get($sitemapUrl)->getBody();
            $sitemap = json_decode(json_encode(new \SimpleXMLElement($sitemapXml)), true);

            $urls = array_filter($sitemap['url'], function ($sitemapElem) {
                $url = $sitemapElem['loc'];

                return strpos($url, 'https://www.ikea.com/ru/ru/p') === 0;
            });

            $productsUrls = array_merge($productsUrls, $urls);
        }

        $processStatus = new ParserStatus();
        $processStatus->total_products = count($productsUrls);
        $processStatus->process_id = $processId;
        $processStatus->save();

        foreach ($productsUrls as $url) {
            $vendorCode = self::extractIkeaId($url['loc']);

            self::parseProductPage($vendorCode, $url['loc'], null, $processStatus, null, true, true);
        }

        ParserSitemapsQueue::where(['process_id' => $processId])->delete();
    }

    /**
     * Parse single product page
     *
     * @param                   $vendorCode
     * @param                   $url
     * @param Subcategory|null  $subcategory
     * @param ParserStatus|null $processStatus
     * @param null              $parentProductCode
     * @param boolean           $fromSitemap
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     * @throws Exception
     */
    public static function parseProductPage($vendorCode, $url, Subcategory $subcategory = null, ParserStatus $processStatus = null, $parentProductCode = null, $fromSitemap = false) {
        $url = explode('?', $url)[0];

        if (!$fromSitemap && $processStatus && $subcategory) {
            $processStatus->subcategory_id = $subcategory->id;
            $processStatus->vendor_code = $vendorCode;
            $processStatus->url = $url;
            $processStatus->save();
            $processedCodes[] = $processStatus->vendor_code;
            $processStatus->refresh();
        }

        if ($fromSitemap && $processStatus) {
            $processStatus->vendor_code = $vendorCode;
            $processStatus->url = $url;
            $processStatus->save();
            $processStatus->refresh();
        }

        $productCrawler = new Crawler();
        $guzzle = new Client([
            'proxy'   => static::$proxy,
            'headers' => [
                'User-Agent' => static::$userAgent,
                'Referer'    => 'https://www.ikea.com/ru/ru/',
            ],
        ]);

        try {
            $html = (string) $guzzle->get($url)->getBody()->getContents();
        } catch (\Throwable $e) {
            Log::debug('Product page error', [
                'url' => $url,
                'e'   => $e,
            ]);
            //			Sentry::captureException($e);
            // 404 or timeout
            $product = Product::where('vendor_code', '=', $vendorCode)
                              ->first();
            if ($product) {
                $product->delete();
            }

            return null;
        }

        if ($html === '<span class="product-missing"></span>'/* || !Str::contains($html, '<!DOCTYPE html>')*/) {
            $product = Product::where('vendor_code', '=', $vendorCode)->first();
            if ($product) {
                $product->delete();
            }

            return null;
        }

        $productCrawler->addHtmlContent($html);

        if ($fromSitemap && !$subcategory) {
            try {
                $breadcrumbs = $productCrawler->filter('ol.bc-breadcrumb__list > li > a[data-tracking-label]');
                $subCategoryPosition = $breadcrumbs->eq(0)->text() === 'Товары' ? 2 : 1;

                $subcategoryElem = $breadcrumbs->eq($subCategoryPosition);
                $subcategoryUrl = $subcategoryElem->attr('href');
                $subcategoryIkeaId = static::extractIkeaId($subcategoryUrl);

                $subcategory = Subcategory::where(['ikea_id' => $subcategoryIkeaId])->first();
            } catch (\Throwable $e) {

            }
        }

        try {
            $lastCatUrl = $productCrawler->filter('ol.bc-breadcrumb__list > li > a[data-tracking-label]')->last()
                                         ->attr('href');
            $catId = self::extractIkeaId($lastCatUrl);
        } catch (\Throwable $e) {
            $catId = null;
        }

        $sizesRaw = $productCrawler->filter('.range-revamp-product-dimensions__list-container');
        try {
            $sizesOriginal = $productCrawler->filter('.range-revamp-product-dimensions__list')->html();
        } catch (\Throwable $e) {
            $sizesOriginal = '';
        }

        $sizes = [];

        if ($sizesRaw->count() > 0) {
            foreach ($sizesRaw as $sizeRaw) {
                /** @var Crawler $sizeRaw */
                $sizeCrawler = new Crawler();
                $sizeCrawler->add($sizeRaw);

                $sizeDimension = $sizeCrawler->filter('dt')->text();
                $sizeValue = $sizeCrawler->filter('dd')->text();
                $sizesMapping = [
                    'Длина'          => 'width',
                    'Ширина'         => 'height',
                    'Высота'         => 'height',
                    'Длина матраса'  => 'width',
                    'Ширина матраса' => 'height',
                ];

                foreach ($sizesMapping as $check => $prop) {
                    if (Str::contains($sizeDimension, $check)) {
                        $sizes[$prop] = $sizeValue;
                    }
                }
            }
        }

        $attributes = [];
        $possibleAttributes = [];
        $attributesRaw = [];
        $sameGroupProductsCodes = [];
        $sameGroupProductsUrls = [];

        $getInitialProps = static function (Crawler $c) {
            $variationsJsonRaw = $c->attr('data-initial-props');

            return json_decode(html_entity_decode($variationsJsonRaw), true);
        };

        $variationsElem = $productCrawler->filter('.js-product-variation-section');
        if ($variationsElem->count() > 0) {
            $initialProps = $getInitialProps($variationsElem);
            // в data-initial-props лежит список опций, таких как размер, материал итд, внутри каждой опции её свойства и список доступных вариантов (размера/материала)
            $attributesRaw = $initialProps['variations'];
        }

        $stylesElem = $productCrawler->filter('.js-product-style-picker');
        if ($stylesElem->count() > 0) {
            $initialProps = $getInitialProps($stylesElem);
            // в data-initial-props сразу лежит свойства единственного стиля, например цвет, и список доступных варантов (цветов)
            $attributesRaw[] = $initialProps;
        }

        foreach ($attributesRaw as $variation) {
            $attribute = $variation['title'];

            foreach ($variation['options'] ?? [] as $option) {
                $possibleAttributes[$attribute][] = $option['title'];

                if ($option['isSelected']) {
                    $attributes[$attribute] = $option['title'];
                }

                $sameGroupProductCode = self::extractIkeaId($option['url']);
                $sameGroupProductsCodes[] = $sameGroupProductCode;
                $sameGroupProductsUrls[$sameGroupProductCode] = $option['url'];
            }
        }

        $sameGroupProductsCodes = array_unique($sameGroupProductsCodes);

        try {
            $additionalProductsJson = (string) $guzzle->get("https://rec.ingka.com/recommendations/v1/ru-prod/items/{$vendorCode}?n=12&filter=removeItemCat", [
                'proxy'   => static::$proxy,
                'headers' => [
                    'Referer' => $url,
                ],
            ])->getBody()->getContents();

            $additionalProducts = array_column(json_decode($additionalProductsJson, true), 'itemId');
        } catch (\Throwable $e) {
            $additionalProducts = [];
        }

        $markOfStartJson = 'var utag_data =';
        $markOfEndJson = '</script>';

        $productMetaInfoStart = mb_strpos($html, $markOfStartJson) + strlen($markOfStartJson);
        $productMetaInfoEnd = mb_strpos($html, $markOfEndJson, $productMetaInfoStart);
        $productMetaInfoRaw = mb_substr(
            $html,
            $productMetaInfoStart,
            $productMetaInfoEnd - $productMetaInfoStart,
            'UTF-8'
        );

        $productMetaInfoJson = preg_split('/};?/u', $productMetaInfoRaw)[0] . '}';

        $productMetaInfo = json_decode($productMetaInfoJson, true);

        $productName = mb_convert_case(trim($productMetaInfo['product_names'][0]), MB_CASE_TITLE);
        $productTypePieces = $productCrawler
            ->filter('.range-revamp-product__buy-module-content .range-revamp-header-section .range-revamp-header-section__description > span')
            ->each(static function (Crawler $c) {
                return trim($c->text());
            });
        $productType = implode(', ', $productTypePieces);

        $descriptionPieces = $productCrawler
            ->filter('div.range-revamp-product-details > div.range-revamp-product-details__container span.range-revamp-product-details__paragraph')
            ->each(static function (Crawler $c) {
                return trim($c->text());
            });
        $description = implode(' ', $descriptionPieces);

        try {
            $packageCountText = $productCrawler
                ->filter('#SEC_product-details-packaging .range-revamp-product-details__package-count')
                ->text();
            $packageCount = preg_replace('/[^\d]/', '', $packageCountText);
        } catch (\Throwable $e) {
            $packageCount = 1;
        }

        $packageInfoDetails = $productCrawler
            ->filter('#SEC_product-details-packaging > div > div.range-revamp-product-details__container');

        $packageInfo = [];

        if ($packageInfoDetails->count() > 0) {
            foreach ($packageInfoDetails as $packageInfoDetail) {
                $c = new Crawler($packageInfoDetail);

                $packInfo = [
                    'articleNumber' => '',
                    'pkgInfo'       => [],
                ];

                try {
                    $articleNumberRaw = $c->filter('.range-revamp-product-identifier__number')->text();
                } catch (\Throwable $e) {
                    $articleNumberRaw = $c->filter('.range-revamp-product-identifier__value')->text();
                }

                $packInfo['articleNumber'] = trim(str_replace('.', '', $articleNumberRaw));

                try {
                    $packageCountForThisItem = $c
                        ->filter('div.range-revamp-product-details__container > div.range-revamp-product-details__container > span:last-child')
                        ->text();
                    $packInfo['quantity'] = trim(explode(':', $packageCountForThisItem)[1]);
                } catch (\Throwable $e) {
                    $packInfo['quantity'] = 1;
                }

                $packageInfoRows = $c
                    ->filter('div.range-revamp-product-details__container > div.range-revamp-product-details__container > span:not(:last-child)');
                foreach ($packageInfoRows as $row) {
                    [
                        $packageDimensionName,
                        $packageDimensionValue,
                    ] = explode(': ', $row->textContent);

                    $paramsMapping = [
                        'Ширина' => 'widthMet',
                        'Высота' => 'heightMet',
                        'Длина'  => 'lengthMet',
                        'Вес'    => 'weightMet',
                    ];

                    $mappedParam = $paramsMapping[$packageDimensionName] ?? '';

                    if ($mappedParam) {
                        $packInfo['pkgInfo'][$mappedParam] = $packageDimensionValue;
                    }
                }

                $packageInfo[] = $packInfo;
            }
        }

        if (count($packageInfo) === 1) {
            $length = $packageInfo[0]['pkgInfo']['lengthMet'] ?? '';
            $weight = $packageInfo[0]['pkgInfo']['weightMet'] ?? '';
        } else {
            $length = '';
            $weight = '';
        }

        $isNew = $productCrawler
                ->filter('.range-revamp-product__buy-module-content .range-revamp-product-highlight__new')
                ->count() > 0;

        try {
            $mainImage = $productCrawler->filter('meta[itemprop="image"]')->attr('content');
        } catch (\Throwable $e) {
            $mainImage = $productCrawler->filter('meta[property="og:image"]')->attr('content');
        }

        $images = $productCrawler->filter('div.range-revamp-media-grid__grid img')->extract(['src']);

        $images = array_map(function ($i) {
            return str_replace('?f=s', '', $i);
        }, $images);

        $imagesToSave = [
            'large' => $images,
        ];

        try {
            $soldSeparately = $productCrawler->filter('.range-revamp-sold-separately__text')->text();
        } catch (\Throwable $e) {
            $soldSeparately = '';
        }

        try {
            $benefit = $productCrawler
                ->filter('.range-revamp-product-summary > .range-revamp-product-summary__description')
                ->text();
        } catch (\Throwable $e) {
            $benefit = '';
        }

        try {
            $materials = trim(
                $productCrawler
                    ->filter('#SEC_product-details-material-and-care > div > .range-revamp-product-details__container')
                    ->text()
            );
        } catch (\Throwable $e) {
            $materials = '';
        }

        try {
            $attachmentsToSave = $productCrawler
                ->filter('#SEC_product-details-assembly-and-documents > div > .range-revamp-product-details__container')
                ->each(static function (Crawler $c1) {
                    $groupName = $c1->filter('.range-revamp-product-details__document-header')->text();

                    $attachments = $c1->filter('a')->each(static function (Crawler $c2) {
                        return [
                            'attachmentPath' => $c2->extract(['href'])[0],
                            'attachmentName' => $c2->filter('span:first-child')->text(),
                        ];
                    });

                    // нет, это не опечатка - так надо для обратной совместисти
                    return [
                        'atcharray' => $attachments,
                        'name'      => $groupName,
                    ];
                });
        } catch (\Throwable $e) {
            $attachmentsToSave = [];
        }

        $hasIkeaFamilyPrice = $productCrawler
                ->filter('.range-revamp-product__buy-module-content .range-revamp-product-highlight__family-label')
                ->count() > 0;

        try {
            if ($hasIkeaFamilyPrice) {
                $ikeaFamilyPrice = $productMetaInfo['price'][0];
                $priceText = $productCrawler->filter('.range-revamp-pip-price-package__previous-price .range-revamp-price__integer')
                                            ->text();
                $price = preg_replace('/\D/', '', $priceText);
            } else {
                $price = $productMetaInfo['price'][0];
                $ikeaFamilyPrice = null;
            }
        } catch (\Throwable $e) {
            $price = $productMetaInfo['price'][0];
            $ikeaFamilyPrice = null;
        }

        if ($fromSitemap && !$parentProductCode) {
            $sameGroupProducts = ProductsGrouped::whereIn('grouped_vendor_code', $sameGroupProductsCodes)->first();
            $parentProductCode = $sameGroupProducts->parent_vendor_code ?? $vendorCode;
        }

        $product_data = [
            'name'                => $productName,
            'link'                => $url,
            'alias'               => transliterate($productName),
            'vendor_code'         => $vendorCode,
            'type'                => $productType,
            'description'         => $description ?? '',
            'color'               => !empty($attributes) ? json_encode(array_values($attributes), JSON_UNESCAPED_UNICODE) : '',
            'height'              => $sizes['height'] ?? '',
            'width'               => $sizes['width'] ?? '',
            'length'              => $length,
            'weight'              => $weight,
            'packaging'           => $packageCount,
            'photo'               => $mainImage,
            'gallery'             => json_encode($imagesToSave),
            'price'               => $price,
            'visible'             => 1,
            'new'                 => $isNew,
            'mod_group'           => $parentProductCode,
            'family_price'        => $ikeaFamilyPrice,
            'family_offers_start' => null,
            'family_offers_end'   => null,
            'benefit'             => $benefit,
            //			'good_to_know'        => (property_exists($item, 'goodToKnow')) ? strip_tags($item->goodToKnow) : '',
            'sold_separately'     => $soldSeparately,
            'cust_materials'      => $materials,
            'attachments'         => json_encode($attachmentsToSave, JSON_UNESCAPED_UNICODE),
            'pkg_info'            => !empty($packageInfo) ? json_encode($packageInfo, JSON_UNESCAPED_UNICODE) : '',
            'additional_products' => json_encode($additionalProducts),
            'size'                => !empty($sizes) ? json_encode($sizes, JSON_UNESCAPED_UNICODE) : '',
            'possible_attributes' => $possibleAttributes,
            'attributes'          => $attributes,
            'sizes_original'      => $sizesOriginal,
            'cat_ikea_id'         => $catId,
            'parsed_at'           => Carbon::now(),
            'parsed_at_this_run'  => true,

            'name_fuzzy'        => StringNormalize::stringToFuzzy($productName),
            'type_fuzzy'        => StringNormalize::stringToFuzzy($productType),
            'description_fuzzy' => StringNormalize::stringToFuzzy($description),
        ];

        $product = Product::where('vendor_code', '=', $vendorCode)->firstOrNew([]);

        $product->fill($product_data);

        if ($product->exists) {
            $product->save();

            if ($subcategory) {
                DB::insert("insert ignore into products_subcategories (product_id, subcategory_id) values (?,?)", [
                    $product->id,
                    $subcategory->id,
                ]);
            }
        } else {
            try {
                $product->save();
                $subcategory->products()->create($product_data);
            } catch (\Throwable $e) {

            }
        }

        foreach ($sameGroupProductsCodes as $productCode) {
            try {
                ProductsGrouped::firstOrCreate([
                    'parent_vendor_code'  => $product_data['mod_group'],
                    'grouped_vendor_code' => $productCode,
                ]);
            } catch (\Throwable $e) {

            }
        }

        if ($processStatus && $processStatus->exists) {
            $processStatus->processed_products++;
            $processStatus->save();
        }

        return $product;
    }

    /**
     * @param string $url
     *
     * @return string
     */
    public static function extractIkeaId($url): string {
        return trim(Arr::last(explode('-', $url)), 's/');
    }
}
