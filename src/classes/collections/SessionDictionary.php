<?php
namespace classes\collections;
use classes\lang\ArrayUtil;

load_lib('func/request');

class SessionDictionary implements IDictionary {
	public function __construct() { if(!request_session_started()) @session_start(); }
	public function __set($key, $value) { $_SESSION[$key] = $value; }
	public function __get($key) { return ArrayUtil::getValue($_SESSION, $key); }
	public function __isset($key) { return isset($_SESSION) && is_array($_SESSION) && array_key_exists($key, $_SESSION);}
	public function __unset($key) { unset($_SESSION[$key]); }
}