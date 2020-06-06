<?php

namespace App\Tevi;

use \App\Tevi\Database;
use \App\Tevi\Config;
use \App\Tevi\Model;

/**
* description
*
* @library	
* @author	
* @since	
*/
class Api extends Model{

	/**
	* description
	*
	* @param
	*
	* @return
	*
	* @access
	*/
	function run() {		
		$this->__init();

		switch ($_GET['action']) {

			case "filters":
				return $this->getFiltersJSON();
			break;
		}

		return $this->json([
			"status"	=> "error" , 
			"message"	=> "Unknown Request"
		]);
	}
	/**
	* description
	*
	* @param
	*
	* @return
	*
	* @access
	*/
	function getAttackTypes() {

		$types = $this->db->QFetchRowArray("SELECT DISTINCT attack_type_text FROM attacks order by attack_type_text ASC");
		$response = [];

		if (is_array($types)) {
			foreach ($types as $key => $val) {
				$response[] = [
					"value" => $val["attack_type_text"],
					"title" => $val["attack_type_text"],
				];
			}			
		}

		return $response;
	}
	
	/**
	* description
	*
	* @param
	*
	* @return
	*
	* @access
	*/
	function getWeaponTypes() {

		$types = $this->db->QFetchRowArray("SELECT DISTINCT weapon_type_text , weapon_type FROM attacks order by weapon_type_text ASC");
		$response = [];

		if (is_array($types)) {
			foreach ($types as $key => $val) {
				$response[] = [
					"value" => $val["weapon_type"],
					"title" => $val["weapon_type_text"],
				];
			}			
		}

		return $response;
		
	}

	/**
	* description
	*
	* @param
	*
	* @return
	*
	* @access
	*/
	function getCountries() {

		$types = $this->db->QFetchRowArray("SELECT DISTINCT country , country_text FROM attacks order by country_text ASC");
		$response = [];

		if (is_array($types)) {
			foreach ($types as $key => $val) {
				$response[] = [
					"value" => $val["country"],
					"title" => $val["country_text"],
				];
			}			
		}

		return $response;
		
	}


	/**
	* description
	*
	* @param
	*
	* @return
	*
	* @access
	*/
	function getRegions() {

		$types = $this->db->QFetchRowArray("SELECT DISTINCT region , region_text FROM attacks order by region_text ASC");
		$response = [];

		if (is_array($types)) {
			foreach ($types as $key => $val) {
				$response[] = [
					"value" => $val["region"],
					"title" => $val["region_text"],
				];
			}			
		}

		return $response;
		
	}

	/**
	* description
	*
	* @param
	*
	* @return
	*
	* @access
	*/
	function getPeriods() {

		$periods = [
			"1"	=> "Last month",
			"6"	=> "Last 6 months",
			"12"	=> "Last year",
			"24"	=> "Last 2 years",
		];

		$response = [];

		if (is_array($periods)) {
			foreach ($periods as $key => $val) {
				$response[] = [
					"value" => $key,
					"title" => $val,
				];
			}			
		}

		return $response;
	}
	


	/**
	* description
	*
	* @param
	*
	* @return
	*
	* @access
	*/
	function getFiltersJSON() {

		return $this->Json([
			"status"		=> "success",
			"options" => [
				"weapons"		=> $this->getWeaponTypes(),
				"countries"		=> $this->getCountries(),
				"regions"		=> $this->getRegions(),
				"attacks"		=> $this->getAttackTypes(),
				"periods"		=> $this->getPeriods()
			]
		]);

	}
	
	
	
}
