<?php

/**
 *
 * 'type': 'html',
 * 'label': 'HTML блок',
 * 'title': 'Заголовок',
 * 'icon': 'fa fa-code',
 * 'template': 'textBlock.html',
 * 'content': 'Содержимое',
 * 'visible': true,
 * 'config': {}
 */

namespace App\Cms\Structures;

use App\Api\Structures\DataStructure;

abstract class CmsBlock extends DataStructure {
	/** @var string Заголовок блока */
	public $title;

	/** @var string Тип блока. Должен быть перегружен. */
	public $type;

	/** @var string CSS класс для иконки кнопки "добавить новый блок" в cms. Должен быть перегружен. */
	public $icon;

	/** @var  string Текст Tooltip'а для кнопки "добавить новый блок" в cms. Должен быть перегружен. */
	public $label;

	/** @var string Имя angular.js шаблона для отображения в cms */
	public $template;

	/** @var  string Содержимое блока */
	public $content;

	/** @var  bool Отображать на странице - да/нет */
	public $visible;

	/** @var string Принудительно сдвигать блок вверх (top) или вниз (bottom) */
	public $position;

	/** @var  array Параметры блока */
	public $config;

	public function seed($data) {
		$this->title   = empty($data['title'])   ? '' : $data['title'];
		$this->content = empty($data['content']) ? '' : $data['content'];
		$this->visible = (bool)$data['visible'] ?? true;
		$this->position = empty($data['position']) ? '' : $data['position'];

		$this->config = $data['config'] ?? [];

		if (is_string($this->config)) {
			$decodedConfig = json_decode($this->config, true);
			if (json_last_error() == JSON_ERROR_NONE) {
				$this->config = $decodedConfig;
			}
		}
	}

	/**
	 * Create block xml element
	 *
	 * @return \DOMElement
	 */
	public function toXml() {
		$xml = new \DOMDocument('1.0', 'UTF-8');

		$blockElement = $xml->createElement('block');
		$blockElement->appendChild($xml->createElement('type',  $this->type));
		$blockElement->appendChild($xml->createElement('title', html_entity_decode($this->title)));
		$blockElement->appendChild($xml->createElement('visible',  $this->visible));
		$blockElement->appendChild($xml->createElement('position', $this->position));

		$cdata = $xml->createCDATASection(html_entity_decode($this->content));
		$contentNode = $xml->createElement('content');
		$contentNode->appendChild($cdata);
		$blockElement->appendChild($contentNode);

		$configElement = $xml->createElement('config');
		if (!empty($this->config)) {
			$cdataConfig = $xml->createCDATASection(json_encode($this->config));
			$configElement->appendChild($cdataConfig);
		}
		$blockElement->appendChild($configElement);

		return $blockElement;
	}

	/**
	 * Create instance of requested cms block type
	 *
	 * @param       $type
	 *
	 * @param array $seedData
	 *
	 * @return CmsBlock
	 */
	static function create($type, array $seedData = null) {
		$type = str_replace('.', '\\', $type);
		$nestedLevels  = explode('.', $type);
		$classNamePart = implode('\\', array_map('ucfirst', $nestedLevels));

		//todo hardcoded path to class - maybe should find workaround for it
		$cmsBlockClass = 'App\\Cms\\Structures\\' . $classNamePart . 'CmsBlock';

		if (!class_exists($cmsBlockClass)) {
			throw new \InvalidArgumentException("No such CMS block: $type");
		}

		/** @var CmsBlock $requestedCmsBlock */
		$requestedCmsBlock = new $cmsBlockClass;
		if ($seedData) {
			$requestedCmsBlock->seed($seedData);
		}

		return $requestedCmsBlock;
	}
}