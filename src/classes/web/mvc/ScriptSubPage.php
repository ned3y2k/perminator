<?php
/**
 * BITMOBILE.
 * 작성자: Kyeongdae
 * 일자: 2014-10-16
 * 시간: 오전 10:15
 */

namespace classes\web\mvc;

use classes\{
	exception\mvc\TPLFileNotFoundException,
	io\exception\FileNotFoundException
};

/**
 * Class ScriptSubPage
 *
 * @package classes\web\mvc
 */
class ScriptSubPage extends SubPage {
	/** @var string */
	private $filePath;
	private $content;

	/**
	 * @param string $filePath
	 * @param array  $content
	 */
	function __construct($filePath, array $content = null) {
		$this->content  = $content;
		$this->filePath = $filePath;
		if (!file_exists($this->filePath)) throw new TPLFileNotFoundException("{$this->filePath} not found");
	}

	/**
	 * 페이지 내용을 출력한다.
	 * @return string
	 */
	public function toString() {
		ob_start();
		try {
			extract($this->content);
			$success = include $this->filePath;

			if (!$success)
				throw new FileNotFoundException($this->filePath);
		} catch (\Exception $ex) {
			var_dump($ex);
		}


		$result = ob_get_clean();

		return $result;
	}

	public function __toString() {
		return $this->toString();
	}
}