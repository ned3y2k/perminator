<?php
/**
 * BITMOBILE
 * 작성자: Kyeongdae
 * 일자: 14. 9. 12
 * 시간: 오후 1:14
 */

namespace classes\webpage;

use classes\io\exception\FileNotFoundException;
use classes\io\File;
use classes\web\response\HttpResponse;

class CombinedTextPage {
	private $files = array();
	private $response;

	/**
	 * @param string $charset
	 * @return CombinedTextPage
	 */
	public static function createJSPage($charset = 'utf-8') {
		$page = new self();
		$page->putHeader('Content-type', 'text/javascript; charset=' . $charset);
		return $page;
	}

	/**
	 * @param string $charset
	 * @return CombinedTextPage
	 */
	public static function createCSSPage($charset = 'utf-8') {
		$page = new self();
		$page->putHeader('Content-type', 'text/css; charset=' . $charset);

		return $page;
	}

	public function __construct() {
		$this->response = new HttpResponse();
	}

	public function putHeader($name, $value) {
		$this->response->putHeader($name, $value);
	}

	public function addFile($file) {
		$this->files[] = $file;
	}

	/**
	 * @throws FileNotFoundException
	 */
	public function flush() {
		$buff = '';

		// $etag = $this->createEntityTag();

		foreach ($this->files as $file) {
			if (File::exists($file, true)) {
				$buff .= "\n/***** {$file} *****/\n" . File::readAllLine($file, true) . "\n";
			} else {
				$buff .= "\n/***** err: {$file} *****/\n";
			}
		}

		$this->response->setBody($buff);
		$this->response->send();
	}

	/**
	 * @return string
	 * @throws FileNotFoundException
	 */
	private function createEntityTag() {
		$times = array();

		foreach ($this->files as $file) {
			$times[] = File::getModificationTime($file);
		}

		return md5(implode('', $times));
	}
} 