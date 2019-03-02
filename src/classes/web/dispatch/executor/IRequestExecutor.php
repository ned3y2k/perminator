<?php
/**
 * User: Kyeongdae
 * Date: 2018-07-11
 * Time: 오전 11:40
 */

namespace classes\web\dispatch\executor;


use classes\web\dispatch\resolver\IDispatcherResolver;

interface IRequestExecutor {
	function doDispatch(string $className = null);
	function setDispatchResolver(IDispatcherResolver $dispatchResolver);
}