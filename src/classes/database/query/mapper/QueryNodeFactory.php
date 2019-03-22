<?php
/**
 * BITMOBILE
 * 작성자: Kyeongdae
 * 일자: 14. 9. 13
 * 시간: 오후 7:26
 */
namespace classes\database\query\mapper;

use classes\database\query\mapper\node\IQueryNode;

/**
 * Class QueryNodeFactory
 * @package classes\database\query\mapper
 */
class QueryNodeFactory {
    /**
     * @param string $name
     * @param array $attributes
     *
     * @return IQueryNode
     */
	public static function create($name, array $attributes = null) {
		$name = strtolower($name);
		$name = '\\' . __NAMESPACE__ . '\\' . 'node\\' . 'QueryNode' . ucwords($name);

		/** @var IQueryNode $instance */
		$instance = new $name();

		if ($attributes != null)
			$instance->setAttributes($attributes);

		return $instance;
	}
}