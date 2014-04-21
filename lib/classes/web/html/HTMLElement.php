<?php
namespace classes\web\html;

use classes\lang\StringBuilder;
/**
 * @author 경대
 * http://www.w3schools.com/jsref/dom_obj_all.asp
 */
abstract class HTMLElement {
	public $childNodes = array();
	public $className;
	public $id;

	protected $htmlContext;
	protected $attributes = array();
	protected $parentNodes;

	public final function __construct(HTMLContext $htmlContext = null) {
		$this->htmlContext = is_null($htmlContext) ? new HTMLContext() : $htmlContext;
		$this->onCreate();
	}
	public abstract function onCreate();

	public abstract function getTagName();

	public function appendChild(HTMLElement $element) {
		$this->childs[] = $element;
	}

	public function hasAttribute($key) {
		return array_key_exists($key, $this->attributes);
	}

	public function setAttribute($attributeName, $attributeValue) {
		$this->attributes[$attributeName] = $attributeValue;
	}

	public function __get($name) {
		if($name == 'parentNodes') return $this->parentNodes;
	}

	public function __set($name, $value) {
		if($name == 'parentNodes') return $this->parentNodes = $value;
	}

	protected function attrsToString() {
		$strBuilder = new StringBuilder();

		foreach ($this->attributes as $key => $value) {
			$strBuilder->append(sprintf(' %s="%s"', $key, $value));
		}

		return $strBuilder->toString();
	}

	public abstract function toString();
	public final function __toString() { return $this->toString(); }

//	public final function __toString() { $ref = new \ReflectionObject($this); return $ref->name; }
}