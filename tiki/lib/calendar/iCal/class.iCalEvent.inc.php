<?php
/**
* @package iCalendar Everything to generate simple iCal files
*/
/**
* We need the base class
*/
include_once('class.iCalBase.inc.php');
/**
* We need the child class
*/
include_once('class.iCalAlarm.inc.php');

/**
* Container for a single event
*
* Last Change: 2003-03-29
* Tested with WAMP (XP-SP1/1.3.24/4.0.4/4.3.0)
*
* @desc Container for a single event
* @access private
* @author Michael Wimmer <flaimo@gmx.net>
* @copyright Michael Wimmer
* @link http://www.flaimo.com/
* @package iCalendar
* @version 1.031
*/
class iCalEvent extends iCalBase {

	/*-------------------*/
	/* V A R I A B L E S */
	/*-------------------*/

	/**
	* Timestamp of the start date
	*
	* @desc Timestamp of the start date
	* @var int
	* @access private
	*/
	var $startdate_ts;

	/**
	* Timestamp of the end date
	*
	* @desc Timestamp of the end date
	* @var int
	* @access private
	*/
	var $enddate_ts;

	/**
	* start date in iCal format
	*
	* @desc start date in iCal format
	* @var string
	* @access private
	*/
	var $startdate;

	/**
	* end date in iCal format
	*
	* @desc end date in iCal format
	* @var string
	* @access private
	*/
	var $enddate;

	/**
	* OPAQUE (1) or TRANSPARENT (1)
	*
	* @desc OPAQUE (1) or TRANSPARENT (1)
	* @var int
	* @access private
	*/
	var $transp = 0;

	/**
	* Automaticaly created: md5 value of the start date + end date
	*
	* @desc Automaticaly created: md5 value of the start date + end date
	* @var string
	* @access private
	*/
	var $uid;

	/**
	* '' = never, integer < 4 numbers = number of times, integer >= 4 = timestamp
	*
	* @desc '' = never, integer < 4 numbers = number of times, integer >= 4 = timestamp
	* @var mixed
	* @access private
	*/
	var $rec_end;

	/**
	* If alarm is set, holds alarm object
	*
	* @desc If alarm is set, holds alarm object
	* @var object
	* @access private
	*/
	var $alarm;

	/*-----------------------*/
	/* C O N S T R U C T O R */
	/*-----------------------*/

	/**
	* Constructor
	*
	* Only job is to set all the variablesnames
	*
	* @desc Constructor
	* @param (array) $organizer  The organizer - use array('Name', 'name@domain.com')
	* @param (int) $start  Start time for the event (timestamp; if you want an allday event the startdate has to start at 00:00:00)
	* @param (int) $end  Start time for the event (timestamp or write 'allday' for an allday event)
	* @param (string) $location  Location
	* @param (int) $transp  Transparancy (0 = OPAQUE | 1 = TRANSPARENT)
	* @param (array) $categories  Array with Strings (example: array('Freetime','Party'))
	* @param (string) $description  Description
	* @param (string) $summary  Title for the event
	* @param (int) $class  (0 = PRIVATE | 1 = PUBLIC | 2 = CONFIDENTIAL)
	* @param (array) $attendees  key = attendee name, value = e-mail, second value = role of the attendee [0 = CHAIR | 1 = REQ | 2 = OPT | 3 =NON] (example: array('Michi' => 'flaimo@gmx.net,1'); )
	* @param (int) $prio  riority = 0–9
	* @param (int) $frequency  frequency: 0 = once, secoundly – yearly = 1–7
	* @param (mixed) $rec_end  recurrency end: ('' = forever | integer = number of times | timestring = explicit date)
	* @param (int) $interval  Interval for frequency (every 2,3,4 weeks…)
	* @param (string) $days  Array with the number of the days the event accures (example: array(0,1,5) = Sunday, Monday, Friday
	* @param (string) $weekstart  Startday of the Week ( 0 = Sunday - 6 = Saturday)
	* @param (string) $exept_dates  exeption dates: Array with timestamps of dates that should not be includes in the recurring event
	* @param (array) $alarm  Array with all the alarm information, "''" for no alarm
	* @param (int) $status  Status of the event (0 = TENTATIVE, 1 = CONFIRMED, 2 = CANCELLED)
	* @param (string) $url  optional URL for that event
	* @param (string) $language  Language of the strings used in the event (iso code)
	* @param (string) $uid  Optional UID for the event
	* @return (void)
	* @uses iCalBase::setLanguage(), iCalBase::setOrganizer(), setStartDate(), setEndDate(), iCalBase::setLocation(), setTransp(), iCalBase::setSequence(), iCalBase::setCategories(), iCalBase::setDescription(), iCalBase::setSummary(), iCalBase::setPriority(), iCalBase::setClass(), setUID(), iCalBase::setAttendees(), iCalBase::setFrequency(), setRecEnd(), iCalBase::setInterval(), iCalBase::setDays(), iCalBase::setWeekStart(), iCalBase::setExeptDates(), iCalBase::setStatus(), setAlarm(), iCalBase::setURL(), setUID()
	* @access private
	* @since 1.000 - 2002/10/10
	*/
	function iCalEvent($organizer, $start, $end, $location, $transp, $categories,
					   $description, $summary, $class, $attendees, $prio, $frequency,
					   $rec_end, $interval, $days, $weekstart, $exept_dates,
					   $alarm, $status, $url, $language, $uid) {
		parent::iCalBase();
		parent::setLanguage($language);
		parent::setOrganizer($organizer);
		$this->setStartDate($start);
		$this->setEndDate($end);
		parent::setLocation($location);
		$this->setTransp($transp);
		parent::setSequence(0);
		parent::setCategories($categories);
		parent::setDescription($description);
		parent::setSummary($summary);
		parent::setPriority($prio);
		parent::setClass($class);
		parent::setAttendees($attendees);
		parent::setFrequency($frequency);
		$this->setRecEnd($rec_end);
		parent::setInterval($interval);
		parent::setDays($days);
		parent::setWeekStart($weekstart);
		parent::setExeptDates($exept_dates);
		parent::setStatus($status);
		$this->setAlarm($alarm);
		parent::setURL($url);
        $this->setUID($uid);
	} // end constructor

