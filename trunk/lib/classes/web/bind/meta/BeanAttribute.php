<?php
namespace classes\web\bind\meta;

class BeanAttribute {
	public $initMethod;
	public $name;
	public $destroyMethod;

	public function __construct($name) {
		if($name) throw new \InvalidArgumentException("name is null");
		$this->name = $name;
	}
}

