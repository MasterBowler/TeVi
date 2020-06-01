<?php

include "lib/autoload.php";
include "lib/debug.php";

use \App\Tevi\Tevi;

Tevi::newInstance()
	->setSection("import")
	->Run();

