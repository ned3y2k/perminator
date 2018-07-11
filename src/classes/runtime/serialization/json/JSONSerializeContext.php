<?php
/**
 * Bitmobile System Corp.
 * 작성자: Kyeongdae
 * 일자: 2015-04-02
 * 시간: 오후 10:52
 */

namespace classes\runtime\serialization\json;


/**
 * Class JSONSerializeContext
 *
 * @package classes\runtime\serialization\json
 */
class JSONSerializeContext {
	/** @var \ReflectionProperty[][] */
	private $propertyReflectorsMap = array();
	/** @var \ReflectionClass[] */
	private $classReflectionMap = array();

	/** @var UserExtractorContext */
	private $userExtractorContext;

	public function __construct() {
		$this->userExtractorContext = new UserExtractorContext();
	}


	/**
	 * @param $className
	 * @return \ReflectionProperty[]
	 * @throws \ReflectionException
	 */
	public function getPropertyReflectors($className) {
		if (!array_key_exists($className, $this->propertyReflectorsMap)) {
			$reflection = $this->getClassReflection($className);

			$properties = array();
			foreach ($reflection->getProperties() as $property) {
				if (!$property->isPublic() || !$property->isDefault()) {
					$property->setAccessible(true);
				}

				$properties[] = $property;
			}

			$this->propertyReflectorsMap[$className] = $properties;
		}

		return $this->propertyReflectorsMap[$className];
	}

	/**
	 * @param string $className
	 * @return bool
	 * @throws \ReflectionException
	 */
	public function isInternal($className) { return $this->getClassReflection($className)->isInternal(); }

	/**
	 * @param string $className
	 * @return \ReflectionClass
	 * @throws \ReflectionException
	 */
	private function getClassReflection($className) {
		if (!array_key_exists($className, $this->classReflectionMap)) {
			$this->classReflectionMap[$className] = new \ReflectionClass($className);
		}

		return $this->classReflectionMap[$className];
	}

	/** @return UserExtractorContext */
	public function getUserExtractorContext() { return $this->userExtractorContext; }
}