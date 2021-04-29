<?php

namespace App\Exceptions\Cms;

use App\Exceptions\AjaxException;

class ExistingPageException extends AjaxException
{
	protected $defaultCode		= 'PAGE_PARAMS_NOT_UNIQUE';
	protected $defaultMessage	= 'Страница с такими параметрами уще существует!';
}
