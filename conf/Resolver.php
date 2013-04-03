<?php
namespace conf;
class Resolver {
//	const REQUEST_RESOLVER = '\classes\resolver\request\ClassNameAndMethodNameResolver';
	const REQUEST_RESOLVER = '\classes\resolver\request\JsonResolver';
	const EXCEPTION_RESOLVER = '\classes\resolver\exception\JsonResolver';
}