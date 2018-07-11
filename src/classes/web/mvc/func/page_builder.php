<?php
/**
 * Created by PhpStorm.
 * User: Kyeongdae
 * Date: 2018-07-07
 * Time: 오후 3:42
 */
use classes\{
	exception\mvc\PageBuilderCouldNotFoundMethodException, exception\mvc\PageBuilderException, web\mvc\PageBuilder
};

/** @return \classes\web\mvc\IPageBuilder */
function page_builder() { return getApplicationContext()->getSharedUserContext()->getSharedValue('pageBuilder'); }

/**
 * 페이지 빌드 클래스 display 를 재정의 할때는 반드시 이 놈을 콜해주어야 한다.(ContextSwitching 개념??)
 *
 * @param PageBuilder $newPageBuilder
 */
function page_builder_init(PageBuilder $newPageBuilder) { getApplicationContext()->getSharedUserContext()->putSharedValue('pageBuilder', $newPageBuilder); }

function page_builder_free() { getApplicationContext()->getSharedUserContext()->removeSharedValue('pageBuilder'); }

function page_builder_check() { if (page_builder() == null) throw new RuntimeException('you need to call page_builder_init'); }

function page_builder_method_call($method, $args = array()) {
	$page_builder = page_builder();

	if($page_builder == null) {
		$stack = debug_backtrace();
		$file = $stack[1]['file'];
		$line = $stack[1]['line'];

		throw new PageBuilderException("page_builder_method_call occur error. page builder is null. {$file}:{$line}");
	}  elseif (!($page_builder instanceof \classes\web\mvc\IPageBuilder)) {
		$stack = debug_backtrace();
		$file = $stack[1]['file'];
		$line = $stack[1]['line'];

		throw new PageBuilderException("page_builder_method_call occur error. {$file}:{$line}");
	}

	if (method_exists($page_builder, $method)) return call_user_func_array(array($page_builder, $method), $args);
	else throw new PageBuilderCouldNotFoundMethodException(get_class($page_builder), $method);
}

/** @return \classes\webpage\menu\MenuContainer */
function page_builder_get_menu_container() { return page_builder_method_call('getMenuContainer'); }

/**
 * 현재 페이지명을 가지고온다.
 * @return STRING
 */
function page_builder_get_title() {
	return page_builder_method_call('getTitle');
}

/**
 * 일정한 틀이 있는 경우 사용된다.
 * 단일 페이지이면 사용될일이 없을듯;
 */
function page_builder_show_content_tpl() {
	return page_builder()->executeShowSubPage();
}

/**
 * 페이지 빌더에서 putContent 했던 내용을 가져온다!
 *
 * @param string      $key
 * @param string      $default
 * @param bool|string $trim
 *
 * @return string|mixed
 */
function page_builder_get_content($key, $default = null, $trim = true) {
	$value = page_builder_method_call('getContent', array($key));

	if ($trim && is_string($value) && strlen($value) === 0) return $default;
	elseif ($value === null) return $default;

	return $value;
}


/** @return string */
function page_builder_head_element() { return page_builder()->getHeadElements(); }

/**
 * @param array $queries
 * @param string $url
 *
 * @return string
 */
function page_builder_createPageLink(array $queries, $url = null) { return page_builder()->createPageLink($queries, $url); }

function page_builder_dump() { var_dump(page_builder()); }