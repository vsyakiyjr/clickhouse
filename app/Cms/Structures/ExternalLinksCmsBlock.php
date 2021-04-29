<?php

namespace App\Cms\Structures;

class ExternalLinksCmsBlock extends CmsBlock  {
	public $type = 'externalLinks';

	public $icon = 'fa fa-list-alt';

	public $label = 'Внешние ссылки';

	public $template = 'sidebarLinks.html';

}
