<?php

namespace App\Tevi;

use \App\Tevi\Config;

class Model{

	var $db;

	function __init() {


		$this->db = new Database(
			Config::$mysql_server,
			Config::$mysql_user,
			Config::$mysql_pass,
			Config::$mysql_database
		);

	}


	static function newInstance() {
		return new static();
	}

	function JSON($data , $headers = true) {

		if ($headers) {
			header('Content-Type: application/json');
		}
		
		echo json_encode($data , JSON_THROW_ON_ERROR);
		die();

	}
	
}
