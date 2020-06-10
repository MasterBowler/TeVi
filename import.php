<?php

include "lib/autoload.php";

use \App\Tevi\Tevi;

Tevi::newInstance()
	->setSection("import")
	->Run();

