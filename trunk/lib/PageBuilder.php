<?php
namespace lib;
use classes\util\ViewUtil;

abstract class PageBuilder {
	protected $header;
	protected $footer;
	private $jsPaths = array ();
	private $cssPaths;
	private $title;
	private $content;
	private $pageNo;
	private $paginatorFormat;
	private $pageNoFormat;
	private $paginationLimit;
	private $totalPage;
	private $headerContent;

	public abstract function __construct();

	public function setTitle($title) {$this->title = $title;return $this;}
	public function setContent($content) {$this->content = $content;}
	public function addJSPath($path) {$this->jsPaths [] = $path;return $this;}
	public function addCSSPath($path) {$this->cssPaths [] = $path;return $this;}
	public function setPageNo($pageNo) {$this->pageNo = $pageNo;return $this;}
	public function setPaginatorFormat($paginatorFormat) {$this->paginatorFormat = $paginatorFormat;return $this;}
	public function setPageNoFormat ($pageNoFormat) {$this->pageNoFormat = $pageNoFormat;return $this;}
	public function setPaginationLimit($paginationLimit) {$this->paginationLimit = $paginationLimit;return $this;}
	public function setTotalPage($totalPage) {$this->totalPage = $totalPage;return $this;}
	public function setHeaderContent($headerContent) {$this->headerContent = $headerContent;}

	public function createHeader() {
		$this->header = str_replace ( "@title", $this->title, $this->header );
		$javascriptLoadString = "";
		$jsFormat = "<script type='text/javascript' src='%s'></script>\n";

		foreach ( $this->jsPaths as $jsPath )
			$javascriptLoadString .= sprintf ( $jsFormat, $jsPath );

		$cssLoadString = "";
		$cssFormat = '<link rel="stylesheet" href="%s" type="text/css" />';
		foreach ( $this->cssPaths as $cssPath )
			$javascriptLoadString .= sprintf ( $cssFormat, $cssPath );

		$this->header = str_replace("@header", $this->headerContent, $this->header);
		$this->header = str_replace("@js", $javascriptLoadString, $this->header);
		$this->header = str_replace("@css", $cssLoadString, $this->header);

		return $this->header;
	}

	public function createPaginator() {
		$prevBtn = "";
		if($this->pageNo > 0)
			$prevBtn = sprintf($this->pageNoFormat, "", $this->pageNo - 1);
		else
			$prevBtn = sprintf($this->pageNoFormat, "disable", "#");

		$nextBtn = "";
		if($this->totalPage < $this->pageNo)
			$nextBtn = sprintf($this->pageNoFormat, "", $this->pageNo + 1);
		else
			$nextBtn = sprintf($this->pageNoFormat, "disable", "#");

		$pageBlock = floor(($this->pageNo - 1) / $this->totalPage);

		$firstPageNo = ($pageBlock * $this->paginationLimit) + 1;
		$firstPageNo = $firstPageNo < $this->totalPage ? $firstPageNo : $this->totalPage;

		$lastPageNo = ($firstPageNo-1)+$this->paginationLimit;
		$lastPageNo = $lastPageNo < $this->totalPage ? $lastPageNo : $this->totalPage;

		$paginatorViewCode = "";
		$paginatorViewCode .= sprintf($this->pageNoFormat, $this->pageNo == 1 ? "disabled":"", $firstPageNo, "&lt;");
		for($i = $firstPageNo; $i <= $lastPageNo; $i ++) {
			$class = $i == $this->pageNo ? 'active' : '';
			$paginatorViewCode .= sprintf($this->pageNoFormat, $class, $i, $i);
		}
		$paginatorViewCode .= sprintf($this->pageNoFormat, $this->pageNo == $lastPageNo ? "disabled":"", $lastPageNo, "&gt;");
		$paginatorViewCode = sprintf($this->paginatorFormat, $paginatorViewCode);

		return $paginatorViewCode;
	}

	public function createFooter() {
		return $this->footer;
	}
}