<?php
use classes\trouble\exception\assert\AssertCallerException;
use classes\trouble\exception\assert\AssertEqualException;

/**
 * 이전에 부른 함수명이 비교할 함수 이름과 같은지
 * @param array|string|... $expectedFuncName
 * @throws AssertCallerException
 * @throws InvalidArgumentException
 */
function assert_caller_func($expectedFuncName) {
	$stack = debug_backtrace();

	if(count($stack) < 3) throw new LogicException('Call tree is too small.');

	if(func_num_args() > 1 && !in_array($stack[2]['function'], func_get_args())) throw new AssertCallerException("[".implode(',', func_get_args())."]", $expectedFuncName);
	elseif(is_array($expectedFuncName) && !in_array($stack[2]['function'], $expectedFuncName)) throw new AssertCallerException("[".implode(',', $expectedFuncName)."]", $expectedFuncName);
	elseif(func_num_args() == 1 && is_string($expectedFuncName) && $stack[2]['function'] != $expectedFuncName) throw new AssertCallerException( $stack[2]['function'], $expectedFuncName);
	elseif(!(func_num_args() > 1 || is_string($expectedFuncName) || is_array($expectedFuncName)))throw new InvalidArgumentException();
}

/**
 * @param Scalar $actual
 * @param Scalar $expected
 * @throws AssertEqualException
 */
function assert_equal($actual, $expected) {
	if($actual != $expected) throw new AssertEqualException($actual, $expected);
}