<?php

$data = [
	"status"	=> "error",
	"fields"	=> [
		"first-name"	=> "Introduceti numele de nefamilie",
		"last-name"	=> "Introduceti numele de familie",
	],
	"msg"		=> "Corectati erorile ca sa puteti triimte!"
];


header('Content-Type: application/json');
echo json_encode($data);
