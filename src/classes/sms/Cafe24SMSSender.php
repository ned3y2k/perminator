<?php
namespace classes\sms;
use app\classes\common\pool\AppEnvironmentPool;

/**
 *
 * @author 경대
 *
 */
class Cafe24SMSSender implements SMSSender {
	private $id;
	private $pw;
	/**
	 * @var ICafe24SMSResultReceiver
	 */
	private $resultReceiver;

	/**
	 * Cafe24SMSSender constructor.
	 * @param ICafe24SMSResultReceiver|null $resultReceiver
	 * @throws \Exception
	 */
	public function __construct(ICafe24SMSResultReceiver $resultReceiver = null) {
		$siteEnvironmentDic   = AppEnvironmentPool::getInstance();
		$this->id             = $siteEnvironmentDic->sms_id;
		$this->pw             = $siteEnvironmentDic->sms_pw;
		$this->resultReceiver = $resultReceiver;
	}

	public function send($receiverNo, $senderNo, $message) {
		$sphone = explode('-', $senderNo);
		$this->sendACafe24Server($receiverNo, $message, $sphone [ 0 ], $sphone [ 1 ], $sphone [ 2 ]);
	}

	public function createCafe24Message($code) {
		switch (intval($code)) {
			case -100 :
				return " 서버 에러";
			case -101 :
				return " 변수 부족 에러";
			case -102 :
				return " 인증 에러";
			case -105 :
				return " 예약 시간 에러";
			case -110 :
				return " 1000건 이상 발송 불가";
			case -201 :
				return " sms 건수 부족 에러";
			case -202 :
				return " 문자 '됬'은 사용불가능한 문자입니다.";
			case -203 :
				return " sms 대량 발송 에러";
			case 1 :
				return " 서비스 번호 오류";
			case 2 :
				return " 메지시 구성 결여";
			case 3 :
				return " 메시지 포맷 오류";
			case 4 :
				return " 메시지 body길이 오류";
			case 5 :
				return " Connect 필요";
			case 99 :
				return " 기타 오류(DB오류시스템장애)";
			case 44 :
				return " 스팸메시지 차단(배팅, 바카라, 도박, 섹스, liveno1 ,카지노 등을 포함한 스팸메시지는 발송이 실패됩니다.)";
			case 3201 :
				return " 발송시각 오류";
			case 3202 :
				return " 폰넘버 오류";
			case 3203 :
				return " SMS 메시지 Base64 Encoding 오류";
			case 3204 :
				return " CallBack메시지 Base64 Encoding 오류)";
			case 3205 :
				return " 번호형식 오류";
			case 3206 :
				return " 전송 성공";
			case 3207 :
				return " 비가입자 결번 서비스정지";
			case 3208 :
				return " 단말기 Power-off 상태";
			case 3209 :
				return " 음영";
			case 3210 :
				return " 단말기 메시지 FULL";
			case 3211 :
				return " 기타에러(이통사)";
			case 3214 :
				return " 기타에러(무선망)";
			case 3213 :
				return " 번호이동관련";
			case 3217 :
				return " 조합메시지 형식오류";
			case 3218 :
				return " 메시지 중복 오류";
			case 3219 :
				return " 월 송신건수 초과";
			case 3220 :
				return " UNKNOWN";
			case 3221 :
				return " 착신번호 에러(자리수 에러)";
			case 3222 :
				return " 착신번호 에러(없는 국번)";
			case 3223 :
				return " 수신거부 메시지 부분 없음";
			case 3224 :
				return " 21시 이후 광고";
		}

		throw new \UnsupportedOperationException();
	}

