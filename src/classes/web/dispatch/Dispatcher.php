<?php
/**
 * BITMOBILE
 * 작성자: Kyeongdae
 * 일자: 2014-12-23
 * 시간: 오후 11:06
 */

namespace classes\web\dispatch;

use classes\web\dispatch\executor\IRequestExecutor;
use classes\web\dispatch\resolver\IDispatcherResolver;
use classes\web\IInterceptorFinder;

/**
 * Class Dispatcher
 */
class Dispatcher {
	/** @var IDispatcherResolver */
	private $dispatchResolver;
	/**
	 * @var IRequestExecutor
	 */
	private $requestExecutor;

	/**
	 * @param IDispatcherResolver $dispatchResolver
	 * @param IRequestExecutor    $requestExecutor
	 */
	public function __construct(IDispatcherResolver $dispatchResolver, IRequestExecutor $requestExecutor) {
		$this->dispatchResolver = $dispatchResolver;
		$requestExecutor->setDispatchResolver($dispatchResolver);

		$this->requestExecutor = $requestExecutor;
	}

	/** @param IInterceptorFinder $interceptorFinder */
	public function addInterceptorFinder(IInterceptorFinder $interceptorFinder) { $this->dispatchResolver->addInterceptorFinder($interceptorFinder); }

	/**
	 * @param string|null $className
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public function doDispatch($className = null) {
		return $this->requestExecutor->doDispatch($className);
	}
}