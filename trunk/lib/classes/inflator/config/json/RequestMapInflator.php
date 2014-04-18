<?php
namespace classes\inflator\config\json;
use classes\inflator\Inflator;

class RequestMapInflator implements Inflator {
	public static function inflate() {
		// FIXME file_get_contents 로 가져오지 않게 바꾸기
		return json_decode(file_get_contents('app/conf/requestMap.json'));
	}
}