	/*-------------------*/
	/* F U N C T I O N S */
	/*-------------------*/

	/**
	* Sets the end for a recurring event (0 = never ending,
	* integer < 4 numbers = number of times, integer >= 4 enddate)
	*
	* @desc Get $rec_end variable
	* @param (int) $freq
	* @return (void)
	* @see getRecEnd(), $rec_end
	* @access private
	* @since 1.010 - 2002/10/26
	*/
	function setRecEnd($freq = '') {
		if (strlen(trim($freq)) < 1) {
			$this->rec_end = 0;
		} elseif (is_int($freq) && strlen(trim($freq)) < 4) {
			$this->rec_end = $freq;
		} else {
			$this->rec_end = (string) date('Ymd\THi00\Z',$freq);
		} // end if
	} // end function

	/**
	* Get $rec_end variable
	*
	* @desc Get $rec_end variable
	* @return (mixed) $rec_end
	* @see setRecEnd(), $rec_end
	* @access public
	* @since 1.010 - 2002/10/26
	*/
	function &getRecEnd() {
		return $this->rec_end;
	} // end function

	/**
	* Set $startdate_ts variable
	*
	* @desc Set $startdate_ts variable
	* @param (int) $timestamp
	* @return (void)
	* @see getStartDateTS(), $startdate_ts
	* @access private
	* @since 1.000 - 2002/10/10
	*/
	function setStartDateTS($timestamp = 0) {
		if (is_int($timestamp) && $timestamp > 0) {
			$this->startdate_ts = (int) $timestamp;
		} else {
			$this->startdate_ts = (int) ((isset($this->enddate_ts) && is_numeric($this->enddate_ts) && $this->enddate_ts > 0) ? ($this->enddate_ts - 3600) : time());
		} // end if
	} // end function

	/**
	* Get $startdate_ts variable
	*
	* @desc Get $startdate_ts variable
	* @return (int) $startdate_ts
	* @see setStartDateTS(), $startdate_ts
	* @access public
	* @since 1.000 - 2002/10/10
	*/
	function &getStartDateTS() {
		return (int) $this->startdate_ts;
	} // end function

	/**
	* Set $enddate_ts variable
	*
	* @desc Set $enddate_ts variable
	* @param (int) $timestamp
	* @return (void)
	* @see getEndDateTS(), $enddate_ts
	* @access private
	* @since 1.000 - 2002/10/10
	*/
	function setEndDateTS($timestamp = 0) {
		if (is_int($timestamp) && $timestamp > 0) {
			$this->enddate_ts = (int) $timestamp;
		} else {
			$this->enddate_ts = (int) ((isset($this->startdate_ts) && is_numeric($this->startdate_ts) && $this->startdate_ts > 0) ? ($this->startdate_ts + 3600) : (time() + 3600));
		} // end if
	} // end function

