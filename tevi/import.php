<?php

namespace App\Tevi;

use \App\Tevi\Database;
use \App\Tevi\Config;
use \App\Tevi\Model;

class Import extends Model{

	function run() {		

		$this->__init();

		$this->prepareTable();
		$this->processCSV();

		return true;
	}

	function processCSV() {

		//open the csv for reading
		$count = 0;
		if (($handle = fopen(Config::csv_database, "r")) !== FALSE) {
		
			$header = null;
			//set buffer to 10000, maximum number of line chars
			$query = $this->db->conn->stmt_init();

			while (($data = fgetcsv($handle, 100000, ",")) !== FALSE) {

				if ($header === null) {
					$header = $data;
				} else {

					$row = @array_combine($header , $data);

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
							`gname`,
							`nwound`,
							`summary`

						)

						VALUES
						( ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ? , ?)
					");

					$date = mktime(1,0,0,$row['imonth'] , $row['iday'] , $row['iyear']);

					$query->bind_param(
						"iisisssddiiiiiissssdsds",
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
						$row['gname'],
						$row['nwound'],
						$row['summary'],
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
				  `region_text` varchar(255) DEFAULT NULL,
				  `country` int(11) DEFAULT NULL,
				  `country_text` varchar(255) DEFAULT NULL,
				  `province` varchar(255) DEFAULT NULL,
				  `city` varchar(255) DEFAULT NULL,
				  `lat` float DEFAULT NULL,
				  `long` float DEFAULT NULL,
				  `success` int(1) DEFAULT NULL,
				  `suicide` int(11) DEFAULT NULL,
				  `attack_type` int(11) DEFAULT NULL,
				  `target_type` int(11) DEFAULT NULL,
				  `weapon_type` int(11) DEFAULT NULL,
				  `weapon_subtype` int(11) DEFAULT NULL,
				  `weapon_type_text` varchar(255) DEFAULT NULL,
				  `weapon_subtype_text` varchar(255) DEFAULT NULL,
				  `target_type_text` varchar(255) DEFAULT NULL,
				  `attack_type_text` varchar(255) DEFAULT NULL,
				  `nkill` int(11) NOT NULL,
				  `gname` varchar(255) NOT NULL,
				  `nwound` int(11) NOT NULL,
				  `summary` text NOT NULL,
				  PRIMARY KEY (`event_id`),
				  KEY `date` (`date`),
				  KEY `lat` (`lat`),
				  KEY `long` (`long`),
				  KEY `nkill` (`nkill`),
				  KEY `suicide` (`suicide`),
				  KEY `country` (`country`),
				  KEY `region` (`region`),
				  KEY `weapon_type` (`weapon_type`),
				  KEY `weapon_type_text` (`weapon_type_text`),
				  KEY `attack_type` (`attack_type`),
				  KEY `attack_type_text` (`attack_type_text`),
				  KEY `country_text` (`country_text`),
				  KEY `region_text` (`region_text`)
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
	

	function __init() {		

		//can take a lot of time to import the database;
		set_time_limit(0);

		parent::__init();
	}

	
}
