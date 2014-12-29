<?php

/*
    PHP iCal Interface Library
    Copyright (C) 2005  Gregory Szorc <gregory.szorc@case.edu>

    This library is free software; you can redistribute it and/or
    modify it under the terms of the GNU Lesser General Public
    License as published by the Free Software Foundation; either
    version 2.1 of the License, or (at your option) any later version.

    This library is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
    Lesser General Public License for more details.

    You should have received a copy of the GNU Lesser General Public
    License along with this library; if not, write to the Free Software
    Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA

*/
/**
 *  iCal (RFC 2245) class definition
 *
 *  PHP version 5
 *
 *  @package    iCal
 *  @subpackage Parser
 *  @author     Gregory Szorc <gregory.szorc@case.edu>
 *  @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 */

/**
 * File_iCal_ValueDataTypes emulate the value types defined by RFC 2445.
 *
 * Valid ValueDataTypes should derive from this base class.
 *
 * @category File
 * @package iCal
 */
class File_iCal_ValueDataType
{
    protected $_value;  //may or may not be implemented

    public function getValue()
    {
        //might want to overwrite in child classes
        return $this->_value;
    }

}

/**
 * Implements a binary value type
 *
 * @category File
 * @package iCal
 */
class File_iCal_ValueDataType_Binary extends File_iCal_ValueDataType
{

}

/**
 * Implements a boolean value type
 *
 * @category File
 * @package iCal
 */
class File_iCal_ValueDataType_Boolean extends File_iCal_ValueDataType
{
    public function __construct($s)
    {
       if (strcasecmp($s, "true"))
       {
        $this->_value = "TRUE";
        return;
       }

       if (strcasecmp($s, "false"))
       {
        $this->_value = "FALSE";
        return;
       }

       //else
       trigger_error("Boolean value data type is not \"true\" or \"false\".  Value is $s", E_WARNING);
    }

    public function getValue()
    {
        return $this->_value;
    }
}

/**
 * Implements a calendar user address value type
 *
 * @category File
 * @package iCal
 */
class File_iCal_ValueDataType_CalendarUserAddress extends File_iCal_ValueDataType
{
    public function __construct($s) {
        $this->_value = new File_iCal_ValueDataType_URI($s);
    }

    public function getValue() {
        return $this->_value->getValue();
    }

}

/**
 * Implements a date value type
 *
 * @category File
 * @package iCal
 */
class File_iCal_ValueDataType_Date extends File_iCal_ValueDataType
{
    private $_year, $_month, $_day;

    public function __construct($date)
    {
        if (strlen($date) != 8) {
            //throw new Exception("Date data type must be 8 characters in length");
        } else {
            $this->_year = substr($date, 0, 4);
            $this->_month = substr($date, 4, 2);
            $this->_day = substr($date, 6, 2);
        }
    }

    //returns YYYYmmdd
    public function getValue()
    {
      return date("Ymd", mktime(0,0,0, $this->_month, $this->_day, $this->_year));
    }

    public function getIntegerValue() {
        return mktime(0,0,0, $this->_month, $this->_day, $this->_year);
    }

    public function setYear($y)
    {
        if ($y > 0) {
            $this->_year = $y;
        } else {
            trigger_error("Year must be greater than 0", E_USER_WARNING);
        }
    }

    public function setMonth($m)
    {
        if ($m > 0) {
            $this->_month = $m;
        } else {
            trigger_error("Month must be greater than 0", E_USER_WARNING);
        }
    }

    public function setDay($d)
    {
        if ($d > 0) {
            $this->_day = $d;
        } else {
            trigger_error("Day must be greater than 0", E_USER_WARNING);
        }

    }

}

/**
 * Implements a date/time value type
 *
 * @category File
 * @package iCal
 */
class File_iCal_ValueDataType_DateTime extends File_iCal_ValueDataType
{
    private $_date, $_time;

