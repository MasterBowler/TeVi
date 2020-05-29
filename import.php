<?php

include "lib/autoload.php";
include "lib/debug.php";

use \App\Tevi\Import;

Import::newInstance()
	->Run();

