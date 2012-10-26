<?php

namespace classes\binder;

use classes\web\script\http\Request;

class DataBinder {
	private $setterPrefix;
	private $incomplete = true;
	private $keyNamePrefix;
	public function __construct($keyNamePrefix = "", $setterPrefix = "set") {
		if (is_string ( $setterPrefix ))
			$this->setterPrefix = $setterPrefix;
		else
			throw new \InvalidArgumentException ( 'setter prefix는 string type만 허용' );

		if (is_string ( $keyNamePrefix ))
			$this->keyNamePrefix = $keyNamePrefix;
		else
			throw new \InvalidArgumentException ( 'variable name prefix는 string type만 허용' );
	}

	/**
	 *
	 * @param object $desInstance
	 * @param array $srcData
	 *        	Associative Arraay[GET, POST, SESSION, COOKIE, ETC...]
	 * @param string $setterPrefix
	 * @throws DataBindingException
	 */
	public function binding(&$desInstance, &$srcData, $incomplete = true) {
		if (! is_object ( $desInstance ))
			throw new DataBindingException ( "First Argument passed must be of type object." );

		if (is_bool ( $incomplete ))
			$this->incomplete = $incomplete;
		else
			throw new \InvalidArgumentException ( 'incomplete 값은 bool type만 허용' );

		$objectRef = new \ReflectionObject ( $desInstance );
		$props = $objectRef->getProperties ();

		if ($srcData instanceof Request) {
			$requestValue = $srcData->getParameters ();
			$this->instanceFromAssociativeArray ( $desInstance, $props, $requestValue );
		} elseif (is_array ( $srcData )) {
			$this->instanceFromAssociativeArray ( $desInstance, $props, $srcData );
		} else {
			throw new DataBindingException ( "Second Argument Passed Must be of Type Associative Array." );
		}
	}
	private function instanceFromAssociativeArray(&$desInstance, &$reflectionProperties, $data) {
		$keyNamePrefix = $this->keyNamePrefix;
		foreach ( $reflectionProperties as $prop ) {
			/* @var $prop \ReflectionProperty */

			$keyName = $this->keyNamePrefix . $prop->name;
			if ($prop->isPublic ()) {
				if (array_key_exists ( $keyName, $data ))
					$prop->setValue ( $desInstance, $data [$keyName] );
				elseif (! $this->incomplete) {
					$desInstance = null;
					break;
				}
			} else {
				$prop->setAccessible ( true );
				$setterName = $this->createSetterNameFrom ( $prop->name );
				if ($this->existSetter ( $setterName, $desInstance ) && array_key_exists ( $keyName, $data )) {
					$desInstance->$setterName ( $data [$prop->name] );
				} elseif (array_key_exists ( $keyName, $data ))
					$prop->setValue ( $desInstance, $data [$keyName] );
				elseif (! $this->incomplete) {
					$desInstance = null;
					break;
				}
				$prop->setAccessible ( false );
			}
		}
	}
	private function createSetterNameFrom($fieldName) {
		$fieldName = trim ( $fieldName );
		$name = "set" . strtoupper ( substr ( $fieldName, 0, 1 ) );
		$name .= substr ( $fieldName, 1 );
		return $name;
	}
	private function existSetter($setterName, $desInstance) {
		$refObject = new \ReflectionObject ( $desInstance );
		return $refObject->hasMethod ( $setterName );
	}
}
class DataBindingException {
}
?>