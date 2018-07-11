<?php
namespace classes\runtime\serialization\json;

use classes\lang\ObjectUtil;


/**
 * Class UserExtractorContext
 *
 * Bitmobile System Corp.
 * 작성자: Kyeongdae
 * 일자: 2015-04-02
 * 시간: 오후 11:05
 *
 * @package classes\runtime\serialization\json
 */
class UserExtractorContext {
	/** @var \Closure[] */
	private $comparators = array();


	/**
	 * @param string   $name
	 * @param \Closure $func
	 *
	 * @return callable
	 * @throws \ReflectionException
	 */
	public function getComparator($name, \Closure $func) {
		if (array_key_exists($name, $this->comparators))
			return $this->comparators[$name];

		$ref = new \ReflectionFunction($func);
		$params = $ref->getParameters();
		$paramCount = count($params);

		if (count($params) != 2)
			throw new \InvalidArgumentException("invalid user value extract closure. two parameters are required.");

		$typeName = ObjectUtil::select($params[0], array('getClass', 'getName'));

		if ($params[0]->isArray()) {
			$this->comparators[$name] = function ($owner) {
				return is_array($owner);
			};
		} elseif ($typeName != null) {
			$this->comparators[$name] = function ($owner) use ($typeName) {
				return $owner != null && is_object($owner) && is_a($owner, $typeName);
			};
		} else {
			$this->comparators[$name] = function ($owner) use ($typeName) {
				return true;
			};
		}
		unset($typeName, $ref, $params, $paramCount, $ownerTypeName);

		return $this->comparators[$name];
	}
}

/**
 * @param mixed  $value
 * @param string $typeName
 *
 * @return bool
 */
function ownerTypeCompare($value, $typeName) {
	return
		$typeName == 'array' && is_array($value)
		|| $typeName == 'bool' && is_bool($value)
		|| $typeName == 'int' && is_int($value)
		|| $typeName == 'object' && is_object($value)
		|| $typeName == 'is_numeric' && is_numeric($value)
		|| $typeName == 'scalar' && is_scalar($value)
		|| $typeName == 'string' && is_string($value)
		|| is_a($value, $typeName);
}