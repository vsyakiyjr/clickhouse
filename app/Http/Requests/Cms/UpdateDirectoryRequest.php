<?php

namespace App\Http\Requests\Cms;

use App\Http\Requests\Request;

class UpdateDirectoryRequest extends Request {
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
			'fullpath'         => [
				'required',
				'string',
			],
			'parent_directory' => [
				'required',
				'exists:cms__directories,fullpath',
			],
			'description'      => [
				'required',
				'string',
			],
			'description_uk'      => [
				'nullable',
				'string',
			],
			'description_en'      => [
				'nullable',
				'string',
			],
		];
	}
}
