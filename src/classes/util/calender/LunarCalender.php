<?php
/**
 * User: Kyeongdae
 * Date: 2015-02-12
 * Time: 오후 2:39
 * @link http://blog.munilive.com/gregorian-lunar-conversion-class/
 */

namespace classes\util\calender;


use classes\cache\CacheManagerPool;

/**
 * Class LunarCalender
 * @package classes\util\calender
 */
class LunarCalender
{
    /** @var array */
    private $dateCount = array(0, 29, 30, 29, 30);
    /**
     * 음력 달력의 달형태를 저장한다.
     * 각 해는 13월로 표현되고,  1 작은달, 2 큰달, 3 작은 윤달, 4 큰 윤달 이다. 0 은 윤달이 없는 해에 자리를 채우는 것이다.
     * 1881년 1월 30일은 음력 1881년 1월 1일 임으로 이를 기준으로 계산한다.
     * @var array
     **/
    private $lunarMonthType = array();
    //  var $accumulateLunarDate = array();

    /** @var array 양력을 음력으로 변환했던 내용 */
    private $solarToLunar = array();
    /** @var array 음력을 양력으로 변환했던 내용 */
    private $lunarToSolar = array();
    /** 태양력 시작 */
    const SOLAR_START = "1881-01-30";
    /** 음력 시작 */
    const LUNAR_START = '18810101';
	private $cacheManager;

	/**
	 * LunarCalender constructor.
	 * @throws \Exception
	 */
    function __construct() { $this->cacheManager = CacheManagerPool::getInstance(); }

    /**
     * $monthTypeMark = "1212122322121" . "1212121221220"; // 디버깅용 데이터.
     * $monthTypeMark 에 대응하는 날의수
     * @return string[]
     */
    private static function getMonthTypeMark()
    {
        static $monthTypeMark = null;
        if ($monthTypeMark == null) {
            $monthTypeMark = array(
                "1212122322121", "1212121221220", "1121121222120", "2112132122122", "2112112121220", "2121211212120", "2212321121212", "2122121121210", "2122121212120",
                "1232122121212", "1212121221220", "1121123221222", "1121121212220", "1212112121220", "2121231212121", "2221211212120", "1221212121210", "2123221212121", "2121212212120",
                "1211212232212", "1211212122210", "2121121212220", "1212132112212", "2212112112210", "2212211212120", "1221412121212", "1212122121210", "2112212122120", "1231212122212",
                "1211212122210", "2121123122122", "2121121122120", "2212112112120", "2212231212112", "2122121212120", "1212122121210", "2132122122121", "2112121222120", "1211212322122",
                "1211211221220", "2121121121220", "2122132112122", "1221212121120", "2121221212110", "2122321221212", "1121212212210", "2112121221220", "1231211221222", "1211211212220",
                "1221123121221", "2221121121210", "2221212112120", "1221241212112", "1212212212120", "1121212212210", "2114121212221", "2112112122210", "2211211412212", "2211211212120",
                "2212121121210", "2212214112121", "2122122121120", "1212122122120", "1121412122122", "1121121222120", "2112112122120", "2231211212122", "2121211212120", "2212121321212",
                "2122121121210", "2122121212120", "1212142121212", "1211221221220", "1121121221220", "2114112121222", "1212112121220", "2121211232122", "1221211212120", "1221212121210",
                "2121223212121", "2121212212120", "1211212212210", "2121321212221", "2121121212220", "1212112112210", "2223211211221", "2212211212120", "1221212321212", "1212122121210",
                "2112212122120", "1211232122212", "1211212122210", "2121121122210", "2212312112212", "2212112112120", "2212121232112", "2122121212110", "2212122121210", "2112124122121",
                "2112121221220", "1211211221220", "2121321122122", "2121121121220", "2122112112322", "1221212112120", "1221221212110", "2122123221212", "1121212212210", "2112121221220",
                "1211231212222", "1211211212220", "1221121121220", "1223212112121", "2221212112120", "1221221232112", "1212212122120", "1121212212210", "2112132212221", "2112112122210",
                "2211211212210", "2221321121212", "2212121121210", "2212212112120", "1232212122112", "1212122122120", "1121212322122", "1121121222120", "2112112122120", "2211231212122",
                "2121211212120", "2122121121210", "2124212112121", "2122121212120", "1212121223212", "1211212221220", "1121121221220", "2112132121222", "1212112121220", "2121211212120",
                "2122321121212", "1221212121210", "2121221212120", "1232121221212", "1211212212210", "2121123212221", "2121121212220", "1212112112220", "1221231211221", "2212211211220",
                "1212212121210", "2123212212121", "2112122122120", "1211212322212", "1211212122210", "2121121122120", "2212114112122", "2212112112120", "2212121211210", "2212232121211",
                "2122122121210", "2112122122120", "1231212122212", "1211211221220", "2121121321222", "2121121121220", "2122112112120", "2122141211212", "1221221212110", "2121221221210",
                "2114121221221"
            );
        }

        return $monthTypeMark;
    }

