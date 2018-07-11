<?php
/**
 * Created by PhpStorm.
 * User: Kyeongdae
 * Date: 2018-02-12
 * Time: 오후 2:03
 */

namespace classes\database\statement;


use Throwable;

class PDOPrepareStatementException extends \RuntimeException {
	public function __construct(string $message = "", int $code = 0, Throwable $previous = null) { parent::__construct($message, $code, $previous); }

}