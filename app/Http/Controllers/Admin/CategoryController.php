<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Subcategory;
use http\Exception;
use Illuminate\Http\Request;

class CategoryController extends Controller {
	/**
	 * Display a listing of active resources.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function lists(Request $request) {
		$sort = $request->sortBy ? $request->sortBy : 'id';
		$order = $request->descending ? ('false' == $request->descending ? 'asc' : 'desc') : 'asc';

		$query = Category::orderBy($sort, $order);

		return $query->get();
	}

	public function subcategories(Request $request) {
		$sort = $request->sortBy ? $request->sortBy : 'id';
		$order = 'asc';

		$query = Subcategory::with('category')->orderBy($sort, $order);

		return $query->get();
	}

	public function discount(Request $request) {
		if ($request->category_id && $request->discount) {
			Category::find($request->category_id)->update(['discount' => $request->discount]);
		}

		return ['success' => true];
	}

	public function changeVisible(Request $request) {
		try {
			$category = Category::find($request->id);
			$category->visible = $request->visible;
			$category->save();
		} catch (Exception $e) {
			return [
				'id'      => $request->id,
				'visible' => $request->visible,
				'error'   => $e->getMessage(),
			];
		}

		return [
			'success' => true,
			'id'      => $request->id,
			'visible' => $request->visible,
		];
	}

	public function changeVisibleSubcategories(Request $request) {
		try {
			$category = Subcategory::find($request->id);
			$category->visible = $request->visible;
			$category->save();
		} catch (Exception $e) {
			return [
				'id'      => $request->id,
				'visible' => $request->visible,
				'error'   => $e->getMessage(),
			];
		}

		return [
			'success' => true,
			'id'      => $request->id,
			'visible' => $request->visible,
		];
	}
}
