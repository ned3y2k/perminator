<?php
/**
 * Bitmobile System Corp.
 * 작성자: Kyeongdae
 * 일자: 2015-05-11
 * 시간: 오후 9:00
 */

namespace classes\web\response;

/**
 * Class ResponseFactory
 *
 * @package classes\common\api\response
 */
class ResponseFactory {
	/**
	 * @param IResponseDelegate|HttpResponse $invokeResult
	 * @return HttpResponse
	 * @throws \UnsupportedOperationException
	 */
	public static function create($invokeResult) {
		if ($invokeResult instanceof IResponseDelegate) {
			return $invokeResult->createResponse();
		} elseif ($invokeResult instanceof HttpResponse) {
			return $invokeResult;
		}

		throw new \UnsupportedOperationException('not yet supported result type');
	}
}