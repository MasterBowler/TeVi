<?php

namespace App\Tevi;

use \App\Tevi\Database;
use \App\Tevi\Config;
use \App\Tevi\Model;

class Tevi extends Model{

	var $object = null;
	
	function setSection($section) {

		$class = "\\App\\Tevi\\" . $section;

		$this->object = new $class();

		return $this->object::newInstance();
	}
	
}
