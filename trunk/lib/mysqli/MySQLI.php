<?php
namespace lib\mysqli;

class MySQLI extends \MySQLI {
	public function prepare($query) {
		return new MySQLIStatement($this, $query);
	}
}