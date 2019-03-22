<?php
/**
 * User: Kyeongdae
 * Date: 2018-07-11
 * Time: 오전 11:42
 */

namespace classes\web\dispatch\executor;


use classes\io\exception\IOException;
use classes\web\dispatch\resolver\IDispatcherResolver;

class TestRequestExecutor implements IRequestExecutor {
	private $executor;

	/**
	 * TestRequestExecutor constructor.
	 */
	public function __construct() {
		$this->executor = new RequestExecutor();
	}

	function setDispatchResolver(IDispatcherResolver $dispatchResolver) {
		$this->executor->setDispatchResolver($dispatchResolver);
	}

	/**
	 * @param string|null $className
	 *
	 * @return mixed
	 * @throws IOException
	 */
	function doDispatch(string $className = null) {
		$time_start = microtime(true);
		$response = $this->executor->doDispatch($className);

		echo "\n\nrequest header--------------------";
		foreach (getApplicationContext()->getResponseContext()->getAllHeader() as $name => $value) {
			echo "\n$name: $value";
		}

		echo "\n\nresponse header--------------------";
		foreach ($GLOBALS["headers"] as $name => $data) {
			echo "\n######### begin $name\n";
			echo "** string:\n   " . $data['string'] . "\n";
			echo "** replace:\n   " . ($data['replace'] ? 'true' : 'false') . "\n";
			echo "** http_response_code:\n   " . ($data['http_response_code'] ? 'true' : 'false');
			echo "\n######### end $name\n";
		}

		echo "\nusage--------------------";
		$time_end = microtime(true);
		/** @noinspection PhpUndefinedVariableInspection */
		$execution_time = round(($time_end - $time_start) * 1000);
		echo "\nDispatch Time: {$execution_time} ms";

		$mem = floor((memory_get_usage() / 1024 / 1024) * 100) * 0.01;
		$memPeak = floor((memory_get_peak_usage() / 1024 / 1024) * 100) * 0.01;
		echo "\nMem Usage(MB):" . $mem . '/' . $memPeak;

		if (isset($ex)) {
			echo "\n\throw exception--------------------";
			throw $ex;
		}

		return $response;
	}


}