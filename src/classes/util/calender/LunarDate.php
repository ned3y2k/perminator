<?php
/**
 * User: Kyeongdae
 * Date: 2015-02-12
 * Time: 오후 2:40
 */

namespace classes\util\calender;


/**
 * Class LunarDate
 * @package classes\util\calender
 */
class LunarDate {
    /** @var int 연도 */
    private $year;
    /** @var int 월 */
    private $month;
    /** @var int 일 */
    private $date;
    /** @var bool 윤달여부 */
    private $leapMonth;

    /**
     * @param int $year 연도
     * @param int $month 월
     * @param int $date 일
     * @param bool $leapMonth 윤달여부
     */
    function __construct($year, $month, $date, $leapMonth = false)
    {

        $this->year = $year;
        $this->month = $month;
        $this->date = $date;
        $this->leapMonth = $leapMonth;
    }

    /** @return int 연도 */
    public function getYear() { return $this->year; }

    /** @return int 월 */
    public function getMonth() { return $this->month; }

    /** @return int 일 */
    public function getDate() { return $this->date; }

    /** @return boolean 윤달여부 */
    public function isLeapMonth() { return $this->leapMonth; }
}