	/**
	* Get $enddate_ts variable
	*
	* @desc Get $enddate_ts variable
	* @return (int) $enddate_ts
	* @see setEndDateTS(), $enddate_ts
	* @access public
	* @since 1.000 - 2002/10/10
	*/
	function &getEndDateTS() {
		return (int) $this->enddate_ts;
	} // end function

	/**
	* Set $startdate variable
	*
	* @desc Set $startdate variable
	* @param (int) $timestamp
	* @return (void)
	* @see getStartDate(), $startdate
	* @access private
	* @since 1.000 - 2002/10/10
	*/
	function setStartDate($timestamp = 0) {
		$this->setStartDateTS($timestamp);
		if (date('H:i:s', $this->startdate_ts) == '00:00:00') {
			$this->startdate = (string) date('Ymd',$this->startdate_ts);
		} else {
			$this->startdate = (string) date('Ymd\THi00\Z',$this->startdate_ts);
		} // end if
	} // end function

	/**
	* Get $startdate variable
	*
	* @desc Get $startdate variable
	* @return (int) $startdate
	* @see setStartDate(), $startdate
	* @access public
	* @since 1.000 - 2002/10/10
	*/
	function &getStartDate() {
		return (string) $this->startdate;
	} // end function

	/**
	* Set $enddate variable
	*
	* @desc Set $enddate variable
	* @param (mixed) $timestamp or 'allday'
	* @return (void)
	* @see getEndDate(), $enddate
	* @access private
	* @uses setEndDateTS()
	* @since 1.000 - 2002/10/10
	*/
	function setEndDate($timestamp = 0) {
		if (is_int($timestamp)) {
			$this->setEndDateTS($timestamp);
			$this->enddate = (string) date('Ymd\THi00\Z',$this->enddate_ts);
		} else {
			$this->enddate = (string) '';
		} // end if

	} // end function

	/**
	* Get $enddate variable
	*
	* @desc Get $enddate variable
	* @return (string) $enddate
	* @see setEndDate(), $enddate
	* @access public
	* @since 1.000 - 2002/10/10
	*/
	function &getEndDate() {
		return (string) $this->enddate;
	} // end function

	/**
	* Set $transp variable
	*
	* @desc Set $transp variable
	* @param (int) $int  0|1
	* @return (void)
	* @see getTransp(), $transp
	* @access private
	* @since 1.000 - 2002/10/10
	*/
	function setTransp($int = 0) {
		$this->transp = (int) $int;
	} // end function

	/**
	* Get $transp variable
	*
	* @desc Get $transp variable
	* @return (int) $transp
	* @see setTransp(), $transp
	* @access public
	* @since 1.000 - 2002/10/10
	*/
	function &getTransp() {
		$transps = (array) array('OPAQUE','TRANSPARENT');
		return (string) ((array_key_exists($this->transp, $transps)) ? $transps[$this->transp] : $transps[0]);
	} // end function

	/**
	* Set $uid variable
	*
	* @desc Set $uid variable
    * @param (int) $uid
	* @return (void)
	* @see getUID(), $uid
	* @access private
	* @since 1.000 - 2002/10/10
	*/
	function setUID($uid = 0) {
		if (strlen(trim($uid)) > 0) {
            $this->uid = (string) $uid;
        } else {
            $rawid = (string) $this->startdate . 'plus' .  $this->enddate;
            $this->uid = (string) md5($rawid);
        }
	} // end function

	/**
	* Get $uid variable
	*
	* @desc Get $uid variable
	* @return (string) $uid
	* @see setUID(), $uid
	* @access public
	* @since 1.000 - 2002/10/10
	*/
	function &getUID() {
		return (string) $this->uid;
	} // end function

	/**
	* Set $alarm object
	*
	* @desc Set $attendees variable
	* @param (array) $attendees
	* @return (void)
	* @see getAttendees(), $attendees
	* @access private
	* @since 1.001 - 2002/10/10
	*/
	function setAlarm($alarm = '') {
		if (is_array($alarm)) {
			$this->alarm = (object) new iCalAlarm($alarm[0], $alarm[1],
												  $alarm[2], $alarm[3], $alarm[4],
												  $alarm[5], $alarm[6], $this->lang);
		} // end if
	} // end function

	/**
	* Get $alarm object
	*
	* @desc Get $attendees variable
	* @return (string) $attendees
	* @see setAttendees(), $attendees
	* @access public
	* @since 1.001 - 2002/10/10
	*/
	function &getAlarm() {
		return ((is_object($this->alarm)) ? $this->alarm : FALSE);
	} // end function
} // end class iCalEvent
?>
