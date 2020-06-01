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
	function query($sql, $params = null) {

		$query = $this->conn->stmt_init();
		$query->prepare($sql);

		if (is_array($params) && count($params)) {
			foreach ($params as $key => $val) {
				$query->bindParam($key , $val);
			}			
		}

		$query->execute();

		$result = $query->get_result();

		return $result;
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
	function qFetchArray($sql ,$params = null) {

		$result = $this->Query($sql , $params);

		return $result->fetch_array(MYSQLI_ASSOC);

		
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
	function qFetchRowArray($sql , $params = null) {

		$result = $this->Query($sql , $params);

		$results = null;
		
		while ($data = $result->fetch_array(MYSQLI_ASSOC)) {
			$results[] = $data;
		}

		return $results;

	}
	
	
}
