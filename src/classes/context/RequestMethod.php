<?php
/**
 * User: Kyeongdae
 * Date: 2016-12-13
 * Time: 오전 7:29
 */

namespace classes\context;


trait RequestMethod
{
	function isGet() { return $_SERVER['REQUEST_METHOD'] == 'GET'; }

	function isPost() { return $_SERVER['REQUEST_METHOD'] == 'POST'; }

	function isHead() { return $_SERVER['REQUEST_METHOD'] == 'HEAD'; }

	function isDelete() { return $_SERVER['REQUEST_METHOD'] == 'DELETE'; }

	function isPut() { return $_SERVER['REQUEST_METHOD'] == 'PUT'; }

	function isOptions() { return $_SERVER['REQUEST_METHOD'] == 'OPTIONS'; }

	function isConnect() { return $_SERVER['REQUEST_METHOD'] == 'CONNECT'; }

	function getMethod() { return $_SERVER['REQUEST_METHOD']; }

	function __toString() { return array_key_exists('REQUEST_METHOD', $_SESSION) ? $_SERVER['REQUEST_METHOD'] : ''; }
}
