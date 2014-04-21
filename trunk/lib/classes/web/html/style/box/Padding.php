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

		if(is_null($right) && is_null($bottom) && is_null($left)) {
			$this->right = $top;
			$this->bottom = $top;
			$this->left = $top;
		} elseif(is_null($bottom) && is_null($left)) {
			$this->right = $right;
			$this->bottom = $top;
			$this->left = $right;
		} elseif(is_null($left)) {
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