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

			case "search":
				return $this->getSearchJSON($_POST);
			break;

			case "event":
				return $this->getEventJSON($_POST["event"]);
			break;

			case "test":
				return $this->getSearchJSON([
//				"attack_type" => "Abortion Related",
//				"weapon_type" => "11",
//				"country" => "21",
//				"period" => "6",
//				"region" => "8",			
			]);
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
			"36"	=> "Last 3 years",
			"48"	=> "Last 4 years",
			"60"	=> "Last 5 years",
			"72"	=> "Last 6 years",
			"84"	=> "Last 7 years",
			"96"	=> "Last 8 years",
			"108"	=> "Last 9 years",
			"120"	=> "Last 10 years",
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
	function getVictimsTypes() {

		$periods = [
			"1"	=> "Fatal",
			"2"	=> "Non Fatal",
			"3"	=> "Suicide",
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
				"victims"		=> $this->getVictimsTypes(),
				"weapons"		=> $this->getWeaponTypes(),
				"countries"		=> $this->getCountries(),
				"regions"		=> $this->getRegions(),
				"attacks"		=> $this->getAttackTypes(),
				"periods"		=> $this->getPeriods()
			]
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
	function getSearchJSON($query) {
		
		
		//build the query
		//$query = $_POST;

		$sql = "SELECT * FROM attacks ";
		$cond = [];
		$params = [];

		if (isset($query["country"]) && $query["country"]) {
			$cond[] = " country = ? ";
			$params[] = [
				"type" => "d",
				"data" => $query["country"]
			];
		}

		if (isset($query["region"]) && $query["region"]) {
			$cond[] = " region = ? ";
			$params[] = [
				"type" => "d",
				"data" => $query["region"]
			];
		}

		if (isset($query["weapon_type"]) && $query["weapon_type"]) {
			$cond[] = " weapon_type = ? ";

			$params[] = [
				"type" => "d",
				"data" => $query["weapon_type"]
			];
		}

		if (isset($query["period"]) && $query["period"]) {
			$cond[] = " `date` >= ? ";

			$params[] = [
				"type" => "d",
				"data" => time() - $query["period"] * 31 * 24 * 3600
			];
		} else {
			$cond[] = " `date` >= ? ";

			$params[] = [
				"type" => "d",
				"data" => time() - 20 * 356 * 24 * 3600
			];
		}


		if (isset($query["attack_type"]) && $query["attack_type"]) {
			$cond[] = " attack_type = ? ";

			$params[] = [
				"type" => "s",
				"data" => $query["attack_type"]
			];
		}

//		debug($params,1);

		$cond2 = $cond;
		if (isset($query["victims"]) && $query["victims"]) {
		

			switch ($query["victims"]) {
				case "1":
					$cond2[] = " nkill > 0 ";
				break;

				case "2":
					$cond2[] = " nkill = 0 ";
				break;

				case "3":
					$cond2[] = " suicide > 0 ";
				break;
			}			
		}
		
		$results = $this->db->QFetchRowArray(
			"SELECT * from attacks "	. (count($cond2 ) ? " WHERE" . implode(" AND" , $cond2 ) : "") . " ",
			$params
		);
	
		
		$events = null;

		if (is_array($results)) {
			foreach ($results as $key => $val) {
				$events[$val["lat"] . "-" . $val["long"]] = [
					"event_id"	=> "xx",
					"lat"		=> $val["lat"],
					"long"		=> $val["long"],
				];
			}
			
			$results = null;
		}

		$graphResults = $this->db->QFetchRowArray(
			"SELECT * from attacks "	. (count($cond ) ? " WHERE" . implode(" AND" , $cond ) : "") . " ",
			$params
		);		

		return $this->Json([
			"status"	=> "success",
			"results"	=> array_values($events),
			"graphs"	=> $this->getGraphs($graphResults)
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
	function getEventJSON($event) {
		$data = $this->db->QFetchArray(
			"SELECT * FROM attacks WHERE event_id = ?",
			[
				["type"	=> "d",	"data" => $event]
			]
		);

		if (is_array($data)) {
			return $this->json([
				"status"	=> "success",
				"event"		=> $data
			]);
		} else {
			return $this->json([
				"status"	=> "error",
			]);
		}
		
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
	function getGraphs(&$results) {

		return [
			"graph_1"	=> $this->getGraph1($results),
			"graph_2"	=> $this->getGraph2($results),
			"graph_3"	=> $this->getGraph3($results),
			"graph_4"	=> $this->getGraph4($results)
		];
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
	function getGraph1(&$results) {

		$data = [
		];

		//$labels = ['Success' , 'Suicide' , 'Kills'];

		foreach ($results as $key => $val) {
			if (!isset($data["success"][$val['attack_type_text']])) {
				$data["success"][$val['attack_type_text']] = 0;
				$data["suicide"][$val['attack_type_text']] = 0;
				$data["nkill"][$val['attack_type_text']] = 0;
			}

			$data["success"][$val['attack_type_text']] += $val["success"];
			$data["suicide"][$val['attack_type_text']] += $val["suicide"];
			$data["nkill"][$val['attack_type_text']] += $val["nkill"];
			
		}

		$datasets = [];		

		$labels = array_keys($data["success"]);

		foreach ($data as $key => $val) {

			$datasets[] = [
				"fill"	=>  true , 
				"label"	=>  $key , 
				"data"	=> array_values($val),
				"backgroundColor"	=> $this->rand_color()
			];

			//$labels[] = $key;
		}

		return [
			"datasets"	=> $datasets,
			"labels"	=> $labels
		];
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
	function old_getGraph1(&$results) {

		$data = [
		];

		foreach ($results as $key => $val) {
			if (!isset($data[$val['attack_type_text']])) {
				$data[$val['attack_type_text']] = [
					"success" => 0,
					"suicide" => 0,
					"nkill" => 0,
				];
			}

			$data[$val['attack_type_text']]["success"] += $val["success"];
			$data[$val['attack_type_text']]["suicide"] += $val["suicide"];
			$data[$val['attack_type_text']]["nkill"] += $val["nkill"];
			
		}

		$datasets = [];
		$labels = ['Success' , 'Suicide' , 'Kills'];

		foreach ($data as $key => $val) {

			$datasets[] = [
				"fill"	=>  true , 
				"label"	=>  $key , 
				"data"	=> array_values($val),
				"backgroundColor"	=> $this->rand_color()
			];

			//$labels[] = $key;
		}

		return [
			"datasets"	=> $datasets,
			"labels"	=> $labels
		];
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
	function getGraph2(&$results) {

		$data = [
			'Success' => [],
			'Suicide' => [],
			'Kills' => [],
		];

		$labels = [];

		foreach ($results as $key => $val) {
			$year = strftime("%Y" , $val["date"]);
			if (!isset($data['Success'][$year])) {
				$data['Success'][$year] = 0;
				$data['Suicide'][$year] = 0;
				$data['Kills'][$year] = 0;
			}

			$data['Success'][$year] += $val["success"];
			$data['Suicide'][$year] += $val["suicide"];
			$data['Kills'][$year] += $val["nkill"];

			$labels[$year] = $year;
			
		}

		$datasets = [];

		foreach ($data as $key => $val) {
			//$labels[] = $key;

			$datasets[] = [
				"fill"	=>  true , 
				"label"	=>  $key , 
				"data"	=> array_values($val),
				"backgroundColor"	=> $this->rand_color()
			];

			//$labels[] = $key;
		}

		return [
			"datasets"	=> $datasets,
			"labels"	=> array_values($labels)
		];
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
	function getGraph3(&$results) {

		$data = [
			'Success' => [],
			'Suicide' => [],
			'Kills' => [],
		];

		$labels = [];

		foreach ($results as $key => $val) {
			$graphLabel = $val['region_text'];

			if (!isset($data['Success'][$graphLabel])) {
				$data['Success'][$graphLabel] = 0;
				$data['Suicide'][$graphLabel] = 0;
				$data['Kills'][$graphLabel] = 0;
			}

			$data['Success'][$graphLabel] += $val["success"];
			$data['Suicide'][$graphLabel] += $val["suicide"];
			$data['Kills'][$graphLabel] += $val["nkill"];

			$labels[$graphLabel] = $graphLabel;
			
		}

		$datasets = [];

		foreach ($data as $key => $val) {
			//$labels[] = $key;

			$datasets[] = [
				"fill"	=>  true , 
				"label"	=>  $key , 
				"data"	=> array_values($val),
				"backgroundColor"	=> $this->rand_color()
			];

			//$labels[] = $key;
		}

		return [
			"datasets"	=> $datasets,
			"labels"	=> array_values($labels)
		];
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
	function getGraph4(&$results) {

		$data = [
		];

		//$labels = ['Success' , 'Suicide' , 'Kills'];

		foreach ($results as $key => $val) {
			if (!isset($data[$val['attack_type_text']])) {
				$data[$val['attack_type_text']] = 0;
			}

			$data[$val['attack_type_text']] += $val["nkill"];
			
		}

		$datasets = [];		
		$labels = array_keys($data);

		$_data  = [];
		$_bg = [];

		foreach ($data as $key => $val) {

			$_data[] = $val;
			$_bg[] = $this->rand_color();

/*
			$datasets[] = [
				"fill"	=>  true , 
				"label"	=>  $key , 
				"data"	=> [$val],//array_values($val),
				"backgroundColor"	=> $this->rand_color()
			];
*/
			//$labels[] = $key;
		}

		return [
			"datasets"	=> [[
				"data"	=> $_data,
				"backgroundColor" => $_bg,
			]],
			"labels"	=> $labels
		];
	}


	private function rand_color() {
		return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
	}
	
}
