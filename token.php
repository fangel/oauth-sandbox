<?php

require 'library/Sandbox.php';

if( ! AF::registry()->user ) { header( 'Location: ' . WEB_URL . '/' ); die; }

$delete = (isset($_GET['delete'])) ? true              : false;
$id     = (isset($_GET['id']))     ? (int) $_GET['id'] : NULL;

if( ! $id ) { header( 'Location: ' . WEB_URL . '/' ); die; }

$access_token_table = new ApiAccessToken_Table();
$access_token = $access_token_table->get( $id, AF::DELAY_SAFE );

if( $access_token->userid != AF::registry()->user->id ) { header( 'Location: ' . WEB_URL . '/' ); die; }

if( $delete ) {
	$access_token_table->query('DELETE FROM :table WHERE :primary = ?' , $access_token->id, AF::NO_DELAY );
}

header( 'Location: ' . WEB_URL . '/' );
