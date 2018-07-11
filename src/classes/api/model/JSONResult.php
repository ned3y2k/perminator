<?php
namespace classes\api\model;

use app\classes\common\pool\AppEnvironmentPool;
use classes\lang\StringPool;
use classes\lang\UniqueObject;
use classes\runtime\serialization\json\IJsonUnserializable;
use classes\runtime\serialization\json\JSON;
use classes\runtime\serialization\json\JsonIgnoreField;
use classes\runtime\serialization\json\JsonSerializable;
use classes\runtime\serialization\json\JsonStdClassUnserializer;
use classes\util\ErrorDebugData;
use classes\util\ThrowableLogger;
use classes\web\HttpResponse;

/**
 * Class JSONResult
 * errorCode 에러 코드 (0은 정상 나머지는 에러 있음)
 * errorMsg 에러 메시지
 * data 응답 데이터
 * FIXME string 버퍼 데이터(임시로 string을 저장 하는 놈인데 사라져야한다)
 * @package classes\model
 */
class JSONResult extends UniqueObject implements JsonSerializable, IJsonUnserializable {
	protected $errorClassName;
	/** @var int 에러코드 */
	protected $errorCode = 0;
	/** @var string 에러 메시지 */
	protected $errorMsg = '';
	/** @var mixed 결과 데이터 */
	protected $data = null;
	/** @var IJSONResultStringier */
	protected $stringier;
	protected $extraData = array();
	/** @var bool */
	protected $dataSetLock = true;

	/**
	 * @param mixed                $data
	 * @param IJSONResultStringier $stringier
	 */
	public function __construct($data = null, IJSONResultStringier $stringier = null) {
		if (!$data)
			$this->dataSetLock = false;

		$this->data      = $data;
		$this->stringier = $stringier;
	}

	/**
	 * @param string $data
	 * @return IJsonUnserializable|mixed|null
	 * @throws \ReflectionException
	 */
	public static function fromJsonString(string $data) {
		return $data ? JSON::decode($data) : null;
	}

	/**
	 * @return null|string
	 * @throws \ReflectionException
	 */
	public function __toString() {
		static $stringPool = null;
		if ($stringPool == null)
			$stringPool = StringPool::getInstance();

		/** @noinspection PhpParamsInspection */
		if (!$stringPool->containsKey($this)) {
			$object = clone $this;

			if ($this->stringier != null)
				return $this->stringier->stringify($object);

			$stringPool->put(JSON::encode($object), $this);
		}

		return $stringPool->get($this);
	}

	/**
	 * @param bool  $requireSession
	 * @param bool  $compress
	 * @param array $headers
	 *
	 * @return HttpResponse
	 */
	public function createHttpResponse($requireSession = false, $compress = true, array $headers = null) {
		$res                         = new HttpResponse($requireSession);
		$res->getSetting()->compress = $compress;

		$res->putHeader('Content-type', self::createContentType());

		if ($headers != null) {
			foreach ($headers as $key => $val) {
				$res->putHeader($key, $val);
			}
		}

		$res->setBody($this);

		return $res;
	}

	/** @return string 구 브라우저 지원을 위한... */
	public static function createContentType() { return self::isNotSupportBrowser() ? 'text/plain' : 'application/json'; }

	/**
	 * 데이터를 넣는다.
	 *
	 * @param $key
	 * @param $value
	 */
	public function put($key, $value) {
		if ($this->dataSetLock)
			throw new \RuntimeException("put data is locked");

		static $stringPool = null;
		if ($stringPool == null)
			$stringPool = StringPool::getInstance();

		if ($this->data === null) $this->data = array();
		$this->data[$key] = $value;

		$stringPool->delete($this);
	}

	/**
	 * 기타 데이터를 넣을때 사용
	 *
	 * @param $key
	 * @param $value
	 */
	public function putExtraData($key, $value) {
		static $stringPool = null;
		if ($stringPool == null)
			$stringPool = StringPool::getInstance();

		$this->extraData[$key] = $value;

		$stringPool->delete($this);
	}

	/**
	 * 처리 결과중 에러가 있음을 알림
	 *
	 * @param string $errorMsg
	 * @param int $errorCode
	 * @param string $data
	 *
	 * @return JSONResult
	 * @throws \Exception
	 */
	public static function error($errorMsg = '', $errorCode = -1, $data = null) {
		$jsonResult            = new self();
		$jsonResult->errorCode = $errorCode;
		$jsonResult->errorMsg  = $errorMsg;
		$jsonResult->data      = $data;

		if (AppEnvironmentPool::getInstance()->develop) {
			$jsonResult->putExtraData("errorDebugData", ErrorDebugData::build());
		}

		return $jsonResult;
	}

	/**
	 * 예외를 Json 으로 출력
	 *
	 * @param \Throwable $throwable
	 *
	 * @return JSONResult
	 * @throws \Throwable
	 */
	public static function throwException(\Throwable $throwable) {
		if (TEST) {
			throw $throwable;
		}

		ThrowableLogger::getInstance()->writeObjectLog($throwable);
		if (getApplicationContext()->isDebug() && getApplicationContext()->getDebugFlag('jsonResultThrowExceptionRaw') == 1) {
			$msg = str_replace('#', "\n#", $throwable->getTraceAsString());
		} else {
			$msg = $throwable->getMessage();
		}

		if(getApplicationContext()->isDebug()) {
			$traces = $throwable->getTrace();
			$msg = $msg.' root has debug file. '.$traces[0]['file'].' '.$traces[0]['line'];
		}

		$result                 = self::error($msg, $throwable->getCode() == 0 ? -1 : $throwable->getCode()); // FIXME 예외 로깅 하는 부분이 있어야 한다.
		$result->errorClassName = get_class($throwable);

		return $result;
	}

	/**
	 * @return JSONResult
	 * @throws \Exception
	 */
	public static function requireAllField() {
		return self::error('모든 내용을 정확하게 입력하여주십시오.');
	}

	/**
	 * @return bool
	 */
	private static function isNotSupportBrowser() {
		return
			strpos($_SERVER['HTTP_USER_AGENT'], "Android 2.3") !== false ||
			strpos($_SERVER['HTTP_USER_AGENT'], "MSIE 6 ") !== false;
	}

	/**
	 * @param \stdClass $out
	 *
	 * @return void
	 */
	public function getSerializeConfig(\stdClass &$out) {
		$out->stringier   = JsonIgnoreField::getInstance();
		$out->dataSetLock = JsonIgnoreField::getInstance();
	}

	/**
	 * @param \stdClass|null $stdClass
	 * @throws \ReflectionException
	 */
	function jsonUnserialize(\stdClass $stdClass = null) {
		$this->errorClassName = $stdClass->errorClassName;
		$this->errorCode = $stdClass->errorCode;
		$this->errorMsg = $stdClass->errorMsg;
		$this->dataSetLock = true;
		$this->extraData[] = array();
		foreach ($stdClass->extraData as $data) {
			$this->data[] = JsonStdClassUnserializer::isJsonUnserializableStdClass($stdClass->data)
				? JsonStdClassUnserializer::createInstance($data)
				: $data;
		}

		if(JsonStdClassUnserializer::isJsonUnserializableStdClass($stdClass->data)) {
			$this->data = JsonStdClassUnserializer::createInstance($stdClass->data);
		} else {
			$this->data = $stdClass->data;
		}
	}
}