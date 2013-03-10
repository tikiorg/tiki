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
 * iCal (RFC 2245) class definition
 *
 * PHP version 5
 *
 * @package    iCal
 * @author     Gregory Szorc <gregory.szorc@case.edu>
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 */


//a recurrence set stores the recurrence and exception information about objects in components

/**
 * Easy to use object for dealing with an item's recurrence
 *
 * File_iCal_Recurrence is the user-implemented equivalent of the RECUR datatype in RFC 2445.
 *
 * @category    File
 * @package     iCal
 * @author      Gregory Szorc <gregory.szorc@case.edu>
 * @license     http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 *
 */
class File_iCal_Recurrence
{
    protected $_frequency;
    protected $_count, $_until;
    protected $_interval;
    protected $_bysecond = array();
    protected $_byminute = array();
    protected $_byhour = array();
    protected $_byday = array();
    protected $_bymonthday = array();
    protected $_byyearday = array();
    protected $_byweekno = array();
    protected $_bymonth = array();
    protected $_wkst;
    protected $_setpos = array();


    /**
     * Create a new recurrence object
     *
     * This will create an empty recurrence object.  It will set some default values.
     *
     */
    public function __construct() {
        $this->_interval = 1;
    }

    /**
     * Make the recurrence object recur every second
     *
     *
     */
    public function makeSecondly() {
        $this->_freqeuncy = ICAL_FREQUENCY_SECONDLY;
    }

    /**
     * Make the recurrence object recur every minute
     *
     *
     */
    public function makeMinutely() {
        $this->_frequency = ICAL_FREQUENCY_MINUTELY;
    }

    /**
     * Make the recurrence object recur every hour
     *
     *
     */
    public function makeHourly() {
        $this->_frequency = ICAL_FREQUENCY_HOURLY;
    }

    /**
     * Make the recurrence object recur every day
     *
     *
     */
    public function makeDaily() {
        $this->_frequency = ICAL_FREQUENCY_DAILY;
    }

    /**
     * Make the recurrence object recur every week
     *
     *
     */
    public function makeWeekly() {
        $this->_frequency = ICAL_FREQUENCY_WEEKLY;
    }

    /**
     * Make the recurrence object recur every month
     *
     *
     */
    public function makeMonthly() {
        $this->_frequency = ICAL_FREQUENCY_MONTHLY;
    }

    /**
     * Make the recurrence object recur every year
     *
     *
     */
    public function makeYearly() {
        $this->_frequency = ICAL_FREQUENCY_YEARLY;
    }

    public function getFrequency() {
        return $this->_frequency;
    }

    /**
     * Set the number of times to recur
     *
     */
    public function setCount($c) {
        if (ctype_digit($c)) {
            $this->_count = $c;
        }
        else {
            trigger_error("Count must be an integer", E_USER_WARNING);
        }
    }

    public function getCount() {
        return $this->_count;
    }


    /**
     * Set the date or time until which to recur
     *
     */
    public function setUntil($u) {
        $this->_until = $u;
    }

    public function getUntil() {
        return $this->_until;
    }

    /**
     * Set the frequency interval for which items recur
     *
     */
    public function setInterval($i) {
        if (ctype_digit($i)) {
            if ($i > 0) {
                $this->_interval = $i;
            } else {
                trigger_error("Count must not be 0", E_USER_WARNING);
            }
        } else {
            //generate a notice because interval is defined by the constructor
            trigger_error("Interval must be an integer", E_USER_NOTICE);
        }
    }

    public function getInterval() {
        return $this->_interval;
    }

    public function addSecond($s) {
        if ($s >= 0 && $s <= 59) {
            $this->_bysecond[] = $s;
        }
    }

    public function deleteSecond($s) {
        if ($key = array_search($s, $this->_bysecond)) {
            unset($this->_bysecond[$key]);
        }
    }

    public function getSeconds() {
        return $this->_bysecond;
    }

    public function addMinute($m) {
        if ($m >= 0 && $m <= 59) {
            $this->_byminute[] = $m;
        }
    }

    public function deleteMinute($m) {
        if ($key = array_search($m, $_byminute)) {
            unset($this->_byminute[$key]);
        }
    }

    public function getMinutes() {
        return $this->_byminute;
    }

