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
* Container for a single todo
*
* Tested with WAMP (XP-SP1/1.3.24/4.0.4/4.3.0)
* Last Change: 2003-03-29
*
* @desc Container for a single todo
* @access private
* @author Michael Wimmer <flaimo@gmx.net>
* @copyright Michael Wimmer
* @link http://www.flaimo.com/
* @package iCalendar
* @version 1.031
*/
class iCalToDo extends iCalBase {

	/*-------------------*/
	/* V A R I A B L E S */
	/*-------------------*/


	/**
	* end date in iCal format
	*
	* @desc end date in iCal format
	* @var string
	* @access private
	*/
	var $completed;

	/**
	* Timestamp of the end date
	*
	* @desc Timestamp of the end date
	* @var int
	* @access private
	*/
	var $completed_ts;

	/**
	* start date in iCal format
	*
	* @desc start date in iCal format
	* @var string
	* @access private
	*/
	var $startdate;

	/**
	* Timestamp of the start date
	*
	* @desc Timestamp of the start date
	* @var int
	* @access private
	*/
	var $startdate_ts;

	/**
	* The percent completion of a ToDo
	*
	* @desc The percent completion of a ToDo
	* @var int
	* @access private
	*/
	var $percent;

	/**
	* Automaticaly created: md5 value of the start date + Summary
	*
	* @desc Automaticaly created: md5 value of the start date + Summary
	* @var string
	* @access private
	*/
	var $uid;