    public function __construct($dt)
    {
        if (strlen($dt) == 15 || strlen($dt) == 16) {
            $this->_date = new File_iCal_ValueDataType_Date(substr($dt, 0, 8));
            $this->_time = new File_iCal_ValueDataType_Time(substr($dt, 9));
        } else {
            $this->_date = new File_iCal_ValueDataType_Date(date("Ymd", $dt));
            $this->_time = new File_iCal_ValueDataType_Time(date("His", $dt));
        }
    }

    public function getValue()
    {
        return $this->_date->getValue() . 'T' . $this->_time->getValue();
    }

    public function getIntegerValue() {
        return $this->_date->getIntegerValue() + $this->_time->getIntegerValue();
    }
}

/**
 * Implements a duration value type
 *
 * @category File
 * @package iCal
 */
class File_iCal_ValueDataType_Duration extends File_iCal_ValueDataType
{
    private $_plusminus;    //stores either '+' or '-'
    private $_length;   //duration in seconds

    public function __construct($d)
    {
        if ($d{0} == '+') {
            $this->_plusminus = '+';
        } else if ($d{0} == '-') {
            $this->_plusminus = '-';
        }
        else//if we have a week identifier
        {
            //$this->_plusminus = '+';
            $d = '+'.$d;
        }

        if ($d{1} != 'P') {
            trigger_error("Second character of duration value must be a 'P'.  Found a '".$d{1}."'.", E_USER_ERROR);
        }

        $dur = substr($d, 2);
        $this->_length = 0; //set default length to 0 seconds

        $match = null;

        //if we have a week identifier
        if ($w = strpos($dur, 'W'))
        {
            if ($i = preg_match('/^.*?(\d+)D/', $dur, $match)) {
                $this->_length += 7*24*60*60*$match[1];
            } else {
                trigger_error("At least one character before 'W' must be a digit", E_USER_ERROR);
            }
        }

        //if we have a day identifier
        if ($dee = strpos($dur, 'D')) {
            if ($i = preg_match('/^.*?(\d+)D/', $dur, $match)) {
                $this->_length += 24*60*60*$match[1];
            } else {
                trigger_error("At least one character before 'D' must be a digit", E_USER_ERROR);
            }
        }

        //if we have a hour identifier
        if (strpos($dur, 'H')) {
            if ($i = preg_match('/^.*?(\d+)H/', $dur, $match)) {
                $this->_length += 60*60*$match[1];
            } else {
                trigger_error("At least one character before 'H' must be a digit", E_USER_ERROR);
            }
        }

        //if we have a minute identifier
        if (strpos($dur, 'M')) {
            if ($i = preg_match('/^.*?(\d+)M/', $dur, $match)) {
                $this->_length += 60*$match[1];
            } else {
                trigger_error("At least one character before 'M' must be a digit", E_USER_ERROR);
            }
        }

        //if we have a second identifier
        if (strpos($dur, 'S')) {
            if ($i = preg_match('/^.*?(\d+)S/', $dur, $match)) {
                $this->_length += $match[1];
            } else {
                trigger_error("At least one character before 'S' must be a digit", E_USER_ERROR);
            }
        }


    }

    public function getValue()
    {
        $r = $this->_plusminus;

        $sec = $this->_length % 60;

        $left = $this->_length;
        $week = intval($left / (60*60*24*7));

        $left -= $week * 60 * 60 *24 * 7;

        $day = intval($left / (60*60*24));
        $left -= $day * 60 * 60 * 24;

        $hour = intval($left / (60*60));
        $left -= $hour * 60*60;

        $minute = $left / 60;
        $left -= $minute*60;



        $r .= 'P';

        if ($week) $r .= $week.'W';
        if ($day) $r .= $day.'D';

        if ($hour || $minute || $sec) $r .= 'T';

        if ($hour) $r .= $hour.'H';
        if ($minute) $r .= $minute.'M';
        if ($sec) $r .= $sec.'S';

        return $r;
    }

}

/**
 * Implements a float value type
 *
 * @category File
 * @package iCal
 */
class File_iCal_ValueDataType_Float extends File_iCal_ValueDataType
{
    public function __construct($s) {
        $this->_value = $s;
    }

}

/**
 * Implements an integer value type
 *
 * @category File
 * @package iCal
 */
