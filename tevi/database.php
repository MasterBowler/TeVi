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

		$this->conn->set_charset ( "utf8" );

	}

	//https://stackoverflow.com/questions/3681262/php5-3-mysqli-stmtbind-params-with-call-user-func-array-warnings
	function refValues($arr) { 
		$refs = array();

		foreach ($arr as $key => $value) {
			$refs[$key] = &$arr[$key]; 
		}

		return $refs; 
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

		if (is_array($params) && count($params)) {
			$query = $this->conn->stmt_init();
			$query->prepare($sql);

			$types = "";
			$data = [];
			foreach ($params as $key => $val) {
				$types .= $val["type"];
				$data[] = $val["data"];
			}

			$finalParams = [$types];
			foreach ($data as $key => $val) {
				$finalParams[] = $val;
			}
			
			call_user_func_array(array($query, "bind_param"), $this->refValues($finalParams)); 

			$query->execute();
			$result = $query->get_result();
		} else {
			$result = $this->conn->query($sql);
		}

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

		$results = [];
		
		while ($data = $result->fetch_array(MYSQLI_ASSOC)) {
			
			$results[] = $data;
		}

		return count($results) ? $results : null;

	}
	
	
}
