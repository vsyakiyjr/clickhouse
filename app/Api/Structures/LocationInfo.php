<?php
namespace App\Api\Structures;

class LocationInfo extends DataStructure {
	public $city;

	public $country_name;

	public $country_code;

	function seed($data) {
		$this->city = $data['city'] ?? '';
		$this->country_name = $data['country_name'] ?? '';
		$this->country_code = $data['country_code'] ?? 'UA';
	}
}