class File_iCal_ValueDataType_Integer extends File_iCal_ValueDataType
{
    private $_plusminus;


    public function __construct($s)
    {
        if ($s{0} == '-') {
            $this->_plusminus = '-';
        } else if ($s{0} == '+') {
            $this->_plusminus = '+';
        } else {
//            $this->_plusminus = '+';
            $s = '+'.$s;
        }

        $num = substr($s, 1);

        if (ctype_digit($num)) {
            $this->_value = $num;
        } else {
            trigger_error("Integer value must consist of all digits", E_USER_ERROR);
        }

    }

    public function getValue()
    {
        return $this->_plusminus . $this->_value;
    }
}

/**
 * Implements a period-of-time value type
 *
 * @category File
 * @package iCal
 */
class File_iCal_ValueDataType_PeriodOfTime extends File_iCal_ValueDataType
{
    public function __construct($s) {
        $this->_value = $s;
    }

}

/**
 * Implements a recurrence rule value type
 *
 * @category File
 * @package iCal
 */
class File_iCal_ValueDataType_RecurrenceRule extends File_iCal_ValueDataType
{
    private $_frequency;
    private $_until;
    private $_count;
    private $_interval;
    private $_bysecond;
    private $_byminute;
    private $_byhour;
    private $_byday;
    private $_bymonthday;
    private $_byyearday;
    private $_byweekno;
    private $_bymonth;
    private $_bysetpos;
    private $_wkst;


    //takes string $s
    public function __construct($s) {
        if (substr($s, 0, 5) != "FREQ=") {
            trigger_error("Recurrence rule must begin with \"FREQ\"", E_USER_WARNING);
        } else {
           $sc = strpos($s, ';', 5);

            if (self::isValidFrequency(substr($s, 5, $sc - 5))) {
                $this->_frequency = substr($s, 5, $sc - 5);

                $arr = explode(';', substr($s, $sc+1));

                $kv = array();
                foreach ($arr as $v) {
                    $kv[] = explode('=', $v);
                }

                //$kv is an array of two-element arrays (hopefully)
                foreach ($kv as $v) {
                    if (!is_array($v)) {
                        trigger_error("RRULE value not formatted properly");
                    } else {
                        switch ($v[0]) {
                            case "UNTIL":
                                if ($this->_count) {
                                    trigger_error("RRULE value UNTIL cannot occur with COUNT value", E_USER_ERROR);
                                } else {
                                    $this->_until = $v[1];
                                }

                                break;

                            case "COUNT":
                                if ($this->_until) {
                                    trigger_error("RRULE value COUNT cannot occur with UNTIL value", E_USER_ERROR);
                                } else {
                                    $this->_count = $v[1];
                                }
                                break;

                            case "INTERVAL":
                                $this->_interval = $v[1];
                                break;


                            case "BYDAY":
                                $days = explode(',', $v[1]);
                                $this->_byday = $days;

                                break;

                            case "BYSECOND":
                                $this->_bysecond = explode(',', $v[1]);
                                braek;

                            case "BYMINUTE":
                                $this->_byminute = explode(',', $v[1]);
                                break;

                            case "BYHOUR":
                                $this->_byhour = explode(',', $v[1]);
                                break;

                            case "BYMONTHDAY":
                                $this->_bymonthday = explode(',', $v[1]);
                                break;

                            case "BYYEARDAY":
                                $this->_byyearday = explode(',', $v[1]);
                                break;

                            case "BYWEEKND":
                                $this->_byweeknd = explode(',', $v[1]);
                                break;

                            case "BYMONTH":
                                $this->_bymonth = explode(',' , $v[1]);
                                break;

                            case "BYSETPOS":
                                $this->_bysetpos = explode(',', $v[1]);
                                break;

                            case "WKST":
                                $this->_wkst = $v[1];

                                break;


                            default:
                                trigger_error("Unknown RRULE value ".$v[0], E_USER_WARNING);


                        }
                    }
                }


            } else {
                trigger_error("Bad frequency descriptor provided for RRULE", E_USER_ERROR);
            }


        }
    }

