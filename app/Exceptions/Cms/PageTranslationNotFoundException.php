<?php

namespace App\Exceptions\Cms;
use App\Exceptions\AjaxException;


class PageTranslationNotFoundException extends AjaxException {

	protected $defaultCode		= 'PAGE_TRANSLATION_NOT_FOUND';
	protected $defaultMessage	= 'Перевод страницы не найден!';


}