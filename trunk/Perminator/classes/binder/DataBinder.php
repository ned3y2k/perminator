<?php
namespace classes\binder;

use classes\web\script\http\Request;

class DataBinder {
	private $setterPrefix;

	public function __construct($setterPrefix = "set") {
		$this->setterPrefix = $setterPrefix;
	}

	/**
	 *
	 * @param object $desInstance
	 * @param array $srcData Associative Arraay[GET, POST, SESSION, COOKIE, ETC...]
	 * @param string $setterPrefix
	 * @throws DataBindingException
	 */
	public function binding(&$desInstance, &$srcData) {
		if (!is_object($desInstance))
			throw new DataBindingException(
					"First Argument passed must be of type object.");

		$objectRef = new \ReflectionObject($desInstance);
		$props = $objectRef->getProperties();

		if ($srcData instanceof Request) {
			$requestValue = $srcData->getParameters();
			$this->instanceFromAssociativeArray($desInstance, $props, $requestValue);
		} elseif (is_array($srcData)) {
			$this->instanceFromAssociativeArray($desInstance, $props, $srcData);
		} else {
			throw new DataBindingException(
					"Second Argument Passed Must be of Type Associative Array.");
		}
	}

	private function instanceFromAssociativeArray(&$desInstance,
			&$reflectionProperties, $data) {
		foreach ($reflectionProperties as $prop) {
			/* @var $prop \ReflectionProperty */
			if ($prop->isPublic()) {
				if (array_key_exists($prop->name, $data))
					$prop->setValue($desInstance, $data[$prop->name]);
				else {
					$desInstance = null;
					break;
				}
			} else {
				$prop->setAccessible(true);
				$setterName = $this->createSetterNameFrom($prop->name);
				if($this->isExistSetter($setterName))
					$desInstance->$setterName($data[$prop->name]);
				elseif (array_key_exists($prop->name, $data))
					$prop->setValue($desInstance, $data[$prop->name]);
				else {
					$desInstance = null;
					break;
				}
				$prop->setAccessible(false);
			}
		}
	}

	private function createSetterNameFrom($fieldName) {
		$fieldName = trim ( $fieldName );
		$name = "set" . strtoupper ( substr ( $fieldName, 0, 1 ) );
		$name .= substr ( $fieldName, 1 );
		return $name;
	}

	private function isExistSetter($setterName) {
		// TODO 이쪽 부분 정의 필요
		return false;
	}
}
class DataBindingException {
}
?>