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

		$this->test();
		return true;
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
	function test() {
		global $base , $_USER , $_SESS; 

		debug($this->db->QFetchRowArray("SELECT * FROM attacks LIMIT 2"),1);
	}
	
	
}
