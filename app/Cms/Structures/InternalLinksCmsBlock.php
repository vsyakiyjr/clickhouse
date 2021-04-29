<?php

namespace App\Cms\Structures;


class InternalLinksCmsBlock extends CmsBlock  {
	public $type = 'internalLinks';

	public $icon = 'fa fa-list-alt';

	public $label = 'Ссылки на внутренние страницы';

	public $template = 'internalLinks.html';

}