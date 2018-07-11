<?php
namespace classes\model\html\form\select;

use classes\model\html\HTMLElement;

class OptionGroup extends HTMLElement {

	public function onCreate() {
	}

	public function getTagName() { return "optgroup"; }

	public function appendChild(HTMLElement $element) {
		if (!$element instanceof Option) throw new \InvalidArgumentException();
		parent::appendChild($element);
	}

	public function toString() {
		throwNewUnimplementedException();

	}

}
