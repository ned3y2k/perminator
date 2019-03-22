<?php
/**
 * BITMOBILE.
 * 작성자: Kyeongdae
 * 일자: 2014-11-25
 * 시간: 오후 1:42
 */
namespace classes\database\query\mapper\parser;

use classes\database\query\mapper\node\QueryNodeMapper;

interface IXmlParser {
	/** @param string $xml xml content */
	public function __construct($xml);
	/** @return QueryNodeMapper */
	public function getMapper();
} 