<?php
/**
* @package iCalendar Everything to generate simple iCal files
*/
/**
* We need the base class
*/
include_once('class.iCalBase.inc.php');
/**
* Container for a single Journal
*
* Tested with WAMP (XP-SP1/1.3.24/4.0.4/4.3.0)
* Last Change: 2003-03-29
*
* @desc Container for a single Journal
* @access private
* @author Michael Wimmer <flaimo@gmx.net>
* @copyright Michael Wimmer
* @link http://www.flaimo.com/
* @package iCalendar
* @version 1.031
*/
class iCalJournal extends iCalBase {

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
	* start date in iCal format
	*
	* @desc start date in iCal format
	* @var string
	* @access private
	*/
	var $startdate;

	/**
	* Timestamp of the creation date
	*
	* @desc Timestamp of the creation date
	* @var int
	* @access private
	*/
	var $created_ts;

	/**
	* creation date in iCal format
	*
	* @desc creation date in iCal format
	* @var string
	* @access private
	*/
	var $created;

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
	* @param (int) $start  Start time for the event (timestamp)
	* @param (int) $created  Creation date for the event (timestamp)
	* @param (int) $last_mod  Last modification date for the event (timestamp)
	* @param (int) $status  Status of the event (0 = TENTATIVE, 1 = CONFIRMED, 2 = CANCELLED)
	* @param (int) $class  (0 = PRIVATE | 1 = PUBLIC | 2 = CONFIDENTIAL)
	* @param (array) $organizer  The organizer – use array('Name', 'name@domain.com')
	* @param (array) $attendees  key = attendee name, value = e-mail, second value = role of the attendee [0 = CHAIR | 1 = REQ | 2 = OPT | 3 =NON] (example: array('Michi' => 'flaimo@gmx.net,1'); )
	* @param (array) $categories  Array with Strings (example: array('Freetime','Party'))
	* @param (int) $frequency  frequency: 0 = once, secoundly – yearly = 1–7
	* @param (mixed) $rec_end  recurrency end: ('' = forever | integer = number of times | timestring = explicit date)
	* @param (int) $interval  Interval for frequency (every 2,3,4 weeks…)
	* @param (string) $days  Array with the number of the days the event accures (example: array(0,1,5) = Sunday, Monday, Friday
	* @param (string) $weekstart  Startday of the Week ( 0 = Sunday – 6 = Saturday)
	* @param (string) $exept_dates  exeption dates: Array with timestamps of dates that should not be includes in the recurring event
	* @param (string) $url  optional URL for that event
	* @param (string) $lang  Language of the strings used in the event (iso code)
	* @param (string) $uid  Optional UID for the Journal
	* @return (void)
	* @uses setSummary(), iCalBase::setDescription(), setStartDate(), setCreated(), iCalBase::setLastMod(), iCalBase::setStatus(), iCalBase::setClass(), iCalBase::setOrganizer(), iCalBase::setAttendees(), iCalBase::setCategories(), iCalBase::setURL(), iCalBase::setLanguage(), iCalBase::setFrequency(), setRecEnd(), iCalBase::setInterval(), iCalBase::setDays(), iCalBase::setWeekStart(), iCalBase::setExeptDates(), iCalBase::setSequence(), setUID()
	* @access private
	* @since 1.000 - 2002/10/10
	*/
	function iCalJournal($summary, $description, $start, $created, $last_mod,
						 $status, $class, $organizer, $attendees, $categories,
						 $frequency, $rec_end, $interval, $days, $weekstart,
						 $exept_dates, $url, $lang, $uid) {

		parent::iCalBase();
		parent::setSummary($summary);
		parent::setDescription($description);
		$this->setStartDate($start);
		$this->setCreated($created);
		parent::setLastMod($last_mod);
		parent::setStatus($status);
		parent::setClass($class);
		parent::setOrganizer($organizer);
		parent::setAttendees($attendees);
		parent::setCategories($categories);
		parent::setURL($url);
		parent::setLanguage($lang);
		parent::setFrequency($frequency);
		$this->setRecEnd($rec_end);
		parent::setInterval($interval);
		parent::setDays($days);
		parent::setWeekStart($weekstart);
		parent::setExeptDates($exept_dates);
		parent::setSequence(0);
		$this->setUID($uid);
	} // end constructor

	/*-------------------*/
	/* F U N C T I O N S */
	/*-------------------*/

	/**
	* Sets the end for a recurring event (0 = never ending,
	* integer < 4 numbers = number of times, integer >= 4 enddate)
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
	* Set $created_ts variable
	*
	* @desc Set $created_ts variable
	* @param (int) $timestamp
	* @return (void)
	* @see getCreatedTS(), $created_ts
	* @access private
	* @since 1.000 - 2002/10/10
	*/
	function setCreatedTS($timestamp = 0) {
		if (is_int($timestamp) && $timestamp > 0) {
			$this->created_ts = (int) $timestamp;
		} // end if
	} // end function

	/**
	* Get $created_ts variable
	*
	* @desc Get $created_ts variable
	* @return (int) $created_ts
	* @see setCreatedTS(), $created_ts
	* @access public
	* @since 1.000 - 2002/10/10
	*/
	function &getCreatedTS() {
		return (int) $this->created_ts;
	} // end function

	/**
	* Set $startdate variable
	*
	* @desc Set $startdate variable
	* @param (int) $timestamp
	* @return (void)
	* @see getStartDate(), $startdate
	* @access private
	* @uses setStartDateTS()
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
	* Set $created variable
	*
	* @desc Set $created variable
	* @param (int) $timestamp
	* @return (void)
	* @see getCreated(), $created
	* @uses setCreatedTS()
	* @access private
	* @since 1.000 - 2002/10/10
	*/
	function setCreated($timestamp = 0) {
		$this->setCreatedTS($timestamp);
		$this->created = (string) date('Ymd\THi00\Z',$this->created_ts);
	} // end function

	/**
	* Get $created variable
	*
	* @desc Get $created variable
	* @return (string) $created
	* @see setCreated(), $created
	* @access public
	* @since 1.000 - 2002/10/10
	*/
	function &getCreated() {
		return (string) $this->created;
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
} // end class iCalJournal
?>
