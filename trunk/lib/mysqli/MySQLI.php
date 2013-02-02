<?php
namespace lib\mysqli;

class MySQLI extends \mysqli {
	public function prepare($query) {
		return new MySQLIStatement($this, $query);
	}
}