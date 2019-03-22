<?php
namespace classes\model\html\form\select;

use classes\model\html\HTMLElement;

class SelectElement extends HTMLElement {

	public function onCreate() {
	}

	public function getTagName() { return 'select'; }

	public function appendChild(HTMLElement $element) {
		if (!($element instanceof Option || $element instanceof OptionGroup)) throw new \InvalidArgumentException();
		parent::appendChild($element);
	}

	public function toString() {
		throwNewUnimplementedException();
	}

}