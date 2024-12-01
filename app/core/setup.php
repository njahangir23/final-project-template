<?php

//require our files, remember should be relative to index.php
require '../app/core/Router.php';
require '../app/models/Model.php';
require '../app/models/Recommendations.php';
require '../app/models/User.php';
require '../app/controllers/Controller.php';
require '../app/controllers/MainController.php';
require '../app/controllers/UserController.php';
require '../app/controllers/RecommendationController.php';


//set up env variables
$env = parse_ini_file('../.example-env');

define('DBNAME', $env['DBNAME'] ?? 'musicrecords');
define('DBHOST', $env['DBHOST'] ?? '127.0.0.1');
define('DBUSER', $env['DBUSER'] ?? 'root');
define('DBPASS', $env['DBPASS'] ?? '');
define('DBDRIVER', '');

//set up other configs
define('DEBUG', true);