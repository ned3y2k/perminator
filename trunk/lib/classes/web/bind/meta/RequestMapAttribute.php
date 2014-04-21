<?php
namespace classes\web\bind\meta;

class RequestMapAttribute {
	public $value;
	public $requestMethod;

	public $header; // RegexIterator나 string

	/** TODO  content type 헤더와 비교 확정 헤더에 맞지 않는 요청인 경우 406 Not Acceptable 에러 발생 */
	public $consumes; // RegexIterator나 string

	/** TODO 클라이언트의 accept-header에 따라서 다른 응답을 주게 됨  */
	public $produces; // RegexIterator나 string

	public $params;
}