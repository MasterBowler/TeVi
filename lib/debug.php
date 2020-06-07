<?php

/**
* description
*
* @param
*
* @return
*
* @access
*/
function PrintR($array , $die = false) {
		echo "<table><tr><td><pre style=\"background-color:white; position: relative; z-index: 1000; font-size: 12px;\">";

	print_r($array);
		echo "</pre></td></tr></table>\n";
	
	if ($die)
		die();
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
function Debug($array , $die = false , $ip = "" , $level = 1) {

	if ($ip && $ip != $_SERVER["REMOTE_ADDR"]) {
		return "";
	}

	if ($ip && $ip != $_SERVER["REMOTE_ADDR"]) {
		return "";
	}

	$back = [];

    $data = debug_backtrace();
	for ($i = 0; $i < $level; $i++) {

		if (is_array($data[$i])) {
			$back[] = [
				"file"		=> $data[$i]["file"],
				"line"		=> $data[$i]["line"],
			];
		}				
	}
	
	PrintR($back);
	
	PrintR($array , $die);
}
