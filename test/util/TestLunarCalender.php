<?php
/**
 * User: Kyeongdae
 * Date: 2015-02-12
 * Time: 오후 3:55
 */
use classes\test\BitTestCase;

class TestLunarCalender extends BitTestCase
{
	/**
	 * @throws Exception
	 */
    public function testSolarToLunar()
    {
        $cal = new \classes\util\calender\LunarCalender();
        $date = $cal->solarToLunar(new DateTime('1987-02-02'));
        $this->assertEquals(new \classes\util\calender\LunarDate(1987, 1, 5), $date);
    }

	/**
	 * @throws Exception
	 */
    public function testLunarToSolar()
    {
        $cal = new \classes\util\calender\LunarCalender();
        $lunarDate = new \classes\util\calender\LunarDate(1987, 1, 5);
        $date = $cal->lunarToSolar($lunarDate);
        $this->assertEquals("1987-02-02", $date->format('Y-m-d'));
    }
}
