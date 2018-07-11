<?php
namespace classes\model\html;

use classes\lang\IStringBuilder;

class HTMLContext {
	public $isXHTML = false;
	public static $STRING_BUILDER_NAME = '\classes\lang\StringBuilder';

	/** @return IStringBuilder */
	public static function createStringBuilder() { return new self::$STRING_BUILDER_NAME(); }
}