	/**
	* Duration of the ToDo in minutes
	*
	* @desc Duration of the ToDo in minutes
	* @var int
	* @access private
	*/
	var $duration;

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
	* @param (string) $summary  Title for the event
	* @param (string) $description  Description
	* @param (string) $location  Location
	* @param (int) $start  Start time for the event (timestamp)
	* @param (int) $duration  Duration of the todo in minutes
	* @param (int) $end  Start time for the event (timestamp)
	* @param (int) $percent  The percent completion of the ToDo
	* @param (int) $prio  riority = 0–9
	* @param (int) $status  Status of the event (0 = TENTATIVE, 1 = CONFIRMED, 2 = CANCELLED)
	* @param (int) $class  (0 = PRIVATE | 1 = PUBLIC | 2 = CONFIDENTIAL)
	* @param (array) $organizer  The organizer – use array('Name', 'name@domain.com')
	* @param (array) $attendees  key = attendee name, value = e-mail, second value = role of the attendee [0 = CHAIR | 1 = REQ | 2 = OPT | 3 =NON] (example: array('Michi' => 'flaimo@gmx.net,1'); )
	* @param (array) $categories  Array with Strings (example: array('Freetime','Party'))
	* @param (int) $last_mod  Last modification of the to-to (timestamp)
	* @param (array) $alarm  Array with all the alarm information, "''" for no alarm
	* @param (int) $frequency  frequency: 0 = once, secoundly – yearly = 1–7
	* @param (mixed) $rec_end  recurrency end: ('' = forever | integer = number of times | timestring = explicit date)
	* @param (int) $interval  Interval for frequency (every 2,3,4 weeks…)
	* @param (string) $days  Array with the number of the days the event accures (example: array(0,1,5) = Sunday, Monday, Friday
	* @param (string) $weekstart  Startday of the Week ( 0 = Sunday – 6 = Saturday)
	* @param (string) $exept_dates  exeption dates: Array with timestamps of dates that should not be includes in the recurring event
	* @param (string) $url  optional URL for that event
	* @param (string) $lang  Language of the strings used in the event (iso code)
	* @param (string) $uid  Optional UID for the ToDo
	* @return (void)
	* @uses iCalBase::setLanguage(), iCalBase::setOrganizer(), setStartDate(), setDuration(), setCompleted(), iCalBase::setLastMod(), setPercent(), setLocation(), iCalBase::setSequence(), iCalBase::setCategories(), iCalBase::setDescription(), setSummary(), iCalBase::setPriority(), iCalBase::setClass(), iCalBase::setAttendees(), iCalBase::setStatus(), iCalBase::setURL(), iCalBase::setFrequency(), setRecEnd(), iCalBase::setInterval(), iCalBase::setDays(), iCalBase::setWeekStart(), iCalBase::setExeptDates(), setAlarm(), setUID()
	* @access private
	* @since 1.020 - 2002/12/24
	*/
	function iCalToDo($summary, $description, $location, $start, $duration, $end,
					  $percent, $prio, $status, $class, $organizer, $attendees,
					  $categories, $last_mod, $alarm, $frequency, $rec_end,
					  $interval, $days, $weekstart, $exept_dates, $url, $lang, $uid) {

        parent::iCalBase();
		parent::setLanguage($lang);
		parent::setOrganizer($organizer);
		$this->setStartDate($start);
		$this->setDuration($duration);
		$this->setCompleted($end);
		parent::setLastMod($last_mod);
		$this->setPercent($percent);
		parent::setLocation($location);
		parent::setSequence(0);
		parent::setCategories($categories);
		parent::setDescription($description);
		parent::setSummary($summary);
		parent::setPriority($prio);
		parent::setClass($class);
		parent::setAttendees($attendees);
		parent::setStatus($status);
		parent::setURL($url);
		parent::setFrequency($frequency);
		$this->setRecEnd($rec_end);
		parent::setInterval($interval);
		parent::setDays($days);
		parent::setWeekStart($weekstart);
		parent::setExeptDates($exept_dates);
		$this->setAlarm($alarm);
        $this->setUID($uid);
	} // end constructor

	/*-------------------*/
	/* F U N C T I O N S */
	/*-------------------*/


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
            $rawid = (string) $this->startdate . 'plus' .  $this->summary;
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
	* Set $startdate variable
	*
	* @desc Set $startdate variable
	* @param (int) $timestamp
	* @return (void)
	* @see getStartDate(), $startdate
	* @uses setStartDateTS()
	* @access private
	* @since 1.000 - 2002/10/10
	*/
	function setStartDate($timestamp = 0) {
		$this->setStartDateTS($timestamp);
		$this->startdate = (string) date('Ymd\THi00\Z',$this->startdate_ts);
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
	* Set $completed_ts variable
	*
	* @desc Set $completed_ts variable
	* @param (int) $timestamp
	* @return (void)
	* @see getCompletedTS(), $completed_ts
	* @access private
	* @since 1.020 - 2002/12/24
	*/
	function setCompletedTS($timestamp = 0) {
		if (is_int($timestamp) && $timestamp > 0) {
			$this->completed_ts = (int) $timestamp;
		} // end if
	} // end function

	/**
	* Get $completed_ts variable
	*
	* @desc Get $completed_ts variable
	* @return (int) $completed_ts
	* @see setCompletedTS(), $completed_ts
	* @access public
	* @since 1.020 - 2002/12/24
	*/
	function &getCompletedTS() {
		return (int) $this->completed_ts;
	} // end function

	/**
	* Set $completed variable
	*
	* @desc Set $completed variable
	* @param (int) $completed
	* @return (void)
	* @see getCompleted(), $completed
	* @uses setCompletedTS()
	* @access private
	* @since 1.020 - 2002/12/24
	*/
	function setCompleted($timestamp = 0) {
		$this->setCompletedTS($timestamp);
		$this->completed = (string) date('Ymd\THi00\Z',$this->completed_ts);
	} // end function

	/**
	* Get $completed variable
	*
	* @desc Get $completed variable
	* @return (int) $completed
	* @see setCompleted(), $completed
	* @access public
	* @since 1.020 - 2002/12/24
	*/
	function &getCompleted() {
		return (string) $this->completed;
	} // end function

	/**
	* Set $duration variable
	*
	* @desc Set $duration variable
	* @param (int) $int
	* @return (void)
	* @see getPercent(), $percent
	* @access private
	* @since 1.020 - 2002/12/24
	*/
	function setPercent($int = 0) {
		$this->percent = (int) $int;
	} // end function

	/**
	* Get $percent variable
	*
	* @desc Get $percent variable
	* @return (int) $percent
	* @see setPercent(), $percent
	* @access private
	* @since 1.020 - 2002/12/24
	*/
	function &getPercent() {
		return (int) $this->percent;
	} // end function

	/**
	* Set $duration variable
	*
	* @desc Set $duration variable
	* @param (int) $int
	* @return (void)
	* @see getDuration(), $duration
	* @access private
	* @since 1.020 - 2002/12/24
	*/
	function setDuration($int = 0) {
		$this->duration = (int) $int;
	} // end function

	/**
	* Get $duration variable
	*
	* @desc Get $duration variable
	* @return (int) $duration
	* @see setDuration(), $duration
	* @access private
	* @since 1.020 - 2002/12/24
	*/
	function &getDuration() {
		return (int) $this->duration;
	} // end function

	/**
	* Sets the end for a recurring event
	* (0 = never ending, integer < 4 numbers = number of times, integer >= 4 enddate)
	*
	* @desc Sets the end for a recurring event
	* @param (int) $freq
	* @return (void)
	* @see getRecEnd(), $rec_end
	* @access private
	* @since 1.010 - 2002/10/26
	*/
	function setRecEnd($freq = '') {
		if (strlen(trim($freq)) < 1) {
			$this->rec_end = 0;
		}
		elseif (is_int($freq) && strlen(trim($freq)) < 4) {
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
	* Set $attendees variable
	*
	* @desc Set $attendees variable
	* @param (array) $attendees
	* @return (void)
	* @see getAttendees(), $attendees
	* @uses iCalAlarm
	* @access private
	* @since 1.001 - 2002/10/10
	*/
	function setAlarm($alarm = '') {
		if (is_array($alarm)) {
			$this->alarm = (object) new iCalAlarm($alarm[0], $alarm[1], $alarm[2],
												  $alarm[3], $alarm[4], $alarm[5],
												  $alarm[6], $this->lang);
		} // end if
	} // end function

	/**
	* Get $attendees variable
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
} // end class iCalToDo
?>
