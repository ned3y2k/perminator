<?php
namespace classes\web;

use classes\io\exception\IOException;

class MultiPartFile {
	private $filePath;
	private $originalFileName;
	private $error;
	private $type;
	private $size;
	private $transferred = false;
	private $extensionName;

	/**
	 * @param string $fileName 클라이언트 실제 파일명
	 * @param string $originalFileName 서버로 업로드된 파일 이름
	 * @param int $size 파일 크기
	 * @param int $error 0이 정상 코드
	 * @param string $type 0 mime type
	 */
	public function __construct($fileName, $originalFileName, $size, $error = -1, $type = '') {
		$this->filePath         = $fileName;
		$this->originalFileName = $originalFileName;
		$this->type             = $type;
		$this->error            = $error;
		$this->size             = $size;
		$this->extensionName    = strtolower(pathinfo($originalFileName, PATHINFO_EXTENSION));
	}

	public function getContentType() {
		return $this->type;
	}

	public function getPath() {
		return $this->filePath;
	}

	public function getOriginalFilename() {
		return $this->originalFileName;
	}

	public function getExtensionName() {
		return $this->extensionName;
	}

	public function getSize() {
		return filesize($this->filePath);
	}

	public function getErrorCode() { return $this->error; }

	/**
	 * @return string
	 * @throws IOException
	 */
	public function getErrorMsg () {
		switch($this->error) {
			case UPLOAD_ERR_OK:         return 'UPLOAD_ERR_OK';
			case UPLOAD_ERR_INI_SIZE:   return 'UPLOAD_ERR_INI_SIZE';
			case UPLOAD_ERR_FORM_SIZE:  return 'UPLOAD_ERR_FORM_SIZE';
			case UPLOAD_ERR_PARTIAL:    return 'UPLOAD_ERR_PARTIAL';
			case UPLOAD_ERR_NO_FILE:    return 'UPLOAD_ERR_NO_FILE';
			case UPLOAD_ERR_NO_TMP_DIR: return 'UPLOAD_ERR_NO_TMP_DIR';
			case UPLOAD_ERR_CANT_WRITE: return 'UPLOAD_ERR_CANT_WRITE';
			case UPLOAD_ERR_EXTENSION:  return 'UPLOAD_ERR_EXTENSION';
		}

		throw new IOException("unknown error code");
	}

	public function isUploadSucceed() { return $this->error == 0; }

	public function isEmpty() {
		return filesize($this->filePath) == 0 || $this->error != 0;
	}

	/**
	 * PHP 기본 설정에서 Size 체크 하는 부분 필요
	 *
	 * @param $destinationPath
	 *
	 * @throws \classes\io\exception\IOException
	 */
	public function transferTo($destinationPath) {
		if($this->error != 0) {
			throw new IOException($this->getErrorMsg(), $this->getErrorCode());
		}

		if (!$this->transferred) {
			if (!move_uploaded_file($this->filePath, $destinationPath)) {
				if(!file_exists($this->filePath)) {
					throw new IOException("업로드된 파일을 이동할수 없습니다.");
				}

				@rename($this->filePath, $destinationPath);
				if(!file_exists($destinationPath)) {
					copy($this->filePath, $destinationPath);
					unlink($this->filePath);

					if(!file_exists($destinationPath)) throw new IOException("업로드된 파일을 이동할수 없습니다.");
				}
			}

			$this->transferred = true;
			$this->filePath    = $destinationPath;
		}
	}
}
