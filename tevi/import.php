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
class Import extends Model{


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

		$this->prepareTable();
		$this->processCSV();

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
	function processRecord($header , $data) {		

		$row = [];

		foreach ($header as $key => $val) {
			$row[$val] = $data[$key];
		}
		
		return $row;
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
	function processCSV() {

		//open the csv for reading
		$count = 0;
		if (($handle = fopen(Config::csv_database, "r")) !== FALSE) {
		
			$header = null;
			//set buffer to 10000, maximum number of line chars
			$query = $this->db->conn->stmt_init();

			while (($data = fgetcsv($handle, 10000, ",")) !== FALSE) {

				if ($header === null) {
					$header = $data;
				} else {

					$row = $this->processRecord($header , $data);

					//$query->reset();
					$query = $this->db->conn->stmt_init();
					$query->prepare("
						INSERT INTO attacks
						(
							`date`,
							`region`,
							`region_text`,
							`country`,
							`country_text`,
							`province`,
							`city`,
							`lat`,
							`long`,
							`success`,
							`suicide`,
							`attack_type`,
							`target_type`,
							`weapon_type`,
							`weapon_subtype`,
							`weapon_type_text`,
							`weapon_subtype_text`,
							`target_type_text`,
							`attack_type_text`,
							`nkill`,
							`gname`
						)

						VALUES
						( ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ?)
					");

					$date = mktime(0,0,0,$row['iday'] , $row['imonth'] , $row['iyear']);

					$query->bind_param(
						"iisisssddiiiiiissssds",
						$date,
						$row['region'],
						$row['region_txt'],
						$row['country'],
						$row['country_txt'],
						$row['provstate'],
						$row['city'],
						$row['latitude'],
						$row['longitude'],
						$row['success'],
						$row['suicide'],
						$row['attacktype1'],
						$row['attacktype1_txt'],
						$row['weaptype1'],
						$row['weapsubtype1'],
						$row['weaptype1_txt'],
						$row['weapsubtype1_txt'],
						$row['targtype1'],
						$row['targtype1_txt'],
						$row['nkill'],
						$row['gname']
					);				

					$query->execute();

					$count += 1;
				}
				

			}
			fclose($handle);

			$query->close();

			echo "Imported {$count} events.";

			return true;
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
	function prepareTable() {		

		//drop existing records
		$query = $this->db->conn->stmt_init();
		$query->prepare("SELECT * FROM  information_schema.tables WHERE table_schema = ? AND table_name = ?");
		$query->bind_param("ss" , Config::$mysql_database , Config::$table_attacks);
		$query->execute();

		$result = $query->get_result();

		if (!is_array($result->fetch_array(MYSQLI_ASSOC))) {
			$query->reset();
			$query->prepare("CREATE TABLE `attacks` (
				  `event_id` int(11) NOT NULL AUTO_INCREMENT,
				  `date` int(11) NOT NULL,
				  `region` int(11) DEFAULT NULL,
				  `region_text` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
				  `country` int(11) DEFAULT NULL,
				  `country_text` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
				  `province` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
				  `city` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
				  `lat` float DEFAULT NULL,
				  `long` float DEFAULT NULL,
				  `success` int(1) DEFAULT NULL,
				  `suicide` int(11) DEFAULT NULL,
				  `attack_type` int(11) DEFAULT NULL,
				  `target_type` int(11) DEFAULT NULL,
				  `weapon_type` int(11) DEFAULT NULL,
				  `weapon_subtype` int(11) DEFAULT NULL,
				  `weapon_type_text` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
				  `weapon_subtype_text` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
				  `target_type_text` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
				  `attack_type_text` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
				  `nkill` int(11) DEFAULT NULL,
				  PRIMARY KEY (`event_id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

			$query->execute();
		} else {
			$query->reset();
			//drop existing records
			$query->prepare("TRUNCATE TABLE attacks");
			$query->execute();
		}


		$query->close();



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
	function __init() {		

		//can take a lot of time to import the database;
		set_time_limit(0);

		parent::__init();
	}

	
}
