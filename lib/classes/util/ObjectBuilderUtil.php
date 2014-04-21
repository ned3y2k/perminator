<?php
namespace classes\util;

use classes\filter\IFilter;
class ObjectBuilderUtil {
	private $className;

	/**
	 * @var \ReflectionProperty
	 */
	private $propertyList = array ();

	/**
	 * @var \ReflectionMethod
	 */
	private $setterList = array ();

	/**
	 * @var IFilter
	 */
	private $filters = array ();

	public function __construct($className = '\stdClass') {
		$this->className = $className;
		$reflectionClass = new \ReflectionClass ( $className );

		$properties = $reflectionClass->getProperties ();
		foreach ( $properties as $property ) {
			/* @var $property \ReflectionProperty */
			$fieldName = $property->name ? $property->name : $property->getName ();

			if ($this->existSetter ( $setter = $this->createSetterNameFrom ( $fieldName ) )) {
				$ref = new \ReflectionClass ( $className );
				$this->setterList [$fieldName] = $ref->getMethod ( $setter );
			} else {
				$this->propertyList [$fieldName] = $property;
			}
		}
	}

	public function addFilter($key, IFilter $filter) {
		$this->filters [$key] = $filter;
	}

	private function createSetterNameFrom($fieldName) {
		$fieldName = trim ( $fieldName );
		$name = "set" . strtoupper ( substr ( $fieldName, 0, 1 ) );
		$name .= substr ( $fieldName, 1 );
		return $name;
	}

	private function existSetter($setterName) {
		if ($this->className != '\stdClass') {
			$ref = new \ReflectionClass ( $this->className );
			return $ref->hasMethod ( $setterName );
		}

		return false;
	}

	public function build(array $associativeArray, $instance = null) {
		if (is_null ( $instance ) || $instance === false) $instance = new $this->className ();

		foreach ( $associativeArray as $name => $value ) {
			if (array_key_exists ( $name, $this->filters )) $value = $this->filters [$name]->doFilter ( $value );

			if (array_key_exists ( $name, $this->setterList )) $this->viaSetter ( $name, $instance, $value );
			elseif (array_key_exists ( $name, $this->propertyList )) $this->directSet ( $name, $instance, $value );
		}

		return $instance;
	}

	private function directSet($name, $instance, $value) {
		$protected = ! $this->propertyList [$name]->isPublic ();
		if ($protected) $this->propertyList [$name]->setAccessible ( true );
		$this->propertyList [$name]->setValue ( $instance, $value );
		if ($protected) $this->propertyList [$name]->setAccessible ( false );
	}

	private function viaSetter($name, $instance, $value) {
		$this->setterList [$name]->invoke ( $instance, $value );
	}
}