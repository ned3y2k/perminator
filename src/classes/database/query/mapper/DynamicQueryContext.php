<?php
/**
 * BITMOBILE
 * 작성자: Kyeongdae
 * 일자: 14. 9. 14
 * 시간: 오후 3:42
 */
namespace classes\database\query\mapper;

use classes\database\conf\mapper\DynamicQueryConf;
use classes\database\query\mapper\cache\ICacheManager;
use classes\lang\ArrayUtil;

/**
 * 쿼리를 평가하기 위하여 변수를 가지고 있는 공간
 * Class DynamicQueryContext
 * @package classes\database\query\mapper
 */
class DynamicQueryContext {
	const sep = "\n";
	private $variables;

	function __construct(array $variables = null) {
		$this->variables = $variables;
	}

	/** @return ICacheManager */
	public static function getCacheManager() {
		static $instance = null;
		if($instance == null) {
			$class = DynamicQueryConf::CACHE_MANAGER;
			$instance = new $class();
		}

		return $instance;
	}

	public function getVariables() { return $this->variables; }

	function __set($name, $value) { $this->variables[$name] = $value; }

	function __get($name) { return ArrayUtil::getValue($this->variables, $name); }

	function __isset($name) { return array_key_exists($name, $this->variables); }
}