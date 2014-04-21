<?php
namespace classes\inflator\config\json;
use classes\inflator\Inflator;
use classes\io\File;

class JsonRequestMapInflator implements Inflator {
	public static function inflate() {
		return json_decode(File::readAllText('app/conf/requestMap.json'));
	}
}