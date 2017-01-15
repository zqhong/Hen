<?php

use Hen\App;

define('PATH_ROOT', dirname(__DIR__));

require_once implode(DIRECTORY_SEPARATOR, [PATH_ROOT, 'vendor', 'autoload.php']);

$app = new App();