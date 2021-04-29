<?php

namespace App\Cms\Structures;

class TextCmsBlock extends CmsBlock{
	public $type = 'text';
	
	public $icon = 'fa fa-file-text-o';

	public $label = 'Текстовый блок';

	public $template = 'textBlock.html';
}