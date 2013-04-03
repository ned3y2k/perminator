<?php
namespace util;

class JSONConfigInflater {
	public static function inflate($name) {
		$result = json_decode("conf/".$name);
	}
}