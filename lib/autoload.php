<?php

spl_autoload_register(function ($class_name) {

	$path = str_replace("\\" , "/" , strtolower(str_ireplace("App\\" , "" , $class_name)) . ".php");

	if (file_exists($path)) {
		include_once $path;
	} 

});


error_reporting(E_ALL);
ini_set("display_errors" , true);