    public function getValue() {
        $r = "FREQ=".$this->_frequency;

        if ($this->_until) {
            $r.= ";UNTIL=".$this->_until;
        }

        if ($this->_count) {
            $r .= ";COUNT=".$this->_count;
        }

        if ($this->_interval) {
            $r .= ";INTERVAL=".$this->_interval;
        }

        if ($this->_bysecond) {
            $r .= ";BYSECOND=".implode(',',$this->_bysecond);
        }

        if ($this->_byminute) {
            $r .= ";BYMINUTE=".implode(',', $this->_byminute);
        }

        if ($this->_byhour) {
            $r .= ";BYHOUR=".implode(',', $this->_byhour);
        }

        if ($this->_byday) {
            $r .= ";BYDAY=".implode(',', $this->_byday);
        }

        if ($this->_bymonthday) {
            $r .= ";BYMONTHDAY=".implode(',', $this->_bymonthday);
        }

        if ($this->_byyearday) {
            $r .= ";BYYEARDAY=".implode(',', $this->_byyearday);
        }

        if ($this->_byweekno) {
            $r .= ";BYWEEKNO=".implode(',', $this->_byweekno);
        }

        if ($this->_bysetpos) {
            $r .= ";BYSETPOS=".implode(',', $this->_bysetpos);
        }

        if ($this->_wkst) {
            $r .= ";WKST=".$this->_wkst;
        }


        return $r;
    }

    //given a frequency string $s, return whether it is valid
    public static function isValidFrequency($s) {
        $good = array("SECONDLY", "MINUTELY", "HOURLY", "DAILY", "WEEKLY", "MONTHLY", "YEARLY");

        return in_array($s, $good);
    }

}

/**
 * Implements a text value type
 *
 * @category File
 * @package iCal
 */
class File_iCal_ValueDataType_Text extends File_iCal_ValueDataType
{
    public function __construct($text)
    {
        //need some type verification here
        $this->_value = $text;
    }

}

/**
 * Implements a time value type
 *
 * @category File
 * @package iCal
 */
class File_iCal_ValueDataType_Time extends File_iCal_ValueDataType
{
    private $_hour, $_minute, $_second;
    private $_Z = false;

    public function __construct($time)
    {
        $l = strlen($time);
        assert($l == 6 || $l == 7);
        $this->_hour = substr($time, 0, 2);
        $this->_minute = substr($time, 2, 2);
        $this->_second = substr($time, 4, 2);

        if ($l == 7) $this->_Z = true;
    }

    public function getValue()
    {
        $return = str_pad($this->_hour, 2, '0', STR_PAD_LEFT) . str_pad($this->_minute, 2, '0', STR_PAD_LEFT) . str_pad($this->_second, 2, '0', STR_PAD_LEFT);
        if ($this->_Z) return $return.'Z';
        return $return;
    }

    public function getIntegerValue() {
        return 60*60*$this->_hour + 60*$this->_minute + $this->_second;
    }

    public function setHour($h)
    {
        assert($h > 0 && $h < 24);
        $this->_hour = $h;
    }

    public function setMinute($m)
    {
        assert($m > 0 && $m < 60);
        $this->_minute = $m;
    }

    public function setSecond($s)
    {
        assert($s > 0 && $s <= 60);
        $this->_second = $s;
    }
}

/**
 * Implements a URI value type
 *
 * @category File
 * @package iCal
 */
class File_iCal_ValueDataType_URI extends File_iCal_ValueDataType
{

    //from http://www.gbiv.com/protocols/uri/rfc/rfc2396.html
    public static $regexp = "^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\\?([^#]*))?(#(.*))?";

    public function __construct($v) {
        if (preg_match(self::$regexp, $v)) {
            $this->_value = $v;
        } else {
            trigger_error("URI $v does not appear to be a valid uri", E_USER_WARNING);
        }
    }
}

/**
 * Implements a UTC offset value type
 *
 * @category File
 * @package iCal
 */
class File_iCal_ValueDataType_UTCOffset extends File_iCal_ValueDataType
{
    public function __construct($s) {
        $this->_value = $s;
    }

}
