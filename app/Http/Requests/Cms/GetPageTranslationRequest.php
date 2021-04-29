<?php

namespace App\Http\Requests\Cms;

use App\Http\Requests\Request;

class GetPageTranslationRequest extends Request {
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
			'page_id'             => [
				'required',
				'integer',
			],
			'locale'  => [
				'required',
				'string'
			],
		];
	}
}
