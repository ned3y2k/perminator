<?php
/**
 * Bitmobile System Corp.
 * 작성자: Kyeongdae
 * 일자: 2015-04-29
 * 시간: 오후 5:55
 */

namespace classes\database\query\mapper\exception;


/**
 * Class DynamicQueryMysqlBehaviorException
 *
 * @package classes\database\query\mapper\exception
 */
class DynamicQueryExecutionException extends \RuntimeException {
	private $extras;

	/**
	 * DynamicQueryExecutionException constructor.
	 *
	 * @param string     $message
	 * @param int        $code
	 * @param \Exception $previous
	 * @param array      $extras
	 */
	public function __construct($message = "", $code = 0, \Exception $previous = null, array $extras = null) {
		parent::__construct($message, $code, $previous);
		$this->extras = $extras;
	}
}