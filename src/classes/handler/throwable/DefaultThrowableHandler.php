<?php
/**
 * User: Kyeongdae
 * Date: 2016-12-13
 * Time: 오전 6:58
 */

namespace classes\handler\throwable;

use classes\exception\http\HTTPResponseException;
use classes\lang\ArrayUtil;
use classes\util\ThrowableLogger;
use classes\web\XMLExceptionPrinter;

class DefaultThrowableHandler implements IThrowableHandler {
	/** @var string[] 예외 페이지들 */
	private static $exceptionPages = array();

	/**
	 * @param string $exceptionClass 예외 클래스명
	 * @param string $page 예외 페이지 경로
	 */
	public function addExceptionPage(string $exceptionClass, string $page) { self::$exceptionPages[$exceptionClass] = $page; }

	/**
	 * 예외 페이지 핸들링(저장 및 출력)
	 *
	 * @param \Throwable $throwable
	 *
	 * @throws \Exception
	 * @throws \Throwable
	 */
	public function handling(\Throwable $throwable = null) {
		ThrowableLogger::getInstance()->writeObjectLog($throwable);
		if (ob_get_length() != 0)
			ob_clean();

		$types = array();
		$types[] = get_class($throwable);
		$classRef = new \ReflectionObject($throwable);

		while (($classRef = $classRef->getParentClass()) != false) $types[] = $classRef->getName();
		unset($classRef);

		$this->displayHandledTypes($types, $throwable);
		$this->displayUnhandledTypes($throwable);
	}


	/**
	 * @param            $types
	 * @param \Throwable $throwable include 된 페이지에서 $throwable 를 출력한다.
	 */
	private function displayHandledTypes($types, /** @noinspection PhpUnusedParameterInspection */
	                                     \Throwable $throwable) {

		if ($throwable instanceof HTTPResponseException) {
			http_response_code($throwable->getCode());
		}

		foreach ($types as $type) {
			if (array_key_exists('\\' . $type, self::$exceptionPages)) {
				if (!file_exists($path = _APP_ROOT_ . "assets/exception_page/{$this->findExceptionPagePath($type)}")) {
					echo "{$path} not found. Please generate \"assets/exception_page/{$this->findExceptionPagePath($type)}\" file.";
					die();
				}
				/** @noinspection PhpIncludeInspection */
				require_once $path;
				die();
			}
		}
	}

	/**
	 * 예외 페이지 표시 스크립 경로 해결
	 *
	 * @param string $exceptionType 예외 클래스명
	 *
	 * @return string
	 */
	private function findExceptionPagePath($exceptionType) { return ArrayUtil::getValue(self::$exceptionPages, '\\' . $exceptionType); }

	/**
	 * @param \Throwable $throwable
	 *
	 * @throws \Throwable
	 */
	private function displayUnhandledTypes(\Throwable $throwable) {
		http_response_code(500);


		if (ini_get('html_errors') == 0) {
			getApplicationContext()->getResponseContext()->setContextType('text/plain', _DEFAULT_CHARSET_);

			echo "exception class: " . get_class($throwable) . "\n";
			echo "file: " . $throwable->getFile() . "\n";
			echo "code: " . $throwable->getCode() . "\n";
			echo "msg: " . $throwable->getMessage() . "\n";
			echo "\n";

			echo $throwable->getTraceAsString();
		} else {
			getApplicationContext()->getResponseContext()->setContextType('text/html', _DEFAULT_CHARSET_);
			echo "<strong>xdebug loaded</strong><br><br>";

			echo "exception class: <strong>" . get_class($throwable) . "</strong><br>";
			echo "file: <strong>" . $throwable->getFile() . "</strong><br>";
			echo "code: <strong>" . $throwable->getCode() . "</strong><br>";
			echo "msg: <strong>" . $throwable->getMessage() . "</strong><br>";
			echo "<br>";

			echo nl2br($throwable->getTraceAsString());
		}
	}
}