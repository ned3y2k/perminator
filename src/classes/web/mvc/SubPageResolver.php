<?php
/**
 * BITMOBILE.
 * 작성자: Kyeongdae
 * 일자: 2014-10-16
 * 시간: 오전 10:07
 */
namespace classes\web\mvc;

class SubPageResolver {
	/**
	 * @param StringSubPage|string $content
	 * @param IPageBuilder $pageBuilder
	 * @return SubPage
	 */
	public static function resolve($content, IPageBuilder $pageBuilder) {
		if($content == null) $subPage = new StringSubPage("content tpl is null");
		elseif(is_string($content)) $subPage = new ScriptSubPage($content, $pageBuilder->getContents());
		else $subPage = $content; // FIXME 어떤 경우에 타는지 모르겠음

		$subPage->setPageBuilder($pageBuilder);

		return $subPage;
	}
}