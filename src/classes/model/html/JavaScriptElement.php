<?php

namespace classes\model\html;

class JavaScriptElement extends HeadElement {
	private $content = null;

	public function onCreate() {
//		$this->setAttribute('type', 'text/javascript');
	}

	/**
	 * @param string $src
	 *
	 * @return $this
	 */
	public function setSrc($src) {
		if ($this->content !== null) throw new JavaScriptElementException("이미 내용이 있습니다.");
		$this->setAttribute('src', $src);
		return $this;
	}

	/**
	 * @param string $content
	 *
	 * @return $this
	 */
	public function setContent($content) {
		if ($this->hasAttribute('src')) throw new JavaScriptElementException("소스값이 지정되어 있습니다.");
		$this->content = $content;
		return $this;
	}

	/** @return string */
	public function getTagName() { return 'script'; }

	/**
	 * @param string      $src
	 * @param HTMLContext $context
	 *
	 * @return \classes\model\html\JavaScriptElement
	 */
	public static function createFromSrc($src, HTMLContext $context = null) {
		$instance = new self($context);
		$instance->setSrc($src);

		return $instance;
	}

	/** @return string */
	public function toString() {
		$strBuilder = HTMLContext::createStringBuilder();
		$strBuilder->append('<script');

		$async = $this->getAttribute('async', false);
		$this->removeAttribute('async');

		$strBuilder->append($this->attrsToString());

		if ($async)
			$strBuilder->append(' async');

		if ($this->content === null) {
			$strBuilder->append('></script>');

			return $strBuilder->toString();
		} else {
			$strBuilder->append('>');
			$strBuilder->append($this->content);
			$strBuilder->append('</script>');

			return $strBuilder->toString();
		}
	}

	public function setAsync(bool $async) {
		$this->setAttribute('async', $async);
		return $this;
	}


}

class JavaScriptElementException extends \RuntimeException {}