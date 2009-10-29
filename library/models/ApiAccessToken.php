<?php

class ApiAccessToken_Table extends AF_Table {
	public function __construct() {
		parent::__construct('api_access_tokens', 'id');
	}
	
	public function create() {
		// Create a new Access-Token with a random token and secret
		$obj = parent::create();
		$obj->key = substr(md5(uniqid(rand(), true)),0,16);
		$obj->secret = substr(md5(uniqid(rand(), true)),0,28);
		return $obj;
	}
}