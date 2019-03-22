<?php
class ParityCheckException extends InvalidArgumentException { }

/**
 * 패리티 수를 만든다
 * @param string $data
 * @return int [0|1]
 */
function parity_create($data) { return hexdec(bin2hex($data)) % 2 == 0 ? 1 : 0; }

/**
 * 패리티 수를 점검한다
 *
 * @param int $parity [0|1]
 * @param string $data
 * @return boolean
 */
function parity_is_valid($parity, $data)
{
	if (!is_numeric($parity)) new ParityCheckException('invalid parity value');

	return parity_create($data) == $parity;
}