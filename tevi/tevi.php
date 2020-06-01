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
class Tevi extends Model{

	/**
	* description
	*
	* @var type
	*
	* @access type
	*/
	var $object = null;
	

	/**
	* description
	*
	* @param
	*
	* @return
	*
	* @access
	*/
	function setSection($section) {

		$class = "\\App\\Tevi\\" . $section;

		$this->object = new $class();

		return $this->object::newInstance();
	}
	
}