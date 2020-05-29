<?php


/**
* description
*
* @library	
* @author	
* @since	
*/

namespace App\Tevi;

class Database{

	/**
	* description
	*
	* @var type
	*
	* @access type
	*/
	var $conn;


	/**
	* description
	*
	* @param
	*
	* @return
	*
	* @access
	*/
	function __construct($server , $user , $pass , $database) {
		$this->conn = new \mysqli(
			$server,
			$user,
			$pass,
			$database
		);

		if ($this->conn->connect_errno) {
			throw new Exception($this->conn->connect_error);
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
	function query($sql) {
		global $base , $_USER , $_SESS; 
	}
	

	//uipxIj2n4NYjc8M9
	
	
}
