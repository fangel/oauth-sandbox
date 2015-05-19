<?php

require 'AF/AF.php';
AF::setConfig(
	array(
		'db' => array(
			'master' => array(
				'dsn' => 'mysql:host=localhost;dbname=oauth-sandbox', // MySQL DSN style string
				'username' => 'root', // username
				'password' => '', // password
				'identifier' => 'localhost' // what is this server called (log purposes)
			)
		),
		/*'log' => array( // Well, we like a log
			'type' => 'AF_Log_Array',
			'params' => array( // Make it display itself at the bottom of all page-loads
				'register_shutdown' => true
			)
		)*/
	)
);

AF::bootstrap( AF::ALL );

session_start();

define('LIBRARY_DIR', dirname(__FILE__).'/');
define('MODELS_DIR', LIBRARY_DIR . '/models/');

define('WEB_URL', '');
define('PUBLIC_DIR', WEB_URL . '/public/');

require MODELS_DIR . 'User.php';
require MODELS_DIR . 'ApiConsumer.php';
require MODELS_DIR . 'ApiAccessToken.php';
require MODELS_DIR . 'ApiRequestToken.php';
require MODELS_DIR . 'ApiNonce.php';

require LIBRARY_DIR . 'OAuth.php';
require LIBRARY_DIR . 'DataStorage.php';

require LIBRARY_DIR . 'MessageStorage.php';
AF::registry()->messages = new MessageStorage();

if( isset($_SESSION['user']) ) {
	$t = new User_Table();
	AF::registry()->user = new $t->row_class($t, unserialize($_SESSION['user']), false);
} else {
	AF::registry()->user = false;
}

header("Content-Type: text/html; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
