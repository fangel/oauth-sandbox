<?php

require 'library/Sandbox.php';


$tpl = AF::template();

if( AF::registry()->user ) {
	$tpl->user = AF::registry()->user;

	$consumer_table = new ApiConsumer_Table();
	$consumers = $consumer_table->fetch('userid = ?', AF::registry()->user->id, AF::DELAY_SAFE);
	$tpl->consumers = $consumers;
	
	$access_token_table = new ApiAccessToken_Table();
	$access_tokens = $access_token_table->fetch('userid = ?', AF::registry()->user->id, AF::DELAY_SAFE);
	if( $access_tokens ) {
		$consumers = array();
		foreach( $access_tokens AS $token ) $consumers[] = $token->consumerid;
		$needed_consumers = $consumer_table->fetch('id IN (' . implode(',',$consumers) . ')', array(), AF::DELAY_SAFE);
		foreach( array_keys($access_tokens) AS $i ) $access_tokens[$i]->consumer = $needed_consumers[$access_tokens[$i]->consumerid];
	}
	$tpl->access_tokens = $access_tokens;
	
	$tpl->display('templates/index_logged_in.phtml');
} else {
	$login_failed = AF::registry()->messages->login_failed;
	$username_taken = AF::registry()->messages->username_taken;
	
	$tpl->login_reason     = AF::registry()->messages->login_reason;
	$tpl->login_return_url = AF::registry()->messages->login_return_url;
	
	$tpl->failed = $login_failed || $username_taken ;
	$tpl->reason = ($login_failed) ? 'login' : 'signup';
	
	$tpl->display('templates/index_no_user.phtml');
}
