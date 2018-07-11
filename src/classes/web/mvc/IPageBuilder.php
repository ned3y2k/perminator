<?php

namespace classes\web\mvc;

use classes\model\html\HeadElement;
use classes\web\HttpResponse;
use classes\webpage\menu\MenuContainer;

/**
 * Interface IPageBuilder
 *
 * @package classes\web\mvc
 */
interface IPageBuilder {
	public function display(): HttpResponse;

	/**
	 * @param HeadElement $headElement
	 *
	 * @return $this
	 */
	public function addHeadElement(HeadElement $headElement);

	/**
	 * @param HeadElement[] $headElements
	 *
	 * @return $this
	 */
	public function addHeadElements(... $headElements);

	/** @return string */
	public function getHeadElements();

	/**
	 * @param MenuContainer $menu
	 *
	 * @return IPageBuilder
	 */
	public function setMenuContainer(MenuContainer $menu);

	/**
	 * @param string $key
	 * @param string $content
	 *
	 * @return $this
	 */
	public function putContent($key, $content);

	/**
	 * @param string $key
	 *
	 * @return string
	 */
	public function getContent($key);

	public function getContents();

	public function executeShowSubPage();

	/**
	 * @param string[] $queries (string=>string)
	 * @param string   $url
	 *
	 * @return string
	 */
	public function createPageLink(array $queries, $url);
}