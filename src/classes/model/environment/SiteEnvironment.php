<?php
namespace classes\model\environment;

class SiteEnvironment {
	public $name;
	public $value;

	public function __construct() {
		if(func_num_args() == 2) {
			$this->name = func_get_arg(0);
			$this->value = func_get_arg(1);
		}
	}
}