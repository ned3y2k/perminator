<?php
namespace classes\model\html;

interface TextNode extends IHtmlNode {
	public function getTextNode();
	public function setTextNode($text);
}