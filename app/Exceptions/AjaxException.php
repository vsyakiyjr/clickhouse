<?php

namespace App\Exceptions;

/**
 * Class AjaxException
 * Используется, когда необходимо вернуть ошибку на AJAX запрос.
 *
 *
 *
 *
 * @package App\Exceptions
 */
class AjaxException extends \Exception {

	/** @var  string  code of error */
	protected $code;

	/** @var string Default error message   */
	protected $defaultMessage = '';

	/** @var int Default error code for this type of errors  */
	protected $defaultCode = 'ERROR';

	/** @var bool  */
	protected $needReport = false;

	/**
	 * AjaxException constructor.
	 *
	 * @param string          $message
	 * @param string          $code
	 * @param \Exception|null $previous
	 */
	public function __construct($message = null, $code = null, \Exception $previous = null) {

		$message = $message ?? $this->getDefaultMessage();
		$code = $code ?? $this->defaultCode;

		// Parent constructor accepts only integer code. Used code 144 (just some number)
		parent::__construct($message, 144, $previous);

		// Code property is overridden to string code after parents constructor
		$this->code = $code;
	}

	/**
	 * @return string
	 *
	 */
	protected function getDefaultMessage(){
		if(empty($this->defaultCode)){
			return $this->defaultMessage;
		}

		$langKey = 'exceptions.'.$this->defaultCode;

		$defaultMessage = trans($langKey);

		if($defaultMessage == $langKey){
			return $this->defaultMessage;
		}

		return $defaultMessage;

	}

	public function getErrorArray(){
		return [
			"error" => [
				"message" => $this->getMessage(),
				"code" => $this->getCode()
			]
		];
	}

	public function needReport(){
		return $this->needReport;
	}
}
