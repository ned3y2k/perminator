<?php
/**
 * 파일 업로드 처리
 * 마지막 디렉토리는 알아서 만든다!
 *
 * @param string $key $_FILES에서 사용된 키
 * @param string $destinatonDir 업로드할 디렉토리
 * @param string $newName 변경될 파일 이름
 * @param boolean $returnFullPath 완전한 경로를 돌려줌(또는 파일명만 돌려줌
 * @param boolean $newNameKeepExtName 새파일명에 기존 파일 확장자를 유지함
 *
 * @throws RuntimeException
 * @return NULL|string
 */
function file_upload_process($key, $destinatonDir, $newName = null, $returnFullPath = false, $newNameKeepExtName = true) {
	if (! array_key_exists ( $key, $_FILES ) || $_FILES [$key] ['error'] != 0) return null;

	$extName = pathinfo($_FILES [$key] ['name'] ,PATHINFO_EXTENSION);

	if (substr ( $destinatonDir, - 1 ) != '/') $destinatonDir .= '/';
	if (! file_exists ( $destinatonDir )) mkdir ( $destinatonDir, 7666);

	if($newName === null) $newName = $_FILES [$key] ['name'];
	elseif($newNameKeepExtName) $newName .= ".{$extName}";

	if(!move_uploaded_file ( $_FILES [$key] ['tmp_name'], $destinatonDir.$newName )) throw new RuntimeException('file upload fail');

	return $returnFullPath ? $destinatonDir . $newName : $newName;
}