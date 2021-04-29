<?php

namespace App\Http\Requests\Cms;

use App\Http\Requests\Request;

class PageRequest extends Request {
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			'alias'             => [
				'required',
				'alpha_dash',
//				'unique_with:cms__pages,parent_directory',
				'max:255'
			],
			'parent_directory'  => [
				'required',
				'string',
				'exists:cms__directories,fullpath',
			],
			'content'           => [
				'required',
			],
			'breadcrumbs_title' => [
				"nullable",
				'string',
				'max:255'
			],
			'title'             => [
				'required',
				'max:255'
			],
			'description'       => [
				'required',
				'max:1000',
			],
			'enabled'           => [
				'required',
			],
			'image_path'        => [
				"nullable",
				'string'
			],
			'priority'          => [
				'required',
				'digits_between:1,500',
			],
			'force_noindex' => [
				'required',
				'boolean'
			],
			'sitemap_priority'  => [
				"nullable",
				'numeric',
				'max:1',
				'min:0.01',
			],
			'news_date' => [
				'nullable',
				'date'
			],
			'host' => [
				'required',
				'string'
			]
		];
	}
}
