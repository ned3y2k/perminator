<?php
/**
 * User: Kyeongdae
 * Date: 2016-12-15
 * Time: 오전 4:39
 */

namespace classes\runtime\serialization\json;


interface IJsonUnserializable {
	function jsonUnserialize(\stdClass $stdClass = null);
}