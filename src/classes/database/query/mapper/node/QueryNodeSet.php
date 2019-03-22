<?php
/**
 * User: Kyeongdae
 * Date: 2016-06-18
 * Time: 오전 11:19
 */

namespace classes\database\query\mapper\node;


/**
 * Class QueryNodeSet
 * update set
 * @package classes\database\query\mapper\node
 */
class QueryNodeSet extends AbstractChildNode {
	function __toString() {
		$buff = '';
		foreach($this->childNodes as $childNode) {
			$buff .= $childNode->__toString();
		}

		return trim($buff) ? 'SET '.$buff : ' ';
	}

}