<?php
/**
 * Bitmobile System Corp.
 * 작성자: Kyeongdae
 * 일자: 2015-04-02
 * 시간: 오후 11:34
 */

namespace classes\runtime\serialization\json;


/**
 * Class JsonIgnoreField
 *
 * @package classes\runtime\serialization\json
 */
class JsonIgnoreField implements IJsonIgnoreField {
	/** @return JsonIgnoreField */
	public static function getInstance() {
		static $instance = null;
		if($instance == null)
			$instance = new self();
		return $instance;
	}

	private function __construct() {}
}