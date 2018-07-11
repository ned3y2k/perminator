<?php
/**
 * BITMOBILE
 * 작성자: Kyeongdae
 * 일자: 2015-01-29
 * 시간: 오후 2:20
 */

namespace classes\api\model;

interface IJSONResultStringier {
	/**
	 * @param JSONResult $result
	 * @return string
	 */
	public function stringify(JSONResult $result);
}