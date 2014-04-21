<?php
namespace classes\web\html;

use classes\lang\StringBuilder;
class JavaScriptElement extends HTMLElement  {
	private $content = null;

	public function onCreate() {
		$this->setAttribute('type', 'text/javascript');
	}

	public function setSrc($src) {
		if(!is_null($this->content)) throw new JavaScriptElementException("이미 내용이 있습니다.");
		$this->setAttribute('src', $src);
		return $this;
	}

	public function setContent($content) {
		if($this->hasAttribute('src')) throw new JavaScriptElementException("소스값이 지정되어 있습니다.");
		$this->content = $content;
		return $this;
	}

	public function getTagName() { return 'script'; }

	/**
	 * @param string $src
	 * @param HTMLContext $context
	 * @return \classes\model\html\JavaScriptElement
	 */
	public static function createFromSrc($src, HTMLContext $context = null) {
		$instance = new self($context);
		$instance->setSrc($src);

		return $instance;
	}

	public function toString() {
		$strBuilder = new StringBuilder();
		$strBuilder->append('<script');
		$strBuilder->append($this->attrsToString());

		if(is_null($this->content)) {
			$strBuilder->append('></script>');

			return $strBuilder->toString();
		} else {
			$strBuilder->append('>');
			$strBuilder->append($this->content);
			$strBuilder->append('</script>');

			return $strBuilder->toString();
		}
	}
}

class JavaScriptElementException extends \RuntimeException {}