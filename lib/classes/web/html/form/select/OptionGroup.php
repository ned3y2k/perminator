<?php
namespace classes\web\html\form\select;
use classes\web\html\HTMLElement;
use classes\web\html\form\select\Option;

class OptionGroup extends HTMLElement {

	public function onCreate() {
	}

	public function getTagName() {return "optgroup";}
	public function appendChild(HTMLElement $element) {
		if(!$element instanceof Option) throw new \InvalidArgumentException();
		parent::appendChild($element);
	}

	public function toString() {
		throwNewUnimplementedException();

	}

}
