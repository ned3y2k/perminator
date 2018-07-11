<?php
namespace classes\model\html\form\select;
use classes\model\html\HTMLElement;
use classes\model\html\TextNode;

class Option extends HTMLElement implements TextNode {
	private $selected;
	private $text;

	public function onCreate() {
	}
	public function getTagName() {return 'option';}
	public function setSeleted($boolean) { if(!is_bool($boolean)) throw new \InvalidArgumentException(); $this->selected = $boolean; }

	public function getTextNode() { return $this->text; }
	public function setTextNode($text) { $this->text = $text; }

	public function toString() {
		throwNewUnimplementedException();

	}

}