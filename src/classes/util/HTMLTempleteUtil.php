<?php
namespace classes\util;

class HTMLTempleteUtil {
	static function errorMsg($msg) {
echo '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<title>에러페이지</title>
</head>
<body>'.$msg.'</body></html>';
		exit;
	}

	static function invalidRequest() {
		self::errorMsg('잘못된 요청');
	}
}