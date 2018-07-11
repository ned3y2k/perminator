<?php
/**
 * Created by PhpStorm.
 * User: Kyeongdae
 * Date: 2018-06-30
 * Time: 오후 2:41
 */

namespace classes\model\html;


trait HeadElementFactoryTrait {
	/**
	 * @param string ...$src
	 *
	 * @return LinkElement[]
	 */
	function externCss(string...  $src) {
		$result = [];
		$func_get_args = func_get_args();
		if (!$func_get_args)
			return $result;

		foreach ($func_get_args as $src) {
			$result[] = LinkElement::createStyleSheetLink($src);
		}

		return $result;
	}

	function externJs(string... $src) {
		$result = [];
		$func_get_args = func_get_args();

		if (!$func_get_args)
			return $result;

		foreach ($func_get_args as $src) {
			$result[] = JavaScriptElement::createFromSrc($src);
		}

		return $result;
	}
}