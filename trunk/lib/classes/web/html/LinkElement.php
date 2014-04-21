<?php
namespace classes\web\html;

use classes\lang\StringBuilder;
class LinkElement extends HTMLElement {

	public static function createStyleSheetLink($src) {
		$instance = new self ();
		$instance->setAttribute ( "rel", "stylesheet" );
		$instance->setAttribute ( "type", "text/css" );
		$instance->setAttribute ( "href", $src );

		return $instance;
	}

	public function setRel($rel) { $this->setAttribute ( 'rel', $rel ); return $this; }
	public function setType($type) { $this->setAttribute( 'type', $type); return $this; }
	public function setHref($href) { $this->setAttribute( 'href', $href); return $this; }
	public function setMedia($media) { $this->setAttribute('media', $media); return $this; }
	public function setTarget($target) { $this->setAttribute('target', $target); return $this; }
	public function setRev($rev) { $this->setAttribute('rev', $rev); return $this; }
	public function setCharset($charset) { $this->setAttribute('charset', $charset); return $this; }

	public function onCreate() {
	}

	public function getTagName() { return 'link'; }

	public function toString() {
		$strBuilder = new StringBuilder();
		$strBuilder->append('<link');
		$strBuilder->append($this->attrsToString());

		if($this->htmlContext->isXHTML) {
			$strBuilder->append('>');
		} else {
			$strBuilder->append('/>');
		}

		return $strBuilder->toString();
	}
}