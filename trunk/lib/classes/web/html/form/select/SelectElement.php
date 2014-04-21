<?php
namespace classes\web\html\form\select;

use classes\web\html\HTMLElement;
use classes\web\html\form\select\OptionGroup;
use classes\web\html\form\select\Option;

class SelectElement extends HTMLElement {

	public function onCreate() {
	}

	public function getTagName() { return 'select'; }
	public function appendChild(HTMLElement $element) {
		if(!($element instanceof Option || $element instanceof OptionGroup)) throw new \InvalidArgumentException();
		parent::appendChild($element);
	}

	public function toString() {
		throwNewUnimplementedException();
	}

}