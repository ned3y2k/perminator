<?php
/**
 * User: Kyeongdae
 * Date: 2016-11-11
 * Time: 오전 4:04
 */

namespace classes\database\query\mapper\exception\node;


use classes\database\query\mapper\exception\DynamicQueryBuilderException;

class QueryNodeAttributeStatementException extends DynamicQueryBuilderException {
	public function __construct($message = "", $errorMsg) {
		parent::__construct($message . " statement exception. " . $errorMsg);
	}
}