    public function addHour($h) {
        if ($h >= 0 && $h <= 23) {
            $this->_byhour[] = $h;
        }
    }

    public function deleteHour($h) {
        if ($key = array_search($h, $_byhour)) {
            unset($this->_byhour[$key]);
        }
    }

    public function getHours() {
        return $this->_byhour;
    }

    //need to implement the +n prefix
    public function addDay($d, $i = null) {
        switch ($d) {
            case ICAL_DAY_SUNDAY:
            case ICAL_DAY_MONDAY:
            case ICAL_DAY_TUESDAY:
            case ICAL_DAY_WEDNESDAY:
            case ICAL_DAY_THURSDAY:
            case ICAL_DAY_FRIDAY:
            case ICAL_DAY_SATURDAY:
                if (!in_array($d, $this->_byday)) {
                    $this->_byday[] = $d;
                }
                break;

            default:
                trigger_error("Unknown day specified", E_USER_WARNING);
        }
    }

    public function deleteDay($d) {
        switch ($d) {
            case ICAL_DAY_SUNDAY:
            case ICAL_DAY_MONDAY:
            case ICAL_DAY_TUESDAY:
            case ICAL_DAY_WEDNESDAY:
            case ICAL_DAY_THURSDAY:
            case ICAL_DAY_FRIDAY:
            case ICAL_DAY_SATURDAY:
                if ($key = array_search($d, $this->_byday)) {
                    unset($this->_byday[$key]);
                }
                break;

            default:

        }
    }

    public function getDays() {
        return $this->_byday;
    }

    public function addMonthDay($m) {
        if (($m >= -31 && $m <= -1) || ($m >= 1 && $m <= 31)) {
            $this->_bymonthdayp[] = $m;
        }
    }

    public function deleteMonthDay($m) {
        if ($key = array_search($m, $this->_bymonthday)) {
            unset($this->_bymonthday[$key]);
        }
    }

    public function getMonthDays() {
        return $this->_bymonthday;
    }

    public function addYearDay($y) {
        if (($y >= -366 && $y <= -1) || ($y >= 1 && $y <= 366)) {
            $this->_byyearday[] = $y;
        }
    }

    public function deleteYearDay($y) {
        if ($key = array_search($y, $this->_byyearday)) {
            unset($this->_byyearday[$key]);
        }
    }

    public function getYearDays() {
        return $this->_byyearday;
    }

    public function addWeekNumber($w) {
        if (($w >= -53 && $w <= -1) || ($w >=1 && $w<=53)) {
            $this->_byweekno[] = $w;
        }
    }

    public function deleteWeekNumber($w) {
        if ($key = array_search($w, $this->_byweekno)) {
            unset($this->_byweekno[$key]);
        }
    }

    public function getWeekNumbers() {
        return $this->_byweekno;
    }

    public function addMonth($m) {
        if ($m >=1 && $m <= 12) {
            $this->_bymonth[] = $m;
        }
    }

    public function deleteMonth($m) {
        if ($key = array_search($m, $this->_bymonth)) {
            unset($this->_bymonth[$key]);
        }
    }

    public function getMonths() {
        return $this->_bymonth;
    }

    public function setWeekStart($s) {
        switch ($s) {
            case ICAL_DAY_SUNDAY:
            case ICAL_DAY_MONDAY:
            case ICAL_DAY_TUESDAY:
            case ICAL_DAY_WEDNESDAY:
            case ICAL_DAY_THURSDAY:
            case ICAL_DAY_FRIDAY:
            case ICAL_DAY_SATURDAY:
                $this->_wkst = $s;

            default:
                $this->_wkst = ICAL_DAY_MONDAY;

        }
    }

    public function getWeekStart() {
        return $this->_wkst;
    }

    public function addSetPos($p) {
        if (($p >= -366 && $p <= -1) || ($p >= 1 && $p <= 366)) {
            if (!in_array($p, $this->_setpos)) {
                $this->_setpos[] = $p;
            }
        }
    }

    public function deleteSetPos($p) {
        if ($key = array_search($p, $this->_setpos)) {
            unset($this->_setpos[$key]);
        }
    }

    public function getSetPos() {
        return $this->_setpos;
    }


}
