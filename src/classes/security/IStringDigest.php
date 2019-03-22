<?php
namespace classes\security;

interface IStringDigest {
	public static function digest($string);
}