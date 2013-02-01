<?php
namespace lib\mysqli;

class FieldMeta {
	public $orgName;
	public $name;
	public $type;
	public $orgTable;
	public $table;

	function __set($name, $value) {
		if($name == "type") {
			$explode = explode('(', $value);
			$this->type = array($explode[0], substr($explode[1], -1));
		} else {
			$this->$name = $value;
		}
	}
}