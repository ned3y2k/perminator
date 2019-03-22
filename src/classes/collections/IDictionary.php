<?php

namespace classes\collections;

interface IDictionary {
	public function __set($key, $value);
	public function __get($key);
	public function __isset($key);
	public function __unset($key);
}
