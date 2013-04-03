<?php
namespace classes\inflator\config\json;
use classes\inflator\Inflator;

class RequestMapInflator implements Inflator {
	public static function inflate() {
		return json_decode(file_get_contents('conf/requestMap.json'));
	}
}