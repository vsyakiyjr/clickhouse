<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\Cms\PageRedirect;
use Illuminate\Http\Request;

class PagesRedirectsController extends Controller {
	public function index() {
		return view('cpanel.redirects', [
			'class'         => '',
			'title'         => 'Редиректы',
			'angularModule' => 'app.redirects',
		]);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @param Request $request
	 *
	 * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
	 */
	public function list(Request $request) {
		$filters = json_decode($request->get('filters'), true);
		$searchField = $filters['search'];
		if ($searchField){
			$paginator = PageRedirect::where('alias', 'like', "%$searchField%");

			return $paginator->with('page:id,fullpath,title')->orderBy('id', 'desc')->paginate(100);
		}

		return PageRedirect::with('page:id,fullpath,title')->orderBy('id', 'desc')->paginate(100);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request
	 *
	 * @return PageRedirect
	 */
	public function store(Request $request) {
		$pageRedirect = PageRedirect::create($request->all());
		
		return $pageRedirect;
	}

	/**
	 * Display the specified resource.
	 *
	 * @param PageRedirect $pageRedirect
	 *
	 * @return PageRedirect
	 */
	public function show(PageRedirect $pageRedirect) {
		return $pageRedirect;
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param Request      $request
	 * @param PageRedirect $pageRedirect
	 *
	 * @return PageRedirect
	 */
	public function update(Request $request, PageRedirect $pageRedirect) {
		$pageRedirect->fill($request->all());
		$pageRedirect->save();

		return $pageRedirect;
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param PageRedirect $pageRedirect
	 *
	 * @return array
	 * @throws \Exception
	 */
	public function destroy(PageRedirect $pageRedirect) {
		$success = $pageRedirect->delete();

		return ['success' => $success];
	}
}
