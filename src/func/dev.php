<?php
/**
 * Bitmobile
 * 작성자: Kyeongdae
 * 일자: 14. 6. 25
 * 시간: 오후 3:38
 */


/**
 * 파일로 로그를 남긴다.
 *
 * @param string $fileName
 * @param mixed  $value
 * @param bool   $viaLogSimpled
 * @throws \classes\io\exception\DirectoryNotFoundException
 * @throws \classes\io\exception\PermissionException
 */
function dev_var_export($fileName, $value, $viaLogSimpled = true) {
	if(function_exists('xdebug_get_code_coverage') && ini_get('html_errors')) {
		$fileName = $fileName.'.html';
	}

	ob_start();
	var_dump($value);
	$content = ob_get_contents();
	ob_end_clean();
	if($viaLogSimpled) {
		dev_log_simpled($fileName, $content);
	} else {
		\classes\io\File::writeAllText($fileName, $content);
	}

}