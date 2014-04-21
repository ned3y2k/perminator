<?php
namespace classes\web\bind\meta;

class BeanAttributeContainer implements \IteratorAggregate {
	private $attributes = array();

	public function addBeanAttribute(BeanAttribute $attribute) {
		$this->attributes[$attribute->name] = $attribute;
	}

	public function getIterator() {
		return new \ArrayIterator($this->attributes);
	}
}