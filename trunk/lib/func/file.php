<?php
/**
 * 해당 파일의 확장자를 돌려줌
 * @param string $path
 * @return string
 */
function file_ext_name($path) { return pathinfo ( $path, PATHINFO_EXTENSION ); }


/**
 * 파일 업로드 처리
 * 마지막 디렉토리는 알아서 만든다!
 * @param string $key $_FILES에서 사용된 키
 * @param string $destinatonDir 업로드할 디렉토리
 * @param string $newName 변경될 파일 이름
 * @param boolean $returnFullPath 완전한 경로를 돌려줌(또는 파일명만 돌려줌
 * @param boolean $newNameKeepExtName 새파일명에 기존 파일 확장자를 유지함
 * @return NULL|string
 */
function file_upload_process($key, $destinatonDir, $newName = null, $returnFullPath = false, $newNameKeepExtName = true) {
	if (! array_key_exists ( $key, $_FILES ) || $_FILES [$key] ['error'] != 0) return null;

	$extName = file_ext_name ( $_FILES [$key] ['name'] );

	if (substr ( $destinatonDir, - 1 ) != '/') $destinatonDir .= '/';
	if (! file_exists ( $destinatonDir )) mkdir ( $destinatonDir );

	if(is_null($newName)) $newName = $_FILES [$key] ['name'];
	elseif($newNameKeepExtName) $newName .= ".{$extName}";

	if(!move_uploaded_file ( $_FILES [$key] ['tmp_name'], $destinatonDir.$newName )) throw new RuntimeException('file upload fail');

	return $returnFullPath ? $destinatonDir . $newName : $newName;
}

/**
 * 하위 디렉토리나 파일까지 제거
 * @param unknown $dir
 * @param string $deleteRootToo
 */
function file_unlink_recursive($dir, $deleteRootToo = true) {
	if (! file_exists ( $dir ) || ! $dh = @opendir ( $dir )) { return; }

	while ( false !== ($obj = readdir ( $dh )) ) {
		if ($obj == '.' || $obj == '..') continue;
		if (! @unlink ( $dir . DIRECTORY_SEPARATOR . $obj )) file_unlink_recursive ( $dir . DIRECTORY_SEPARATOR . $obj, true );
	}

	closedir ( $dh );

	if ($deleteRootToo) @rmdir ( $dir );

	return;
}