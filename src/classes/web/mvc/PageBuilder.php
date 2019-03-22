<?php
namespace classes\web\mvc;

use classes\{
	context\RequestContext,
	exception\mvc\TPLFileNotFoundException,
	lang\ArrayStringBuilder,
	lang\ArrayUtil,
	model\html\HeadElement,
	web\response\HttpResponse,
	web\response\StringResultResponse,
	webpage\menu\MenuContainer
};

require_once 'func/page_builder.php';

/**
 * Class PageBuilder
 *
 * @package classes\web\mvc
 */
abstract class PageBuilder implements IPageBuilder {
	/** @var string */
	protected $mainTplPath;
	/** @var string|SubPage */
	protected $subPage;

	/** @var MenuContainer */
	protected $menuContainer;

	/** @var string */
	protected $title;
	/** @var string[] */
	protected $contents = [];

	/** @var IPageBuilderPreAction[] */
	protected $preActions = [];
	/** @var bool */
	protected $compress = true;
	/** @var HeadElement[] */
	protected $headElements = [];

	/**
	 * @param string $mainTplPath
	 * @param string|SubPage $subPage
	 */
	public function __construct($mainTplPath, $subPage = null) {
		$this->mainTplPath    = $mainTplPath;
		$this->subPage = $subPage;

		ob_start();
	}

	/** @param bool $flag default true */
	public function setCompress($flag) { $this->compress = $flag; }

	/**
	 * 모바일인지 테스트
	 * mobile_test 쿠키에 요넘 있으면 무작정 모바일로
	 * @author 장봉수
	 * @return boolean
	 */
	public function isMobile() {
		$md = new \Mobile_Detect();

		if (!array_key_exists("mobile_test", $_COOKIE)) {
			if ($md->isMobile()) {
				return true;
			} else return false;
		} else {
			return true;
		}
	}

	/** @return bool */
	public function isTablet() {
		$md = new \Mobile_Detect();
		return $md->isTablet();
	}

	/** @param IPageBuilderPreAction $preAction */
	public function addPageBuilderPreAction(IPageBuilderPreAction $preAction) { $this->preActions[ ] = $preAction; }

	/**
	 * @param string $title
	 * @return $this
	 */
	public function setTitle($title) { $this->title = $title; return $this; }

	/**
	 * @param string|SubPage $subPage
	 * @return $this
	 */
	public function setSubPage($subPage) {
		$this->subPage = $subPage;
		return $this;
	}

	/**
	 * @param MenuContainer $menu
	 * @return $this
	 */
	public function setMenuContainer(MenuContainer $menu) {
		$this->menuContainer = $menu;
		return $this;
	}

	/**
	 * @param HeadElement $headElement
	 * @return PageBuilder
	 */
	public function addHeadElement(HeadElement $headElement) { $this->headElements[] = $headElement; return $this; }

	/**
	 * @param HeadElement[] $headElements
	 *
	 * @return PageBuilder
	 */
	public function addHeadElements(... $headElements) {
		$args = func_get_args();
		foreach ($args as $arg) {
			if(is_array($arg)) {
				$this->headElements = array_merge($this->headElements, $arg);
			} else {
				$this->headElements[] = $arg;
			}
		}

		return $this;
	}

	/**
	 * @param string $key
	 * @param mixed $content
	 * @return PageBuilder
	 */
	public function putContent($key, $content) {
		$this->contents[ $key ] = $content;
		return $this;
	}

	public function putContents(array $contents) {
		$this->contents = $this->contents + $contents;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getMainTplPath(): string {
		return $this->mainTplPath;
	}

	/**
	 * @param string $key
	 * @return string
	 */
	public function getContent($key) { return ArrayUtil::getValue($this->contents, $key); }
	
	public function getContents() { return $this->contents; }

	/** @return string */
	public function getTitle() { return $this->title; }

	/**
	 * @return string
	 */
	public function getHeadElements() {
		$strBuilder = new ArrayStringBuilder();

		foreach ($this->headElements as $headElement) {
			$strBuilder->append($headElement->toString() . "\n");
		}


		return $strBuilder->toString();
	}

	/** @return MenuContainer */
	public function getMenuContainer() { return $this->menuContainer; }

	public function display(RequestContext $requestContext): HttpResponse {
		foreach ($this->preActions as $preAction) {
			$preAction->execute($this);
		}

		$this->onDisplay();
		page_builder_init($this);

		if ($this->mainTplPath != null && !file_exists($this->mainTplPath)) {
			throw new TPLFileNotFoundException("{$this->mainTplPath} not found");
		}  elseif($this->mainTplPath != null && file_exists($this->mainTplPath)) {
			extract($this->contents);
			/** @noinspection PhpIncludeInspection */
			require_once $this->mainTplPath;
		}

		$content = ob_get_contents();
		ob_end_clean();

		if(false && ini_get('output_compression') !== '1' && ini_get('zlib.output_compression') !== 1 && $this->isSupportedCompress($requestContext)) {
			getApplicationContext()->getResponseContext()->setContentEncoding('gzip');
			return StringResultResponse::createHtmlPage(gzencode($content), false);
		} else {
			return StringResultResponse::createHtmlPage($content, false);
		}
	}

	protected abstract function onDisplay();

	public function executeShowSubPage() {
		return SubPageResolver::resolve($this->subPage, $this);
	}

	/**
	 * @param string[] $queries (string=>string)
	 * @param string $url
	 * @return string
	 */
	public function createPageLink(array $queries, $url) {
		$temp = array();

		foreach(array_diff_key($_GET, $queries) as $key=>$value) {
			$temp[$key] = $key.'='.urlencode($value);
		}

		foreach($queries as $key=>$value) {
			if(strlen($value) != 0)
				$temp[$key] = $key.'='.urlencode($value);
		}

		if($url == null) $url = _SELF_;

		return $url . '?' . implode('&', $temp);
	}

	/**
	 * @param RequestContext $requestContext
	 * @return bool
	 */
	private function isSupportedCompress(RequestContext $requestContext) {

		return $this->compress && $requestContext->hasAcceptEncoding('gzip');
	}
}