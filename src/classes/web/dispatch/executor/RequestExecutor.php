<?php
/**
 * User: Kyeongdae
 * Date: 2018-07-11
 * Time: 오전 11:42
 */

namespace classes\web\dispatch\executor;


use classes\io\exception\IOException;
use classes\lang\ArrayUtil;
use classes\web\dispatch\resolver\IDispatcherResolver;

class RequestExecutor implements IRequestExecutor {
	/**
	 * @var IDispatcherResolver
	 */
	private $dispatchResolver;

	/**
	 * @param string|null $className
	 *
	 * @return mixed
	 * @throws IOException
	 * @throws \Exception
	 */
	function doDispatch(string $className = null) {
		$thrownException = $this->checkRequest();
		if ($thrownException != null) {
			throw $thrownException;
		}

		// 여기서 컨트롤러 이름을 빼주고 다음에 처리 해야 할듯 한데

		return $this->dispatchResolver->resolve($className);
	}

	/**
	 * @return IOException|null
	 */
	private function checkRequest() {
		$maxSize = parse_size(ini_get('post_max_size'));
		if ($maxSize < floatval(ArrayUtil::getValue($_SERVER, 'CONTENT_LENGTH', '0'))) {
			return new IOException("Post Data exceeds the limit. should be increase the post limit.{req: {$_SERVER['CONTENT_LENGTH']}/max: $maxSize}");
		}

		return null;
	}

	function setDispatchResolver(IDispatcherResolver $dispatchResolver) {
		$this->dispatchResolver = $dispatchResolver;
	}
}