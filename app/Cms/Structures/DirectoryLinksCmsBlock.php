<?php

namespace app\Cms\Structures;

use App\Models\Cms\Directory;
use App\Models\Cms\Page;

class DirectoryLinksCmsBlock extends CmsBlock {
	public $type     = 'directoryLinks';

	public $icon     = 'fa fa-list';

	public $label    = 'Перелинковка';

	public $template = 'pageLinks.html';

	/** @var  Page[] */
	public $pages;
	
	/** @var  Directory directory to display links from */
	public $directory;

	function seed($data) {
		parent::seed($data); 
		
		if (isset($this->config['directory_id'])) {
			/** @var Directory directory */
			$this->directory = Directory::find($this->config['directory_id']);

			$loadedForCms = request()->is('cms/*');

			// do not load pages for cms editor to greatly speed up loading
			if ($loadedForCms) {
				return;
			}

			$pagination = 25;
			$this->config['text_cut_1_col'] = $this->config['text_cut_1_col'] ?? 300;
			$this->config['text_cut_2_col'] = $this->config['text_cut_2_col'] ?? 125;
			$this->config['pages'] = $this->config['pages'] ?? 8;
			$this->config['show_right_block'] = $this->config['show_right_block'] ?? true;

			$this->config['links_in_right_block'] = $this->config['links_in_right_block'] ?? 20; //looks fine as default

			$this->pages = isset($this->config['paginated']) ?
				$this->directory->pagePreviews()->paginate($pagination) :
				$this->directory->pagePreviews()
				                ->with(['parent'])
				                ->take($this->config['links_in_right_block'] + $this->config['pages'])
				                ->get();
		}
	}
}