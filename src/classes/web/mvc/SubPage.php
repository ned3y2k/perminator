<?php
/**
 * BITMOBILE.
 * 작성자: Kyeongdae
 * 일자: 2014-10-16
 * 시간: 오전 10:04
 */
namespace classes\web\mvc;


abstract class SubPage {
	/** @var IPageBuilder */
	private $pageBuilder;

	public function setPageBuilder(IPageBuilder $pageBuilder) { $this->pageBuilder = $pageBuilder; }

	/** @return IPageBuilder */
	protected function getPageBuilder () { return $this->pageBuilder; }

	/**
	 * 페이지 내용을 출력한다.
	 * @return string
	 */
	public abstract function __toString();
} 