<?php
namespace classes\model\html;

/**
 * @author 경대
 * http://www.w3schools.com/jsref/dom_obj_all.asp
 */
abstract class HTMLElement implements IHtmlNode {
	/** @var IHtmlNode[] */
	public $childNodes = array();
	/** @var string */
	public $className;
	/** @var string */
	public $id;

	/** @var HTMLContext */
	protected $htmlContext;
	/** @var array */
	protected $attributes = array();
	/** @var IHtmlNode[] 부모 노드 */
	protected $parentNodes;

	/** @param HTMLContext $htmlContext */
	public final function __construct(HTMLContext $htmlContext = null) {
		$this->htmlContext = $htmlContext == null ? new HTMLContext() : $htmlContext;
		$this->onCreate();
	}

	public abstract function onCreate();

	public abstract function getTagName();

	/**
	 * 자식 노드 추가(지원 되는 경우만)
	 * @param HTMLElement $element
	 */
	public function appendChild(HTMLElement $element) {
		$element->parentNodes = $this;
		$this->childNodes[ ] = $element;
	}

	/**
	 * 속성 가지고 있는지 여부 검사
	 *
	 * @param string $attributeName
	 *
	 * @return bool
	 */
	public function hasAttribute($attributeName) {
		return array_key_exists($attributeName, $this->attributes);
	}

	/**
	 * 속성 설정
	 *
	 * @param string $attributeName
	 * @param string $attributeValue
	 * @return $this
	 */
	public function setAttribute($attributeName, $attributeValue) {
		$this->attributes[ $attributeName ] = $attributeValue;
		return $this;
	}

	public function getAttribute($attributeName, $defaultAttributeValue) {
		return array_key_exists($attributeName, $this->attributes) ? $this->attributes[$attributeName] : $defaultAttributeValue;
	}
	
	public function removeAttribute(string $attributeName) {
		unset($this->attributes[$attributeName]);
		return $this;
	}

	/**
	 * @param string $name
	 *
	 * @return IHtmlNode[]
	 */
	public function __get($name) {
		if ($name == 'parentNodes') return $this->parentNodes;
		throw new \UnsupportedOperationException();
	}

	/**
	 * @param string $name
	 * @param IHtmlNode $value
	 *
	 * @throws \UnsupportedOperationException
	 * @return IHtmlNode
	 */
	public function __set($name, $value) {
		if ($name == 'parentNodes') return $this->parentNodes = $value;

		throw new \UnsupportedOperationException();
	}

	/** @return string */
	protected function attrsToString() {
		$strBuilder = HTMLContext::createStringBuilder();

		foreach ($this->attributes as $key => $value) {
			$strBuilder->append(sprintf(' %s="%s"', $key, $value));
		}

		return $strBuilder->toString();
	}

	public abstract function toString();

	public final function __toString() { return $this->toString(); }

	//	public final function __toString() { $ref = new \ReflectionObject($this); return $ref->name; }
}