	/**
	 *
	 * @param string $tel destination
	 * @param string $msg message
	 * @param string $sphone1 sender phone no1
	 * @param string $sphone2 sender phone no2
	 * @param string $sphone3 sender phone no3
	 * @param string $type sms type '예약','결제','결제취소','관리자','미지정'
	 *
	 * @throws SMSException
	 */
	private function sendACafe24Server($tel, $msg, $sphone1, $sphone2, $sphone3, $type = '미지정') {
		$org_msg = $msg;
		/**
		 * ****************** 인증정보 *******************
		 */
		$sms_url = "http://sslsms.cafe24.com/sms_sender.php"; // 전송요청 URL
		// $sms_url = "https://sslsms.cafe24.com/sms_sender.php"; // HTTPS 전송요청 URL
		$sms [ 'user_id' ] = base64_encode($this->id); // SMS 아이디.
		$sms [ 'secure' ]  = base64_encode($this->pw); // 인증키
		$sms [ 'msg' ]     = base64_encode($msg);

		$sms [ 'rphone' ]  = base64_encode($tel);
		$sms [ 'sphone1' ] = base64_encode($sphone1);
		$sms [ 'sphone2' ] = base64_encode($sphone2);
		$sms [ 'sphone3' ] = base64_encode($sphone3);
		// $sms['rdate'] = base64_encode($_POST['rdate']);
		// $sms['rtime'] = base64_encode($_POST['rtime']);
		$sms [ 'mode' ]        = base64_encode("1"); // base64 사용시 반드시 모드값을 1로 주셔야 합니다.
		$sms [ 'returnurl' ]   = base64_encode('/');
		$sms [ 'testflag' ]    = '';
		$sms [ 'destination' ] = urlencode(base64_encode(''));
		// $returnurl             = '/';
		$sms [ 'repeatFlag' ] = base64_encode('');
		$sms [ 'repeatNum' ]  = base64_encode('');
		$sms [ 'repeatTime' ] = base64_encode('');

		$host_info = explode("/", $sms_url);
		$host      = $host_info [ 2 ];
		$path      = $host_info [ 3 ] . "/";

		srand(( double )microtime() * 1000000);
		$boundary = "---------------------" . substr(md5(rand(0, 32000)), 0, 10);
		// print_r($sms);

		// 헤더 생성
		$header = "POST /" . $path . " HTTP/1.0\r\n";
		$header .= "Host: " . $host . "\r\n";
		$header .= "Content-type: multipart/form-data, boundary=" . $boundary . "\r\n";

		$data = "";
		// 본문 생성
		foreach ($sms as $index => $value) {
			$data .= "--$boundary\r\n";
			$data .= "Content-Disposition: form-data; name=\"" . $index . "\"\r\n";
			$data .= "\r\n" . $value . "\r\n";
			$data .= "--$boundary\r\n";
		}
		$header .= "Content-length: " . strlen($data) . "\r\n\r\n";

		$fp = fsockopen($host, 80);

		if ($fp) {
			fputs($fp, $header . $data);
			$rsp = '';
			while (!feof($fp)) {
				$rsp .= fgets($fp, 8192);
			}
			fclose($fp);
			$msg       = explode("\r\n\r\n", trim($rsp));
			$rMsg      = explode(",", $msg [ 1 ]);
			$errorCode = $rMsg [ 0 ]; // 발송결과
			// $Count     = $rMsg [ 1 ]; // 잔여건수

			// 발송결과 알림
			if ($errorCode == "success" || $errorCode == "reserved") {
				$this->storeResult($type, '성공', $tel, var_export($msg, true));
			} else {
				$this->storeResult($type, '실패', $tel, $org_msg);
				throw new SMSException ($this->createCafe24Message($errorCode), $errorCode);
			}
		} else {
			$this->storeResult($type, '실패', $tel, $org_msg);
		}
	}

	/**
	 * 결과를 저장
	 * @param string $type
	 * @param int $status
	 * @param string $receiver
	 * @param string $comment
	 */
	private function storeResult($type, $status, $receiver, $comment) {
		if ($this->resultReceiver !== null) $this->resultReceiver->onReceiveCafe24SMSResult($type, $status, $receiver, $comment);
	}
}