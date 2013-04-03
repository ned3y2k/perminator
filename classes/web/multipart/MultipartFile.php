<?php
namespace classes\web\multipart;

/**
 * @link http://static.springsource.org/spring/docs/1.2.x/api/org/springframework/web/multipart/MultipartFile.html
 *
 * All Known Implementing Classes:
 * CommonsMultipartFile: http://static.springsource.org/spring/docs/1.2.x/api/org/springframework/web/multipart/commons/CommonsMultipartFile.html
 *
 * @see MultipartHttpServletRequest, MultipartResolver
 */
interface MultipartFile {
	function getSize();

	/**
	 * @return boolean
	 */
	function isEmpty();

	/**
	 * @return string
	 */
	function getName();

	/**
	 * @return string
	 */
	function getFileName();

	/**
	 * @return string
	 */
	function getContentType();

	/**
	 * @param string $destinaton
	 * @throws IOException, IllegalStateException
	 */
	function transferTo($destinaton);
}