	/**
	 * 음력으로 돌려줌.
	 * @param \DateTime $dateTime
	 * @return LunarDate
	 * @throws \InvalidArgumentException 계산 불가 범위
	 * @throws \Exception
	 */
    function solarToLunar(\DateTime $dateTime)
    {
        $key = __CLASS__ . __METHOD__ . ':' . __LINE__ . '-' . $dateTime->format('Ymd');
        $instance = $this->cacheManager->get($key);
        if ($instance == null) {
            $year = intval($dateTime->format('Y'));
            $month = intval($dateTime->format('m'));
            $date = intval($dateTime->format('d'));

            $this->initDateIndex();
            list($nearSol, $nearLuna) = $this->getNearData($year, $month, $date);

            //키와 입력과의 날짜 차이만금, lunarPinDate에 더한다.
            $targetJD = cal_to_jd(CAL_GREGORIAN, $month, $date, $year);
            $keyJD = cal_to_jd(CAL_GREGORIAN, substr($nearSol, 4, 2), substr($nearSol, 6, 2), substr($nearSol, 0, 4));

            $diff = $targetJD - $keyJD;

            $lunarYear = intval(substr($nearLuna, 0, 4));
            $lunarMonth = intval(substr($nearLuna, 4, 2));
            $lunarDate = substr($nearLuna, 6, 2);
            $lunarLeapMonth = substr($nearLuna, 8, 1);

            $lunarDate += $diff;
            $instance = new LunarDate($lunarYear, $lunarMonth, $lunarDate, $lunarLeapMonth == 'L' ? 1 : 0);
            $this->cacheManager->put($key, $instance);
        }

        return $instance;
    }

    /**
     * 근처에 일자를 돌려줌
     * @param int $year
     * @param int $month
     * @param int $date
     * @return array
     * @throws \InvalidArgumentException
     */
    private function getNearData($year, $month, $date)
    {
        $ym = sprintf('%d%02d', $year, $month);
        $ymd = sprintf('%d%02d%02d', $year, $month, $date);

        if (!array_key_exists($ym, $this->solarToLunar)) {
            throw new \InvalidArgumentException('계산할수 있는 범위가 아닙니다.');
        }
        $pair = $this->solarToLunar[$ym];
        $lastLuna = '';
        $lastSol = "";
        foreach ($pair as $sol => $luna) {
            if ($ymd < $sol) {
                return array($lastSol, $lastLuna);
            } else if ($ymd == $sol) {
                return array($sol, $luna);
            }

            $lastSol = $sol;
            $lastLuna = $luna;
        }

        return array($lastSol, $lastLuna);
    }

