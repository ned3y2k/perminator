<?php
namespace classes\model\html\style\box;
if (! defined ( 'BITMOBILE' )) throw new \LogicException('not defined BITMOBILE');

class Padding {
	public $top;
	public $right;
	public $bottom;
	public $left;

	public function __construct($top, $right = null, $bottom = null, $left = null) {
		$this->top = $top;

		if($right === null && $bottom === null && $left === null) {
			$this->right = $top;
			$this->bottom = $top;
			$this->left = $top;
		} elseif($bottom === null && $left === null) {
			$this->right = $right;
			$this->bottom = $top;
			$this->left = $right;
		} elseif($left === null) {
			$this->right = $right;
			$this->bottom = $bottom;
			$this->left = $right;
		} else {
			$this->right = $right;
			$this->bottom = $bottom;
			$this->left = $left;
		}
	}
}