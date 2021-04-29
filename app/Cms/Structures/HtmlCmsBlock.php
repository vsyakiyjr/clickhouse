<?php

namespace App\Cms\Structures;

class HtmlCmsBlock extends CmsBlock {
	public $type = 'html';
	
	public $icon = 'fa fa-code';
	
	public $label = 'HTML блок';
	
	public $template = 'textBlock.html';
}