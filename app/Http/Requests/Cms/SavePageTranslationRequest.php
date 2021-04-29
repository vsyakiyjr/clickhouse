<?php

namespace App\Http\Requests\Cms;

use App\Http\Requests\Request;

class SavePageTranslationRequest extends Request {
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
			'id'                => ['nullable', 'integer',],
			'page_id'           => ['required', 'integer',],
			'locale'            => ['required', 'string',],
			'title'             => ['required', 'string',],
			'description'       => ['required', 'string',],
			'keywords'          => ['nullable', 'string',],
			'breadcrumbs_title' => ['nullable', 'string',],
			'content'           => ['required',],
			'text_preview'      => ['nullable', 'string',],
		];
	}
}
