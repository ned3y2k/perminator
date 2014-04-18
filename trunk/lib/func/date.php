<?php
define('DATE_REMAIN_DAY', 86400); // 24h * 60m * 60s
define('DATE_REMAIN_HOUR', 3600); // 60m * 60s;
define('DATE_REMAIN_MINUTE', 60); // 60s;
define('DATE_REMAIN_SECOND', 1);

/** 일요일 */
define('DATE_DAYS_SUN',0);
/** 월요일 */
define('DATE_DAYS_MON',1);
/** 화요일 */
define('DATE_DAYS_TUE',2);
/** 수요일 */
define('DATE_DAYS_WED',3);
/** 목요일 */
define('DATE_DAYS_THU',4);
/** 금요일 */
define('DATE_DAYS_FRI',5);
/** 토요일 */
define('DATE_DAYS_SAT',6);

// $date_days_label = array('일', '월', '화', '수', '목', '금', '토');
$date_days_label = array('일요일', '월요일', '화요일', '수요일', '목요일', '금요일', '토요일');

/**
 * 남을 일자를 계산한다.
 * date2 - date1 수식
 * @param string $date1
 * @param string $date2
 * @param int $type DATE_REMAIN_DAY, DATE_REMAIN_HOUR, DATE_REMAIN_MINUTE, DATE_REMAIN_SECOND
 * return int
 */
function date_remain_day($date1, $date2, $type = DATE_REMAIN_DAY) {
	if($date1 instanceof  \DateTime) $date1 = $date1->format('Y-m-d h:i:s');
	if($date2 instanceof  \DateTime) $date2 = $date2->format('Y-m-d h:i:s');

	return intval((strtotime($date2)-strtotime($date1)) / $type); // 나머지 날짜값이 나옵니다.
}

/**
 * 일자에 일수를 추가한다.
 * @param string $dateStr 일자
 * @param string $day 일수
 */
function date_add_day($dateStr, $day) {
	$date = new DateTime($dateStr);
	$date->add(new DateInterval("P{$day}D"));

	return $date->format('Y-m-d');
}

/**
 * mysql에 들어갈수 있는 일자 범위 set
 * @param string $start_date
 * @param string $end_date
 * @return string
 */
function date_extract_set($start_date, $end_date) {
	$dateSetArray = array();
	$dateSetArray[] = $start_date;
	$remain_day = date_remain_day($start_date, $end_date);

	for($i=1; $i<$remain_day; $i++) {
		$dateSetArray[] = date_add_day($start_date, $i);
	}
	$dateSetArray[] = $end_date;

	return implode(',', $dateSetArray);
}

/**
 * 일자를 분리한다
 * @param string $begin 끝일자
 * @param string $end 시작 일자
 * @return array[(DateTime)'begin', (int)'begin_day', (DateTime)'end', (int)'end_day', (int)'interval_hour']
 */
function date_explode($begin, $end) {
	$days = array();
	$dayInterval = new DateInterval('P1D');
	$begin = new DateTime($begin);
	$end = new DateTime($end);
	$_end = clone $end;
	$_end->modify('+1 day');
	foreach ((new DatePeriod($begin, $dayInterval, $_end)) as $i => $period) {
		/* @var $_begin \DateTime */
		$_begin = $period;
		if ($i) $_begin->setTime(0, 0, 0);
		if ($_begin > $end) break;

		/* @var $_end \DateTime */
		$_end = clone $_begin;
		$_end->setTime(24, 0, 0);
		if ($end < $_end) $_end = $end;

		$interval = $_end->diff($_begin);
		$_interval_hour = ($interval->d * 24) + $interval->h + ($interval->i > 0 ? 1 : 0);

		$days[] = array(
			'begin' => $_begin,
			'begin_day' => intval($_begin->format('w')),
			'end' => $_end,
			'end_day' => intval($_end->format('w')),
			'interval_hour'=> $_interval_hour
		);
	}
	return $days;
}

/**
 * 현재 시간에서 남은 시간 계산
 * @param int $future_year
 * @param int $future_month
 * @param int $future_day
 * @param int $future_hour
 * @param int $future_min
 * @param int $future_sec
 * @return Map <string, number> hour, min, sec, day
 */
function date_separate_remain_day($future_year, $future_month, $future_day, $future_hour, $future_min, $future_sec) {
	// if ( $future_year <= date("Y") ) { $future_year = date("Y")+1; }
	$now = mktime ( date ( "H" ), date ( "i" ), date ( "s" ), date ( "m" ), date ( "d" ), date ( "Y" ) );
	$future = mktime ( $future_hour, $future_min, $future_sec, $future_month, $future_day, $future_year );

	if($now > $future) {
		$day = floor ( ($now - $future) / 86400 );

		$hour = floor ( (($now - $future) % (60 * 60 * 24)) / (60 * 60) ) + ($day * 24);
		$min = floor ( ((($now - $future) % (60 * 60 * 24)) % (60 * 60)) / (60) );
		$sec = floor ( (((($now - $future) % (60 * 60 * 24)) % (60 * 60)) % (60)) );
		if ($hour < 10) $hour = "0" . $hour;
		if ($min < 10) $min = "0" . $min;
		if ($sec < 10) $sec = "0" . $sec;

		return array ( 'hour' => $hour, 'min' => $min, 'sec' => $sec, 'day' => $day );
	} else {
		$day = floor ( ($future - $now) / 86400 );

		$hour = floor ( (($future - $now) % (60 * 60 * 24)) / (60 * 60) ) + ($day * 24);
		$min = floor ( ((($future - $now) % (60 * 60 * 24)) % (60 * 60)) / (60) );
		$sec = floor ( (((($future - $now) % (60 * 60 * 24)) % (60 * 60)) % (60)) );
		if ($hour < 10) $hour = "0" . $hour;
		if ($min < 10) $min = "0" . $min;
		if ($sec < 10) $sec = "0" . $sec;

		return array ( 'hour' => $hour, 'min' => $min, 'sec' => $sec, 'day' => $day );
	}
}

/**
 * 날짜에서 월, 일을 돌려줌
 * @param string $date
 * @return string[] month, day
 */
function date_month($date) {
	$dateTime = new DateTime ( $date );
	$month = $dateTime->format ( 'm' );
	$day = $dateTime->format ( 'd' );
	return array ( 'month' => $month, 'day' => $day );
}