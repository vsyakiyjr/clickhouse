<?php

namespace App\Api\Structures;

use ArrayAccess;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

abstract class DataStructure implements  Arrayable, Jsonable, JsonSerializable, ArrayAccess{

	const ORIGINAL_KEYS = null;
	const UPPER_KEYS = 1;
	const LOWER_KEYS = 2;
	const SNAKE_KEYS = 3;
	const CAMEL_KEYS = 4;

	/**
	 * The fields that should be hidden in serialization.
	 *
	 * @var array
	 */
	protected $hidden = [];

	/**
	 * The hidden fields that should be visible for developers.
	 *
	 * @var array
	 */
	protected $visibleForDevelopers = [];

	function __construct($data = null) {
		if ($data){
			$this->seed($data);
		}
	}

	abstract function seed($data) ;


	/**
	 * @return array
	 */
	protected function getHidden(){
		$user = \Auth::user();
		$hidden = array_merge($this->hidden ?? [], ['hidden', 'visibleForDevelopers', 'localCache']);

		if(
			!empty($this->visibleForDevelopers)
			&&
			(
				($user && $user->belongsToGroup('developer'))
				||
				config('app.debug')
			)
		){
			return array_diff($hidden, $this->visibleForDevelopers);
		}

		return $hidden;
	}

	/**
	 * Add hidden attributes for the structure.
	 *
	 * @param  array|string|null  $attributes
	 * @return void
	 */
	public function addHidden($attributes = null)
	{
		$attributes = is_array($attributes) ? $attributes : func_get_args();

		$this->hidden = array_merge($this->hidden, $attributes);
	}

	/**
	 * Make the given, typically hidden, attributes visible.
	 *
	 * @param  array|string  $attributes
	 * @return $this
	 */
	public function makeVisible($attributes)
	{
		$this->hidden = array_diff($this->hidden, (array) $attributes);
		// todo: may be should use "HidesAttributes" trait and method "getArrayableItems" from "HasAttributes" trait

		return $this;
	}

	/**
	 * Get the instance as an array.
	 *
	 *
	 * @return array
	 */
	public function toArray() {
		// todo: use only public properties
		$array = get_object_vars($this);

		$transformedArray = array_except($array, $this->getHidden());

		return $transformedArray;
	}

	/**
	 * @param null $transformType
	 *
	 * @return array
	 */
	public function toArrayTransformed($transformType = null) {
		return $this->transformKeys($this->toArray(), $transformType);
	}

	function transformKeys(array $inputArray, $transformType){
		switch ($transformType) {
			case self::UPPER_KEYS :{
				$transformFunction = 'mb_strtoupper';
				break;
			}
			case self::LOWER_KEYS:{
				$transformFunction = 'mb_strtolower';
				break;
			}
			case self::CAMEL_KEYS:{
				$transformFunction = 'camel_case';
				break;
			}
			case self::SNAKE_KEYS:{
				$transformFunction = 'snake_case';
				break;
			}
			case self::ORIGINAL_KEYS:
			default:{
				return $inputArray;
			}
		}

		$transformedArray = [];

		foreach ($inputArray as $key => $value) {
			$transformedArray[$transformFunction($key)] = $value;
		}

		return $transformedArray;
	}

	/**
	 * Convert the object into something JSON serializable.
	 *
	 * @return array
	 */
	public function jsonSerialize()
	{
		return $this->toArray();
	}

	/**
	 * Convert the object to its JSON representation.
	 *
	 * @param  int $options
	 *
	 * @return string
	 */
	function toJson($options = 0) {
		$options = $options | JSON_UNESCAPED_UNICODE;
		return json_encode($this->jsonSerialize(), $options);
	}

	/**
	 * Whether a offset exists
	 * @link http://php.net/manual/en/arrayaccess.offsetexists.php
	 *
	 * @param mixed $offset <p>
	 * An offset to check for.
	 * </p>
	 *
	 * @return boolean true on success or false on failure.
	 * </p>
	 * <p>
	 * The return value will be casted to boolean if non-boolean was returned.
	 * @since 5.0.0
	 */
	public function offsetExists($offset) {
		return isset($this->$offset);
	}

	/**
	 * Offset to retrieve
	 * @link http://php.net/manual/en/arrayaccess.offsetget.php
	 *
	 * @param mixed $offset <p>
	 * The offset to retrieve.
	 * </p>
	 *
	 * @return mixed Can return all value types.
	 * @since 5.0.0
	 */
	public function offsetGet($offset) {
		return $this->$offset;
	}

	/**
	 * Offset to set
	 * @link http://php.net/manual/en/arrayaccess.offsetset.php
	 *
	 * @param mixed $offset <p>
	 * The offset to assign the value to.
	 * </p>
	 * @param mixed $value <p>
	 * The value to set.
	 * </p>
	 *
	 * @return void
	 * @since 5.0.0
	 */
	public function offsetSet($offset, $value) {
		$this->$offset = $value;
	}

	/**
	 * Offset to unset
	 * @link http://php.net/manual/en/arrayaccess.offsetunset.php
	 *
	 * @param mixed $offset <p>
	 * The offset to unset.
	 * </p>
	 *
	 * @return void
	 * @since 5.0.0
	 */
	public function offsetUnset($offset) {
		$this->$offset = null;
	}

	/**
	 * Check if structure is empty
	 *
	 * @return bool
	 */
	public function isEmpty(){

		$fields = array_filter($this->toArray());

		if(empty($fields)){
			return true;
		}


		$isEmpty = true;

		foreach($fields as $field){
			if(
				( $field instanceof DataStructure && !$field->isEmpty()) ||
				( !($field instanceof DataStructure) && !empty($field))
			){
				$isEmpty = false;
				break;
			}
		}

		return $isEmpty;

	}


	/**
	 * Защита от записи непредусмотренных параметров
	 */
	public function __set($name, $value) {}
	public function __get($name) {}

}
