<?php

// Uncomment this line if you must temporarily take down your site for maintenance.
// require '.maintenance.php';
// absolute filesystem path to the web root
define('WWW_DIR', dirname(__FILE__));

// absolute filesystem path to the application root
define('APP_DIR', WWW_DIR . '/../app');

// absolute filesystem path to the libraries
define('LIBS_DIR', WWW_DIR . '/../libs');

// absolute filesystem path to the libraries
define('UPLOAD_DIR', WWW_DIR . '/upload');

define("THUMBS_DIR", UPLOAD_DIR . "/thumbs");

$container = require __DIR__ . '/../app/bootstrap.php';

$container->getService('application')->run();
