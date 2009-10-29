<?php

class User_Table extends AF_Table {
	public function __construct() {
		parent::__construct('users', 'id');
	}
}