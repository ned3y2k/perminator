<?php
namespace classes\model\html\style\box;

if (! defined ( 'BITMOBILE' )) throw new \LogicException('not defined BITMOBILE');

class Border {
	const NONE = 'none';
	const DOTTED = 'dotted';
	const DASHED = 'dashed';
	const SOLID = 'solid';
	const DOUBLE = 'double';
	const GROOVE = 'groove';
	const RIDGE = 'ridge';
	const INSET = 'inset';
	const OUTSET = 'outset';
	const INHERIT = 'inherit';

	public $top;
	public $topStyle;
	public $topColor;
	public $topWidth;

	public $right;
	public $rightStyle;
	public $rightColor;
	public $rightWidth;

	public $bottom;
	public $bottomStyle;
	public $bottomColor;
	public $bottomWidth;

	public $left;
	public $leftStyle;
	public $leftColor;
	public $leftWidth;
}