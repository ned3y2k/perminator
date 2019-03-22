<?php
namespace classes\util\object;

use classes\filter\IFilter;
use classes\io\exception\DirectoryNotFoundException;
use classes\io\exception\PermissionException;

/**
 * Class ObjectBuilderUtil
 * @package classes\util\object
 */
class ObjectBuilderUtil {
	/** @var string 클래스명 */
	private $className;

	/**
	 * @var IFilter[]
	 */
	private $filters = array();

	/**
	 * @var AbstractObjectBuilderCache
	 */
	private $objectBuilderCache;

	/**
	 * ObjectBuilderUtil constructor.
	 * @param string $className
	 * @throws \ReflectionException
	 * @throws DirectoryNotFoundException
	 * @throws PermissionException
	 */
	public function __construct($className = '\stdClass') {
		$this->className = $className;

		$this->objectBuilderCache = ObjectBuilderCacheManager::get($className);
		if ($this->objectBuilderCache === null) {
			$reflectionClass = new \ReflectionClass ($className);

			$propertyNames       = array();
			$privateFieldNames   = array();
			$protectedFieldNames = array();
			$visibleFieldNames   = array();

			$properties = $reflectionClass->getProperties();
			foreach ($properties as $property) {
				/* @var $property \ReflectionProperty */
				$fieldName = $property->name ? $property->name : $property->getName();

				$setter = $this->createSetterNameFrom($fieldName);
				$getter = $this->createGetterNameFrom($fieldName);

				if ($this->isExistsMethod($setter) && $this->isExistsMethod($getter)) {
					$propertyNames[ $fieldName ] = array('setter' => $setter, 'getter' => $getter, 'private' => $property->isPrivate());
				} elseif ($this->isExistsMethod($setter) && !$this->isExistsMethod($getter)) {
					$propertyNames[ $fieldName ] = array('setter' => $setter, 'getter' => null, 'field' => $fieldName, 'private' => $property->isPrivate());
				} elseif (!$this->isExistsMethod($setter) && $this->isExistsMethod($getter)) {
					$propertyNames[ $fieldName ] = array('setter' => null, 'getter' => $getter, 'field' => $fieldName, 'private' => $property->isPrivate());
				} else {
					if ($property->isPrivate()) {
						$privateFieldNames [ $fieldName ] = 0;
					} elseif ($property->isProtected() && !$this->isExistsMethod($setter) && !$this->isExistsMethod($getter)) {
						$protectedFieldNames [ $fieldName ] = 0;
					} elseif ($property->isProtected() && $this->isExistsMethod($setter) && $this->isExistsMethod($getter)) {
						$propertyNames[ $fieldName ] = array('setter' => $setter, 'getter' => $getter);
					} else $visibleFieldNames [ $fieldName ] = 0;
				}
			}
			if (count($privateFieldNames) != 0) {
				$this->objectBuilderCache = ObjectBuilderCacheManager::create($className, $propertyNames, array_merge($privateFieldNames, $protectedFieldNames), $visibleFieldNames);
			} else {
				$this->objectBuilderCache = ObjectBuilderCacheManager::create($className, $propertyNames, array(), array_merge($visibleFieldNames, $protectedFieldNames));
			}
		}

		if (!is_object($this->objectBuilderCache)) throw new \LogicException('objectBuilderCache unknown error.');
	}

	/**
	 * @param string  $key
	 * @param IFilter $filter
	 * @return $this
	 */
	public function addFilter($key, IFilter $filter) {
		$this->filters [ $key ] = $filter;
		return $this;
	}

	/**
	 * 겟터명을 생성
	 * @param string $fieldName
	 * @return string
	 */
	private function createGetterNameFrom($fieldName) {
		$fieldName = trim($fieldName);
		$name      = "getParent" . strtoupper(substr($fieldName, 0, 1));
		$name .= substr($fieldName, 1);

		return $name;
	}

	/**
	 * 셋터명을 생성
	 * @param string $fieldName
	 * @return string
	 */
	private function createSetterNameFrom($fieldName) {
		$fieldName = trim($fieldName);
		$name      = "set" . strtoupper(substr($fieldName, 0, 1));
		$name .= substr($fieldName, 1);

		return $name;
	}

	/**
	 * 메서드 존재 여부 체크
	 * @param $setterName
	 * @return bool
	 * @throws \ReflectionException
	 */
	private function isExistsMethod($setterName) {
		if ($this->className != '\stdClass') {
			$ref = new \ReflectionClass ($this->className);

			return $ref->hasMethod($setterName);
		}

		return false;
	}

	/**
	 * @param array $associativeArray
	 * @param mixed  $instance
	 * @return null|\stdClass
	 */
	public function build(array $associativeArray, $instance = null) {
		if ($instance === null || $instance === false) $instance = new $this->className ();

		if (!is_object($instance)) throw new \LogicException('instance unknown error.');

		if($instance !== null) $this->objectBuilderCache->setObject($instance);

		$instance = $this->objectBuilderCache->getObject();

		$processedList = array();
		$processedList = $this->bindFieldsAndProperties($associativeArray, $processedList);

		/** @var IFilter[] $nonProcessedFilterList */
		if($instance === null) { // 기존 참고 인스턴스가 없을때만 사용한다. (필터에서 기본값을 검사하거나 넣어주는 행위)
			$nonProcessedFilterList = array_diff_key($this->filters, $processedList);
			$emptyPropertiesAndFields = array_fill_keys(array_keys($nonProcessedFilterList), null);
			$this->bindFieldsAndProperties($emptyPropertiesAndFields, $processedList);
		}

		return clone $instance;
	}

	/**
	 * @param array $associativeArray
	 * @param       $processedList
	 *
	 * @return array
	 */
	protected function bindFieldsAndProperties(array $associativeArray, $processedList) {
		foreach ($associativeArray as $name => $value) {
			if (is_numeric($name)) continue;

			if (array_key_exists($name, $this->filters)) $value = $this->filters [ $name ]->doFilter($value);

			if ($this->objectBuilderCache->isField($name)) {
				$this->objectBuilderCache->setField($name, $value);
			} elseif ($this->objectBuilderCache->isInvisibleField($name)) {
				$this->objectBuilderCache->setInVisibleFiled($name, $value);
			} elseif ($this->objectBuilderCache->isProperty($name)) {
				$this->objectBuilderCache->setProperty($name, $value);
			}

			$processedList[ ] = $name;
		}

		return $processedList;
	}
}