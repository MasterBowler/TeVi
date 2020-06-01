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
class Contact extends Model{

	/**
	* description
	*
	* @var type
	*
	* @access type
	*/
	var $fields = [
		"email"	=> [
			"type" => FILTER_VALIDATE_EMAIL,
			"error" => "Enter a valid email address!",
			"filter"	=> FILTER_SANITIZE_EMAIL
		],

		"last-name"		=> [
			"error"		=> "Enter your last name!",
			"filter"	=> FILTER_SANITIZE_STRING
		],

		"first-name"	=> [
			"error"		=> "Enter your first name!",
			"filter"	=> FILTER_SANITIZE_STRING
		],

		"university"	=> [
			"error"		=> "Enter your university!",
			"filter"	=> FILTER_SANITIZE_STRING
		],

		"class"	=> [
			"error"		=> "Enter your class!",
			"filter"	=> FILTER_SANITIZE_STRING
		],


		"message"		=> [
			"error"		=> "Enter your message!",
			"filter"	=> FILTER_SANITIZE_STRING
		]
	];

	/**
	* description
	*
	* @var type
	*
	* @access type
	*/
	var $messages = [
		"errors"	=> "Fix errors!",
		"success"	=> "Message Sent!",
		"system"	=> "Unknown error, try again later!"
	];
	


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

		$this->validate();
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
	function validate() {

		$errors = null;
		$data = null;

		foreach ($this->fields as $key => $field) {

			if (isset($field["type"])) {
				if (!(isset($_POST[$key]) && $data[$key] = filter_var($_POST[$key]  , $field["type"]))) {
					$errors[$key] = $field["error"];
				}				
			} elseif (!(isset($_POST[$key]) && $data[$key] = filter_var($_POST[$key] , $field["filter"]))) {
				$errors[$key] = $field["error"];
			}				
		}

		if (is_array($errors)) {

			$this->headers();

			echo json_encode([
				"status"	=> "error",
				"fields"	=> $errors,
				"msg"		=> $this->messages['errors']
			]);

			return 1;
		}

		$status = @mail(
			Config::$contact_to,
			Config::$contact_subject,

			"First Name:" . $data["first-name"] . "\r\n" .
			"Last Name:" . $data["last-name"] . "\r\n" .
			"University:" . $data["university"] . "\r\n" .
			"Class:" . $data["class"] . "\r\n" .
			"Email:" . $data["email"] . "\r\n" .
			"Message:" . $data["message"] . "\r\n" ,

			'From: ' . $data['email'] . "\r\n" .
			'Reply-To: ' . $data['email'] . "\r\n" .
			'X-Mailer: PHP/' . phpversion()
		);


		if ($status) {
			echo json_encode([
				"status"	=> "ok",
				"msg"		=> $this->messages['success']
			]);
		} else {
			echo json_encode([
				"status"	=> "error",
				"msg"		=> $this->messages['system']
			]);
		}
		
		return 1;				
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
	function headers() {

		header('Content-Type: application/json; charset=utf-8');
	}
	
	
}
