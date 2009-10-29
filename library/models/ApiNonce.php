<?php

class ApiNonce_Table extends AF_Table {
	public function __construct() {
		parent::__construct('api_nonces', 'id');
	}
}