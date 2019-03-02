<?php
/**
 * User: Kyeongdae
 * Date: 2018-07-17
 * Time: 오후 3:32
 */

namespace classes\context;


trait ContextAssistTrait {
	public function requestParamTrim($value, $defaultValue) {
		if (is_array($value)) {
			if (count($value) == 0) return null;

			foreach ($value as &$v) {
				if (strlen(trim($v)) == 0) {
					$v = $defaultValue;
				}
			}

			return $value;
		} else {
			if (strlen(trim($value)) == 0) return $defaultValue;
			else return $value;
		}
	}
}