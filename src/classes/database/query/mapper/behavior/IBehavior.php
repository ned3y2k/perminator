<?php
/**
 * BITMOBILE.
 * 작성자: Kyeongdae
 * 일자: 14. 9. 15
 * 시간: 오전 11:28
 */

namespace classes\database\query\mapper\behavior;


interface IBehavior {
	/** 파일로 부터 */
	const TYPE_FILE = 0;
	/** 문자열로 부터 */
	const TYPE_STRING = 1;

    function execFunction($queryId, array $variables = null, $mapperContent, $type = self::TYPE_FILE);
    function execProcedure($queryId, array $variables = null, $mapperContent, $type = self::TYPE_FILE);
} 