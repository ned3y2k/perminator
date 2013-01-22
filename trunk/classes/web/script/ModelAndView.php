<?php
namespace classes\web\script;
use conf\Core;

// http://grepcode.com/file/repo1.maven.org/maven2/org.springframework/spring-webmvc/2.5.1/org/springframework/web/servlet/ModelAndView.java
class ModelAndView implements View {
	private $contentType;
	private $content;
	private $modelMap;
	private $owner;
	public function __construct($view, $modelMap = null, $contentType = null) {
		$this->setView ( $view );
		
		if (! is_null ( $modelMap ))
			$this->setModelMap ( $modelMap );
		else
			$this->modelMap = array ();
		
		if (is_null ( $contentType ))
			if ($view instanceof View)
				$this->contentType = $contentType;
			else
				$contentType = "text/html; charset=" . Core::DEFAULT_CHARSET;
		
		$this->contentType = $contentType;
	}
	
	/**
	 *
	 * @param String|View $view
	 *        	view or path
	 */
	public function setView($view) {
		if ($view instanceof View) {
			$this->contentType = $view->getContentType ();
			$this->content = $view;
		} elseif (is_string ( $view ))
			$this->content = $view;
		elseif (is_null ( $view ))
			$this->content = null;
		else
			throw new \InvalidArgumentException ( "only accepts string or " . __NAMESPACE__ . "\\View" );
	}
	public function setModelMap(array $modelMap) {
		unset ( $this->modelMap );
		if (is_array ( $modelMap ))
			$this->modelMap = $modelMap;
		else
			throw new \InvalidArgumentException ( "Invalid ModelMap" );
	}
	public function addValue($name, $value) {
		$this->modelMap [$name] = $value;
	}
	public function setOwner(View &$owner) {
		$this->owner = $owner;
	}
	public function getContentType() {
		return $this->contentType;
	}
	public function getContent() {
		$content = $this->content;
		
		if ($content instanceof View) {
			return $content->getContent ();
		} elseif (is_string ( $content )) {
			$path = $this->findViewFilePath ( $content );
			
			if ($this->isPhpScript($content)) {
				try {
					ob_start ();
					include $path;
					
					$result = ob_get_contents ();
					ob_end_clean ();
					
					return $result;
				} catch ( \Exception $ex ) {
					ob_clean ();
					throw $ex;
				}
			} else {
				echo file_get_contents ( $path );
			}
		}
	}
	private function isPhpScript($name) {return strtolower ( substr ( $name, - 4 ) ) == ".php";}
	public function getAttribute($name) {
		if (! array_key_exists ( $name, $this->modelMap ))
			throw new \RuntimeException ( $name . " not found" );
		return $this->modelMap [$name];
	}
	public function findViewFilePath($fileName) {
		if (file_exists ( Core::DEFAULT_VIEW_PATH . $fileName ))
			return Core::DEFAULT_VIEW_PATH . $fileName;
		elseif (file_exists ( $fileName )) {
			return $fileName;
		} else
			throw new ViewNotFoundException ( $fileName );
	}
}