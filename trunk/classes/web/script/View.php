<?php
namespace classes\web\script;

// http://javasourcecode.org/html/open-source/spring/spring-3.0.5/org/springframework/web/servlet/View.java.html
interface View {
	public function setOwner(View& $owner);
	public function getContentType();
	public function getContent();
}