<?php
/**
 * BITMOBILE
 * 작성자: Kyeongdae
 * 일자: 2015-01-29
 * 시간: 오후 2:44
 */

namespace classes\api\model;


use classes\runtime\serialization\json\JSON;

class AllFieldJSONResultStringier implements IJSONResultStringier {
	private $iteratorMap = array();

	/**
	 * @param string            $fieldName
	 * @param callable $iterator
	 * @return $this
	 */
	public function addFieldExtractor($fieldName, callable $iterator) {
		$this->iteratorMap[$fieldName] = $iterator;
		return $this;
	}

	/**
	 * @param JSONResult $result
	 * @return string
	 * @throws \ReflectionException
	 */
	public function stringify(JSONResult $result) {
		return JSON::encode($result, $this->iteratorMap);
	}
}