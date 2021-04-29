<?php

namespace app\Cms\Structures;

class FormCmsBlock extends CmsBlock{
	public $type = 'form';

	public $icon = 'fa fa-search';

	public $label = 'Форма поиска';
	
	public $template = 'searchForm.html';
}