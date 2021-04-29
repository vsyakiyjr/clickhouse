<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

abstract class Request extends FormRequest {

	/**
	 * Localization keys for custom attribute names for the validator
	 * @var array
	 */
	protected $attributeLocalizationKeys = [];


	/**
	 * Get the validator instance for the request.
	 *
	 * @return Validator
	 */
	protected function getValidatorInstance(){
		$attributeNames = [];
		foreach ($this->attributeLocalizationKeys as $attrKey => $attrLocalizationKey) {
			$attributeNames[$attrKey] = '"' . trans("request.attribute_names.$attrLocalizationKey") . '"';
		}

		return parent::getValidatorInstance()->setAttributeNames($attributeNames);
	}


	/**
	 * Get a subset containing the provided keys with values from the input not null data.
	 *
	 * @param  array|mixed  $keys
	 * @return array
	 */
	public function onlyNotNull($keys){
		return Arr::where($this->only($keys), function ($value, $key) {
			return !is_null($value);
		});
	}



	/**
	 * Handle a failed validation attempt.
	 *
	 * @param Validator $validator
	 *
	 * @return void
	 *
	 * @throws ValidationException
	 */
	protected function failedValidation(Validator $validator)
	{
		throw (new ValidationException($validator))
			->errorBag($this->errorBag)
			->redirectTo($this->getRedirectUrl());
	}
}
