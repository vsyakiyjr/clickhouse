<?php

/**
 * Use this in models to convert content attribute from XML to CmsBlock collection and vice versa
 */
namespace App\Models\Cms;

use App\Cms\Structures\CmsBlock;

trait ContentAttribute {
	/**
	 * Convert content value to SimpleXMLObject
	 *
	 * @param $content
	 *
	 * @return mixed
	 */
	function getContentAttribute($content) {
		try {
			$content = new \SimpleXMLElement($content, LIBXML_NOCDATA);
		} catch (\Exception $e) {
			$content = new \stdClass();
			$content->block = [];
		}

		$content = json_decode(json_encode($content), true);
		if (!empty($content['block'])) {
			$content['block'] = isset($content['block'][0]) ? $content['block'] : [$content['block']];
		} else {
			$content['block'] = [];
		}
		$cmsBlockCollection = [];

		foreach ($content['block'] as $block) {
			$cmsBlockCollection[] = CmsBlock::create($block['type'], $block);
		}

		$content['block'] = $cmsBlockCollection;

		return $content;
	}

	/**
	 * Convert content array to XML on save
	 *
	 * @param array $value
	 *
	 */
	function setContentAttribute(array $value) {
		$xml = new \DOMDocument('1.0', 'UTF-8');
		$rootElement = $xml->createElement('content');
		$xml->appendChild($rootElement);

		foreach ($value['block'] as $block) {
			$cmsBlock = CmsBlock::create($block['type']);

			$blockToSeed = $block instanceof CmsBlock ? $block->toArray() : $block;

			$cmsBlock->seed($blockToSeed);
			$blockNode = $cmsBlock->toXml();

			$rootElement->appendChild($xml->importNode($blockNode, true));
		}

		$this->attributes['content'] = $xml->saveXML();
	}
}