<?php
/**
 * Created by PhpStorm.
 * User: Kyeongdae
 * Date: 2018-07-03
 * Time: 오후 2:38
 */

namespace classes\web\dispatch\resolver\clazz;


interface IControllerClassNameResolver {
	function resolve(string $providedClassName = null): string;
}