<?php
/**
 * User: Kyeongdae
 * Date: 2016-11-11
 * Time: 오전 3:59
 */

namespace classes\database\query\mapper\node\attribute;


use classes\database\query\mapper\DynamicQueryContext;
use classes\database\query\mapper\exception\node\QueryNodeAttributeStatementException;

abstract class AbstractQueryNodeAttribute {
	/** @var string 에러 내용 */
	protected $errorMsg = null;
	/** @var int 에러 코드 */
	protected $errorSeverity;

	protected function evalExecute(DynamicQueryContext $context, $cmd) {
		set_error_handler(array($this, 'occurError'));
		$result = eval($cmd);

		$errorMsg = $this->getErrorMsg();
		if($errorMsg) {
			throw new QueryNodeAttributeStatementException($this->getAttributeName(), $errorMsg);
		}

		restore_error_handler();

		return $result;
	}

	protected abstract function getAttributeName();

	private function getErrorMsg() {
		if($this->errorMsg !== null) {
			return $this->errorSeverity == E_NOTICE
				? "undefined constant '{$this->normalizeVarName()}'"
				: $this->errorMsg;

		}

		return null;
	}

	/**
	 * @return string
	 */
	protected function normalizeVarName():string {
		$strLen   = strlen($this->errorMsg);
		$startPos = strpos($this->errorMsg, '\'') + 1;
		$endPos   = strrpos($this->errorMsg, '\'');
		$length   = ($strLen - $startPos) - ($strLen - $endPos);
		$varName  = substr($this->errorMsg, $startPos, $length);

		return $varName;
	}

	/**
	 * @param int    $err_severity
	 * @param string $err_msg
	 * @param string $err_file
	 * @param int    $err_line
	 * @param array $err_context
	 */
	protected function occurError($err_severity, $err_msg, $err_file, $err_line, array $err_context) {
		$this->errorSeverity = $err_severity;
		$this->errorMsg = $err_msg;
	}
}