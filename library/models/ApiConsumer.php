<?php

class ApiConsumer_Table extends AF_Table {
	public function __construct() {
		parent::__construct('api_consumers', 'id');
	}
	
	public function create() {
		// Create a new Consumer with a random key and secret
		$obj = parent::create();
		$obj->key = substr(md5(uniqid(rand(), true)),0,16);
		$obj->secret = substr(md5(uniqid(rand(), true)),0,28);
		return $obj;
	}
}