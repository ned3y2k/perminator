<?php
namespace classes\web\html\form\select;
use classes\web\html\HTMLElement;
use classes\web\html\TextNode;

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