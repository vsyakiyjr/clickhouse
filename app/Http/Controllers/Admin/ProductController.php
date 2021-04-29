<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductsVisible;
use App\Models\ShowProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Validator;

class ProductController extends Controller {
	/**
	 * Display a listing of active resources.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function lists(Request $request) {
		//	    ini_set('display_errors', '1');
		//	    ini_set('display_startup_errors', '1');
		//        ini_set('memory_limit', '1024M');

		$columnLists = Schema::getColumnListing("products");
		$columnLists = collect($columnLists);

		$sort = $request->sortBy && $columnLists->contains($request->sortBy) ? $request->sortBy : 'id';
		$order = $request->descending ? ('false' == $request->descending ? 'asc' : 'desc') : 'asc';

		$query = Product::orderBy($sort, $order)
		                ->with('categories');

		if ($request->category_id) {
			$query->whereHas('categories', function ($q) use ($request) {
				$q->where('id', '=', $request->category_id);
			});
		}

		if ($request->sub_category_id) {
			$query->whereHas('subcategories', function ($q) use ($request) {
				$q->where('id', '=', $request->sub_category_id);
			});
		}

		if ($request->search) {
			$query->where('name', 'like', $request->search . '%')
			      ->orWhere('vendor_code', 'like', '%' . str_replace('.', '', $request->search) . '%');
		}

		$this->collectOrdersData();

		return ($request->rowsPerPage > 0) ? $query->paginate($request->rowsPerPage) : $query->paginate(100);
	}

	public function collectOrdersData() {
		DB::table('products')->update(['qty_orders' => 0]);
		//        $products = Product::all();
		//        foreach ($products as $product) {
		//            $product['qty_orders'] = 0;
		//            $product->update();
		//
		//        }

		$orders = Order::Join('deliverance_days', 'deliverance_days.date', '=', 'orders.delivery_date')
		               ->where('deliverance_days.status', '=', 2)
		               ->get();

		foreach ($orders as $order) {
			$qtyVendoCodes = (json_decode($order->vendor_code));
			if ($qtyVendoCodes) {
				foreach ($qtyVendoCodes as $key => $qtyVendoCode) {
					$vendCode = (key($qtyVendoCode));
					$products = Product::where('vendor_code', $vendCode)->get();
					if ($products) {
						foreach ($products as $product) {
							$arry = ((array) ($qtyVendoCode));

							if (isset($arry[$vendCode])) {
								$product['qty_orders'] = $product['qty_orders'] + $arry[$vendCode];
								$product->update();
							}
						}
					}
				}
			}
		}

		return ['success' => true];
	}

	public function fixed(Request $request) {
		if ($request->category_id) {
			Product::whereHas('categories', function ($q) use ($request) {
				$q->where('id', '=', $request->category_id);
			})->update(['fixed_price' => $request->fixed_price == 'true' ? 1 : 0]);
		}

		return ['success' => true];
	}

	/**
	 * Store resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return array
	 */
	public function store(Request $request) {
		$validator = Validator::make($request->all(), [
			'discount'    => 'required',
			'fixed_price' => 'required',
			'visible'     => 'required',
		]);

		if ($validator->fails()) {
			return [
				'success' => false,
				'errors'  => $validator->errors(),
			];
		}

		$product = Product::firstOrNew(['id' => $request->id], [
			//            'category_id' => $request->category_id,
			'vendor_code'       => $request->vendor_code,
			'type'              => $request->type,
			'name'              => $request->name,
			'quantity'          => $request->quantity,
			'quantity_controll' => $request->quantity_controll,
			'fixed_price'       => $request->fixed_price,
			'discount'          => $request->discount,
			'visible'           => $request->visible,
			'length'            => $request->length,
			'color'             => $request->color,
			'price'             => $request->price,
			'family_price'      => $request->family_price,
		]);

		//        $product->category_id = $request->category_id;
		$product->vendor_code = $request->vendor_code;
		$product->type = $request->type;
		$product->name = $request->name;
		$product->quantity = $request->quantity;
		$product->quantity_controll = $request->quantity_controll;
		$product->fixed_price = $request->fixed_price;
		$product->visible = $request->visible;
		$product->discount = $request->discount;
		$product->length = $request->length;
		$product->color = $request->color;
		$product->price = $request->price;
		$product->family_price = $request->family_price;
		$product->priority = $request->priority;

		$product->save();

		return ['success' => true];
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param int $id
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id) {
		Product::find($id)->delete();

		return ['success' => true];
	}

