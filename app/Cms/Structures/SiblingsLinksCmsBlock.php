<?php

namespace App\Cms\Structures;

use App\Models\Cms\Directory;
use App\Models\Cms\Page;

class SiblingsLinksCmsBlock extends CmsBlock {
	public $type = 'siblingsLinks';

	public $icon = 'fa fa-list';

	public $label = 'Перелинковка';

	public $template = 'pageLinks.html';

	/** @var  Page[] */
	public $previousPages;

	/** @var  Page[] */
	public $nextPages;

	/** @var  Directory directory to display links from */
	public $page;
	
	function seed($data) {
		parent::seed($data);

		$limit = $this->config['limit'] ?? 4;

		if (isset($this->config['page_id'])) {
			/** @var Directory directory */
			$this->page = Page::find($this->config['page_id']);
		} elseif($this->config['page'] instanceof Page) {
			$this->page = $this->config['page'];
			unset($this->config['page']);
		}

		$this->previousPages = $this->page->siblings('<', $limit);
		$this->nextPages     = $this->page->siblings('>', $limit);
	}
}