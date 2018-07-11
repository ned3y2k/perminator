<?php
namespace classes\model;

use classes\model\html\style\box\Padding;
use classes\model\html\style\Color;

if (!defined('BITMOBILE')) throw new \LogicException('not defined BITMOBILE');

class ImageText {
	private $minWidth = 500;

	/**
	 * @var Padding
	 */
	private $padding;
	private $text;
	private $fontSize;

	/**
	 * @var Color
	 */
	private $fontColor;

	private $x = 0;
	private $y = 0;

	private $ttfPath;

	private $textBox;

	public static function createTextImage($text = "blank", Color $fontColor = null, $fontSize = 32, $ttfPath, Padding $padding = null) {
		if ($padding === null)
			$padding = new Padding(10);
		$instance = new ImageText();

		$instance->text      = $text;
		$instance->fontColor = $fontColor;
		$instance->padding   = $padding;
		$instance->fontSize  = $fontSize;

		return $instance;
	}

	private function __construct() {

	}

	public function getImagePath() {

	}
}
