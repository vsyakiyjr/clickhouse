<?php

/**
 * Use this in models to convert content attribute from XML to CmsBlock collection and vice versa
 */
namespace App\Models\Cms;

trait DetailsAttribute {
	function getDetailsAttribute($details) {
		try {
			$preparedDetails = json_decode(json_encode(new \SimpleXMLElement($details, LIBXML_NOCDATA), JSON_UNESCAPED_UNICODE), true);
		} catch (\Exception $e) {
			$preparedDetails = [];
		}

		$result = [];
		foreach ($this->detailsAttributes as $detailName => $detailDesc) {
			$result[$detailName] = [
				'label' 	=> $detailDesc['label'],
				'visible' 	=> empty($preparedDetails[$detailName]['visible']) ? false : filter_var($preparedDetails[$detailName]['visible'], FILTER_VALIDATE_BOOLEAN),
				'raw_value' => empty($preparedDetails[$detailName]['raw_value']) ? '' : $this->castAttributeValue($preparedDetails[$detailName]['raw_value'], $detailDesc['type']),
			];

			$result[$detailName]['value'] = $result[$detailName]['raw_value'];
		}

		return $result;
	}

	protected function castAttributeValue($value, $type){
		switch ($type) {
			case 'bool': {
				$filterType = FILTER_VALIDATE_BOOLEAN;
				break;
			}
			case 'integer' :
			case 'int' :{
				$filterType = FILTER_VALIDATE_INT;
				break;
			}
			case 'float' :
			case 'decimal' :
			case 'double' :{
				$filterType = FILTER_VALIDATE_FLOAT;
				break;
			}
			default:{
				return (string)$value;
			}
		}

		return filter_var($value, $filterType);

	}

	function setDetailsAttribute ($details) {
		$xml = new \DOMDocument('1.0', 'UTF-8');
		$rootElement = $xml->createElement('details');

		foreach ($this->detailsAttributes as $detailName => $detailDesc) {
			$detailElem = $xml->createElement($detailName);
			$detailElem->appendChild($xml->createElement('raw_value', empty($details[$detailName]['raw_value']) ? '' : $details[$detailName]['raw_value']));
			$detailElem->appendChild($xml->createElement('visible', empty($details[$detailName]['visible']) ? true : $details[$detailName]['visible']));

			$rootElement->appendChild($detailElem);
		}
		$xml->appendChild($rootElement);

		$this->attributes['details'] = $xml->saveXML();
	}
}