	public function findInCategory(Request $request) {
		ini_set('memory_limit', '1024M');

		$sort = $request->sortBy ? $request->sortBy : 'id';
		$order = $request->descending ? ('false' == $request->descending ? 'asc' : 'desc') : 'asc';

		$query = Product::orderBy($sort, $order)
		                ->with('categories')
		                ->where('name', 'like', '%' . $request->search . '%')
		                ->orWhere('vendor_code', 'like', '%' . str_replace('.', '', $request->search) . '%')
		                ->groupBy('name');

		if ($request->category_id) {
			$query->whereHas('categories', function ($q) use ($request) {
				$q->where('id', '=', $request->category_id);
			});
		}

		//$this->collectOrdersData();

		return ($request->rowsPerPage > 0) ? $query->paginate($request->rowsPerPage) : $query->paginate(100);
	}

	public function addToShowProduct(Request $request, Product $products) {
		if (ShowProduct::query()->where('vendor_code', $products->vendor_code)
		               ->where('place', $request->get('place'))->count() === 0) {
			$showProduct = new ShowProduct();
			$showProduct->vendor_code = $products->vendor_code;
			$showProduct->place = $request->get('place');
			$showProduct->save();

			return ['success' => true];
		}

		return [
			'success' => false,
			'item created yet',
		];
	}

	public function removeFromShowProduct(Request $request) {
		$swowProduct = ShowProduct::query()->where('vendor_code', $request->vendor_code)
		                          ->where('place', $request->get('place'));
		$swowProduct->delete();

		return ['success' => true];
	}

	public function getMain(Request $request) {
		if ($request->get('place') === 'main') {
			return ShowProduct::mainProducts()->get();
		} else {
			return ShowProduct::headerProducts()->get();
		}
	}

	public function changeOrderDisplayForProduct(Request $request){
		$place = $request->get('place');

		$vendorCode = $request->get('vendor_code');
		$pairedVendorCode = $request->get('paired_vendor_code');
		$showOrder  = $request->get('show_order');
		$pairedShowOrder  = $request->get('paired_show_order');

		$showProduct = ShowProduct::where(['vendor_code' => $vendorCode, 'place' => $place])->first();
		$showProduct->show_order = $showOrder;
		$showProduct->save();

		$showProduct = ShowProduct::where(['vendor_code' => $pairedVendorCode, 'place' => $place])->first();
		$showProduct->show_order = $pairedShowOrder;
		$showProduct->save();


		return ['success' => true];
	}

	public function search(Request $request) {
		$search = $request->get('search');
		$searchWithoutDots = str_replace('.', '', $search);

		$products = Product::where('name', 'like', "%$search%")
		                   ->orWhere('vendor_code', 'like', "%$searchWithoutDots%")
		                   ->get();

		return $products;
	}

	public function toggleVisibleProductWithCategory(Request $request) {
		$data = $request->only(['product_id', 'category_id', 'sub_category_id']);
		$visible = ProductsVisible::where(['product_id' => $data['product_id'], 'category_id' => $data['category_id'], 'sub_category_id' => $data['sub_category_id']])->first();
		
	if ($visible) {
			$visible->delete();
			return $visible;
		}

		$visible = ProductsVisible::create([
			'product_id' => $data['product_id'], 
			'category_id' => $data['category_id'], 
			'sub_category_id' => $data['sub_category_id']
			]);
		return $visible;
	}

	public function visibleProductWithCategoryLists(Request $request) {
		$productsVisibleGroupByCategory = ProductsVisible::all()->groupBy('category_id');
		
		$productsVisible = $productsVisibleGroupByCategory->mapWithKeys(function($item, $index) { 
				return [$index => $item->groupBy('sub_category_id')->mapWithKeys(function($item2, $index2) {
					return [$index2 => $item2->pluck('product_id')];
				})];
		});

		return $productsVisible;
	}
}
