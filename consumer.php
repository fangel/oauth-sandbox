<?php

require 'library/Sandbox.php';

if( ! AF::registry()->user ) {
	header( 'Location: ' . WEB_URL . '/' );
	die;
}

$issue    = (isset($_GET['issue']))   ? true               : false;
$enable   = (isset($_GET['enable']))  ? true               : false;
$disable  = (isset($_GET['disable'])) ? true               : false;
$name     = (isset($_POST['name']))   ? $_POST['name']     : NULL;
$id       = (isset($_GET['id']))      ? (int) $_GET['id']  : NULL;

$table = new ApiConsumer_Table();

if( $issue ) {
	$consumer = $table->create();
	$consumer->userid = AF::registry()->user->id;
	$consumer->name = preg_replace('/\p{C}/u','', $name);
	$consumer->check_nonce = 1;
	$consumer->save();
	AF::registry()->messages->created_consumer = $consumer->id;
}

if( $enable || $disable ) {
	$consumer = $table->get($id);
	if( $consumer ) {
		$consumer->check_nonce = ($enable) ? 1 : 0;
		$consumer->save();
		AF::registry()->messages->consumer_changed = $consumer->id;
	}
}

header( 'Location: ' . WEB_URL . '/' );
die;