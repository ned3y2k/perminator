<?php
/**
 * BITMOBILE
 * 작성자: Kyeongdae
 * 일자: 14. 7. 7
 * 시간: 오후 1:01
 */
namespace classes\web;

use ApplicationContextPool;
use classes\io\exception\IOException;
use classes\lang\ArrayUtil;

class MultiPartFileResolver {
	/**
	 * @return self
	 * @throws IOException
	 */
	public static function getInstance() {
		static $instance = null;

		return $instance == null ? $instance = new self() : $instance;
	}

	/**
	 * MultiPartFileResolver constructor.
	 * @throws IOException
	 */
	function __construct() {
		if (strpos(ArrayUtil::getValue($_SERVER, 'CONTENT_TYPE', ''), 'multipart/form-data') === false || !ApplicationContextPool::get()->getRequestContext()->getRequestMethod()->isPost())
			throw new IOException('enctype이 multipart/form-data이 아니거나 get 방식입니다.');
	}

	/**
	 * @return MultiPartFile[]|MultiPartFile[][]|null
	 */
	public function resolve() {
		if (isset($_FILES) && is_array($_FILES) && count($_FILES) != 0) {
			$items = array();

			foreach ($_FILES as $key => $FILE) {
				if(!is_array($FILE[ 'tmp_name' ]))
					$items[ $key ] = new MultiPartFile($FILE[ 'tmp_name' ], $FILE[ 'name' ], $FILE[ 'size' ], $FILE[ 'error' ], $type = $FILE[ 'type' ]);
				elseif(is_array($FILE))
					$items[ $key ] = $this->resolveArray($FILE);
				else
					throw new \UnsupportedOperationException();
			}

			return $items;
		} else {
			return null;
		}
	}

	private function resolveArray(array $FILE) {
		$files = array();
		$keys = array_keys($FILE[ 'tmp_name' ]);
		foreach($keys as $key) {
			$files[] = new MultiPartFile($FILE[ 'tmp_name' ][$key], $FILE[ 'name' ][$key], $FILE[ 'size' ][$key], $FILE[ 'error' ][$key], $type = $FILE[ 'type' ][$key]);
		}

		return $files;
	}
} 