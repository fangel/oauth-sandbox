<?php

require 'library/Sandbox.php';

$username = (isset($_POST['username'])) ? $_POST['username'] : NULL;
$kitten   = (isset($_POST['kitten']))   ? $_POST['kitten']   : NULL;
$signup   = (isset($_POST['signup']))   ? true               : false;
$logout   = (isset($_GET['logout']))    ? true               : false;

$table = new User_Table();

if( $logout ) {
	unset($_SESSION['user']);
	session_destroy();
} else {
	if( ! in_array( $kitten, array('able','baker','charlie','dog','easy','fox') ) ) {
		header( 'Location: http://' . $_SERVER['HTTP_HOST'] . WEB_URL . '/');
		die;
	}
	
	if( $signup ) {
		$existing = $table->find('username', $username, AF::NO_DELAY);
		if( ! $existing ) {
			$user = $table->create();
			$user->username = $username;
			$user->kitten   = $kitten;
			$user->save( AF::NO_DELAY );
		
			// Log the user in
			$_SESSION['user'] = serialize($user->toArray());
		} else {
			AF::registry()->messages->username_taken = true;
			
			if( isset($_POST['return']) && $_POST['return'] )
				AF::registry()->messages->login_return_url = preg_replace('/\p{C}/u','', $_POST['return']);
			header('Location: http://' . $_SERVER['HTTP_HOST'] . WEB_URL . '/');
			die;
		}
	} else {
		$user = reset($table->fetch('username = ? AND kitten = ?', array($username, $kitten), AF::DELAY_SAFE));
		if( $user ) {
			// Log the user in
			$_SESSION['user'] = serialize($user->toArray());
		} else {
			AF::registry()->messages->login_failed = true;
			
			if( isset($_POST['return']) && $_POST['return'] )
				AF::registry()->messages->login_return_url = preg_replace('/\p{C}/u','', $_POST['return']);
			header('Location: http://' . $_SERVER['HTTP_HOST'] . WEB_URL . '/');
			die;
		}
	}
}

$url = 'http://' . $_SERVER['HTTP_HOST'] . WEB_URL . '/';
if( isset($_POST['return']) && $_POST['return'] ) $url = preg_replace('/\p{C}/u','', $_POST['return']);

header("Location: " . $url);
die;