	/**
	 * 음력에 대응하는 양력 날짜 구하기.
	 * @param LunarDate $lunarDate
	 * @return \DateTime
	 * @throws \Exception
	 */
    function lunarToSolar(LunarDate $lunarDate)
    {
        $key = sprintf(__CLASS__ . __METHOD__ . ':' . __LINE__ . "-%d%d%d%s", $lunarDate->getYear(), $lunarDate->getMonth(), $lunarDate->getMonth(), $lunarDate->isLeapMonth() ? 'L' : '');

        $instance = $this->cacheManager->get($key);
        if($instance == null) {
            $this->initDateIndex();
            $nearKey = sprintf('%d%02d%02d%s', $lunarDate->getYear(), $lunarDate->getMonth(), 1, $lunarDate->isLeapMonth() ? 'L' : ' ');

            if (!array_key_exists($nearKey, $this->lunarToSolar)) {
                throw new \InvalidArgumentException('계산할수 있는 범위가 아닙니다.');
            }

            $solarPinDate = $this->lunarToSolar[$nearKey];

            //키와 입력과의 날짜 차이만큼, $solarPinDate 더한다.
            $keyDate = substr($nearKey, 6, 2);
            $keyIsLeapMonth = ('L' == substr($nearKey, 8, 1) ? true : false);
            if ($keyIsLeapMonth != $lunarDate->isLeapMonth()) {
                throw new \InvalidArgumentException(($lunarDate->isLeapMonth() ? "윤달" : "평달") . "{$lunarDate->getYear()}-{$lunarDate->getMonth()}-{$lunarDate->getDate()}" . '는 없음.');
            }

            $diff = $lunarDate->getDate() - $keyDate;

            $instance = \DateTime::createFromFormat('Ymd', $solarPinDate);
            $instance->add(new \DateInterval('P' . $diff . 'D'));
            $this->cacheManager->put($key, $instance);
        }

        return $instance;
    }

	/**
	 * @throws \Exception
	 */
    public function initDateIndex()
    {
        $key = __CLASS__ . __METHOD__ . ':' . __LINE__;
        $index = $this->cacheManager->get($key);

        if ($index == null) {
            $solarDate = new \DateTime(self::SOLAR_START);
            $lastSol = $solarDate->format('Ymd');
            $lastLuna = self::LUNAR_START;
            $lunarYear = (int)substr(self::LUNAR_START, 0, 4);

            if($this->lunarMonthType == null) {
                $this->initLunarMonthType();
            }

            foreach ($this->lunarMonthType as $yearArr) {
                $lunarMonth = 0;
                foreach ($yearArr as $monthType) {
                    if ($monthType == '0')
                        continue;
                    $dcnt = $this->dateCount[$monthType];

                    $isLeapMonth = false;
                    if ($monthType == '3' || $monthType == '4')
                        $isLeapMonth = true;
                    else
                        $lunarMonth++;

                    $lunarYMD = sprintf('%d%02d%02d%s', $lunarYear, $lunarMonth, 1, $isLeapMonth ? 'L' : ' ');

                    if (!array_key_exists($solarDate->format('Ym'), $this->solarToLunar)) {
                        $this->solarToLunar[$solarDate->format('Ym')][$lastSol] = $lastLuna;
                    }

                    $this->solarToLunar[$solarDate->format('Ym')][$solarDate->format('Ymd')] = $lunarYMD;
                    $this->lunarToSolar[$lunarYMD] = $solarDate->format('Ymd');

                    $lastSol = $solarDate->format('Ymd');
                    $lastLuna = $lunarYMD;

                    $solarDate->add(new \DateInterval('P' . $dcnt . 'D'));
                }
                $lunarYear++;
            }

            $index = array($this->solarToLunar, $this->lunarToSolar);
            $this->cacheManager->put($key, $index);
        } else {
            $index = $this->cacheManager->get($key);
            list($this->solarToLunar, $this->lunarToSolar) = $index;
        }
    }

    private function initLunarMonthType()
    {
        $key = __CLASS__ . __METHOD__ . ':' . __LINE__;
        $instance = $this->cacheManager->get($key);

        if ($instance == null) {
            $instance = array();

            $perYear = self::getMonthTypeMark();
            foreach ($perYear as $yearData) {
                $instance[] = str_split($yearData);
            }

            $this->cacheManager->put($key, $instance);
        }

        $this->lunarMonthType = $instance;
    }
}