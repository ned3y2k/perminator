<?php
/**
 * BITMOBILE
 * 작성자: Kyeongdae
 * 일자: 14. 7. 1
 * 시간: 오후 12:28
 */

namespace classes\util\object;


abstract class AbstractObjectBuilderCache {
	protected $propertyNames = array();
	protected $fieldNames = array();
	protected $inVisibleFieldNames = array();
	/** @var \stdClass */
	protected $object;
	/** @var \ReflectionClass */
	protected $refClass;

	public function setObject($object) {
		if (!is_object($object)) throw new \InvalidArgumentException('not object');
		$this->object = $object;
	}

	public function getObject() { return $this->object; }

	public function isField($name) { return in_array($name, $this->fieldNames); }

	public function isInvisibleField($name) { return in_array($name, $this->inVisibleFieldNames); }

	public function isProperty($name) { return array_key_exists($name, $this->propertyNames); }

	public function setField($name, $value) { $this->object->$name = $value; }

	public abstract function setInVisibleFiled($name, $value);

	public function setProperty($name, $value) {
		$setterName = $this->propertyNames[ $name ][ 'setter' ];
		$this->object->$setterName($value);
	}
} 