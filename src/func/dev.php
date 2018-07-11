<?php
/**
 * Bitmobile
 * 작성자: Kyeongdae
 * 일자: 14. 6. 25
 * 시간: 오후 3:38
 */

/**
 * 디버그를 하기위하여 표시모드를 텍스트로 바꿈
 *
 * @param string $encoding 인코딩
 */
function dev_switch_to_text_mode($encoding = 'utf-8') {
	getApplicationContext()->getResponseContext()->setContextType('text/plain', $encoding);
}

/**
 * 간단한 로그 파일을 만든다.
 * @param string $fileName
 * @param string $content
 * @param bool   $append
 * @throws \classes\io\exception\DirectoryNotFoundException
 * @throws \classes\io\exception\PermissionException
 */
function dev_log_simpled($fileName, $content, $append = false) {
	if(function_exists('xdebug_get_code_coverage') && ini_get('html_errors')) {
		$fileName = $fileName.'.html';
	}

	$path = _DIR_LOG_PHP_USR_ . $fileName;

	if (!$append && file_exists($path)) {
		$ext = pathinfo($path, PATHINFO_EXTENSION);
		$name = pathinfo($path, PATHINFO_FILENAME);
		$dir = dirname($path);

		rename($path, $dir . DIRECTORY_SEPARATOR . $name . '.' . time() . '.' . $ext);

		\classes\io\File::writeAllText($path, $content);
	} elseif ($append && file_exists($path)) {
		\classes\io\File::appendAllText($path, PHP_EOL . $content);
	} else {
		\classes\io\File::appendAllText($path, $content);
	}
}

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