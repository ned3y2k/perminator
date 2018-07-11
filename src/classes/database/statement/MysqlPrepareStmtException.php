<?php
/**
 * Project: Inmoa.
 * User: Kyeongdae
 * Date: 2015-08-18
 * Time: 오후 5:54
 */

namespace classes\database\statement;


class MysqlPrepareStmtException extends \mysqli_sql_exception {
	public $object;

	public function __construct($message = '', $code = 0, MysqlPrepareStmt $object = null, $previous = null) {
		parent::__construct($message, $code, $previous);
		$this->object = $object;
	}
}