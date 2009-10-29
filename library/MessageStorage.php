<?php

class MessageStorage {
	private $session_key = 'message_storage_messages';
	private $messages = array();
	private $pending_messages = array();
	
	public function __construct( $session_key = 'message_storage_messages' ) {
		$this->session_key = $session_key;
		$this->messages = (isset($_SESSION[$this->session_key])) ? unserialize($_SESSION[$this->session_key]) : array();
		$_SESSION[$this->session_key] = serialize(array());
	}
	
	public function __isset( $message ) {
		return isset($this->messages[$message]);
	}
	
	public function __get( $message ) {
		if( isset($this->messages[$message]) ) {
			return $this->messages[$message];
		} else {
			return false;
		}
	}
	
	public function __set( $message, $value ) {
		$this->messages[$message] = $value;
		$this->pending_messages[$message] = $value;
		$_SESSION[$this->session_key] = serialize($this->pending_messages);
	}
}