<?php
/**
* @package iCalendar Everything to generate simple iCal files
*/
/**
* Base Class for the different Modules
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
class iCalBase {

	/*-------------------*/
	/* V A R I A B L E S */
	/*-------------------*/


	/**
	* Detailed information for the module
	*
	* @desc Detailed information for the module
	* @var string
	* @access private
	*/
	var $description;

	/**
	* iso code language (en, de,…)
	*
	* @desc iso code language (en, de,…)
	* @var string
	* @access private
	*/
	var $lang;

	/**
	* Organizer of the module; $organizer[0] = Name, $organizer[1] = e-mail
	*
	* @desc Organizer of the module; $organizer[0] = Name, $organizer[1] = e-mail
	* @var array
	* @access private
	*/
	var $organizer = array('vCalEvent class', 'http://www.flaimo.com');

	/**
	* If not empty, contains a Link for that module
	*
	* @desc If not empty, contains a Link for that module
	* @var string
	* @access private
	*/
	var $url;

	/**
	* Headline for the module (mostly displayed in your cal programm)
	*
	* @desc Headline for the module
	* @var string
	* @access private
	*/
	var $summary;

	/**
	* set to 0
	*
	* @desc set to 0
	* @var int
	* @access private
	*/
	var $sequence;

	/**
	* List of short strings symbolizing the weekdays
	*
	* @desc List of short strings symbolizing the weekdays
	* @var array
	* @access private
	*/
	var $shortDaynames = array('SU','MO','TU','WE','TH','FR','SA');

	/**
	* String of days for the recurring module (example: “SU,MO”)
	*
	* @desc String of days for the recurring module
	* @var string
	* @access private
	*/
	var $rec_days;

	/**
	* If the method is REQUEST, all attendees are listet in the file
	*
	* key = attendee name, value = e-mail, second value = role of the attendee
	* [0 = CHAIR | 1 = REQ | 2 = OPT | 3 =NON] (example: array('Michi' => 'flaimo@gmx.net,1'); )
	*
	* @desc If the method is REQUEST, all attendees are listet in the file
	* @var array
	* @access private
	*/
	var $attendees = array();

	/**
	* Short string symbolizing the startday of the week
	*
	* @desc Short string symbolizing the startday of the week
	* @var string
	* @access private
	*/
	var $week_start = 1;

	/**
	* Location of the module
	*
	* @desc Location of the module
	* @var string
	* @access private
	*/
	var $location;

	/**
	* Array with the categories asigned to the module (example:
	* array('Freetime','Party'))
	*
	* @desc Array with the categories asigned to the module
	* @var array
	* @access private
	*/
	var $categories_array;

	/**
	* String with the categories asigned to the module
	*
	* @desc String with the categories asigned to the module
	* @var string
	* @access private
	*/
	var $categories;


	/**
	* 0 = once, 1-7 = secoundly - yearly
	*
	* @desc 0 = once, 1-7 = secoundly - yearly
	* @var int
	* @access private
	*/
	var $frequency;

	/**
	* If not empty, contains the status of the module
	* (0 = TENTATIVE, 1 = CONFIRMED, 2 = CANCELLED)
	*
	* @desc If not empty, contains the status of the module
	* @var int
	* @access private
	*/
	var $status;

	/**
	* interval of the recurring date (example: every 2,3,4 weeks)
	*
	* @desc
	* @var int
	* @access private
	*/
	var $interval = 1;

	/**
	* Exeptions dates for the recurring module (Array of timestamps)
	*
	* @desc Exeptions dates for the recurring module
	* @var array
	* @access private
	*/
	var $exept_dates;

	/**
	* PRIVATE (0) or PUBLIC (1) or CONFIDENTIAL (2)
	*
	* @desc PRIVATE (0) or PUBLIC (1) or CONFIDENTIAL (2)
	* @var int
	* @access private
	*/
	var $class;

	/**
	* set to 5 (value between 0 and 9)
	*
	* @desc set to 5 (value between 0 and 9)
	* @var int
	* @access private
	*/
	var $priority;

	/**
	* Timestamp of the last modification
	*
	* @desc Timestamp of the last modification
	* @var int
	* @access private
	*/
	var $last_mod_ts;

	/**
	* last modification date in iCal format
	*
	* @desc last modification date in iCal format
	* @var string
	* @access private
	*/
	var $last_mod;

	/*-----------------------*/
	/* C O N S T R U C T O R */
	/*-----------------------*/

	function iCalBase() {

	} // end constructor

	/*-------------------*/
	/* F U N C T I O N S */
	/*-------------------*/

	/**
	* Checks if a given string is a valid iso-language-code
	*
	* @desc Checks if a given string is a valid iso-language-code
	* @param (string) $code  String that should validated
	* @return (boolean) isvalid  If string is valid or not
	* @access protected
	* @since 1.001 - 2002/10/19
	*/
	function isValidLanguageCode($code = '') {
		$isvalid = (boolean) false;
		if (preg_match('(^([a-zA-Z]{2})((_|-)[a-zA-Z]{2})?$)',trim($code)) > 0) {
			$isvalid = (boolean) true;
		} // end if
		return (boolean) $isvalid;
	} // end function

	/**
	* Set $startdate variable
	*
	* @desc Set $startdate variable
	* @param (string) $isocode
	* @return (void)
	* @see getStartDate(), $startdate
	* @access private
	* @uses isValidLanguageCode()
	* @since 1.000 - 2002/10/10
	*/
	function setLanguage($isocode = '') {
		$this->lang = (string) (($this->isValidLanguageCode($isocode) == TRUE) ? ';LANGUAGE=' . $isocode : '');
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
	function &getLanguage() {
		return (string) $this->lang;
	} // end function

	/**
	* Set $description variable
	*
	* @desc Set $description variable
	* @param (string) $description
	* @return (void)
	* @see getDescription(), $description
	* @access private
	* @since 1.000 - 2002/10/10
	*/
	function setDescription($description) {
		$this->description = (string) $description;
	} // end function

	/**
	* Get $description variable
	*
	* @desc Get $description variable
	* @return (string) $description
	* @see setDescription(), $description
	* @access public
	* @since 1.000 - 2002/10/10
	*/
	function &getDescription() {
		return (string) $this->description;
	} // end function

	/**
	* Set $organizer variable
	*
	* @desc Set $organizer variable
	* @param (array) $organizer
	* @return (void)
	* @see getOrganizerName(), getOrganizerMail(), $organizer
	* @access private
	* @since 1.000 - 2002/10/10
	*/
	function setOrganizer($organizer = '') {
		if (is_array($organizer)) {
			$this->organizer = (array) $organizer;
		} // end if
	} // end function

	/**
	* Get name from $organizer variable
	*
	* @desc Get name from $organizer variable
	* @return (array) $organizer
	* @see setOrganizer(), getOrganizerMail(), $organizer
	* @access public
	* @since 1.011 - 2002/12/22
	*/
	function &getOrganizerName() {
		return (string) $this->organizer[0];
	} // end function

	/**
	* Get e-mail from $organizer variable
	*
	* @desc Get e-mail from $organizer variable
	* @return (array) $organizer
	* @see setOrganizer(), getOrganizerName(), $organizer
	* @access public
	* @since 1.011 - 2002/12/22
	*/
	function &getOrganizerMail() {
		return (string) $this->organizer[1];
	} // end function

	/**
	* Set $url variable
	*
	* @desc Set $url variable
	* @param (string) $url
	* @return (void)
	* @see getURL(), $url
	* @access private
	* @since 1.011 - 2002/12/22
	*/
	function setURL($url = '') {
		$this->url = (string) $url;
	} // end function

	/**
	* Get $url variable
	*
	* @desc Get $url variable
	* @return (string) $url
	* @see setURL(), $url
	* @access public
	* @since 1.011 - 2002/12/22
	*/
	function &getURL() {
		return (string) $this->url;
	} // end function

	/**
	* Set $summary variable
	*
	* @desc Set $summary variable
	* @param (string) $summary
	* @return (void)
	* @see getSummary(), $summary
	* @access private
	* @since 1.000 - 2002/10/10
	*/
	function setSummary($summary = '') {
		$this->summary = (string) $summary;
	} // end function

	/**
	* Get $summary variable
	*
	* @desc Get $summary variable
	* @return (string) $summary
	* @see setSummary(), $summary
	* @access public
	* @since 1.000 - 2002/10/10
	*/
	function &getSummary() {
		return (string) $this->summary;
	} // end function

	/**
	* Set $sequence variable
	*
	* @desc Set $sequence variable
	* @param (int) $int
	* @return (void)
	* @see getSequence(), $sequence
	* @access private
	* @since 1.000 - 2002/10/10
	*/
	function setSequence($int = 0) {
		$this->sequence = (int) $int;
	} // end function

	/**
	* Get $sequence variable
	*
	* @desc Get $sequence variable
	* @return (int) $sequence
	* @see setSequence(), $sequence
	* @access public
	* @since 1.000 - 2002/10/10
	*/
	function &getSequence() {
		return (int) $this->sequence;
	} // end function

	/**
	* Sets a string with weekdays of the recurring module
	*
	* @desc Sets a string with weekdays of the recurring event
	* @param (array) $recdays integers
	* @return (void)
	* @see getDays(), $rec_days
	* @access private
	* @since 1.010 - 2002/10/26
	*/
	function setDays($recdays = '') {
		$this->rec_days = (string) '';
		if (!is_array($recdays) || count($recdays) == 0) {
			$this->rec_days = (string) $this->shortDaynames[1];
		} else {
			if (count($recdays) > 1) {
				$recdays = array_values(array_unique($recdays));
			} // end if
			foreach ($recdays as $day) {
				if (array_key_exists($day, $this->shortDaynames)) {
					$this->rec_days .= (string) $this->shortDaynames[$day] . ',';
				} // end if
			} // end foreach
			$this->rec_days = (string) substr($this->rec_days,0,strlen($this->rec_days)-1);
		} // end if
	} // end function

	/**
	* Returns a string with recurring days
	*
	* @desc Returns a string with recurring days
	* @return (string) $rec_days
	* @see setDays(), $rec_days
	* @access public
	* @since 1.010 - 2002/10/26
	*/
	function &getDays() {
		return (string) $this->rec_days;
	} // end function

	/**
	* Sets the starting day for the week (0 = Sunday)
	*
	* @desc Sets the starting day for the week (0 = Sunday)
	* @param (int) $weekstart  0–6
	* @return (void)
	* @see getWeekStart(), $week_start
	* @access private
	* @since 1.010 - 2002/10/26
	*/
	function setWeekStart($weekstart = 1) {
		if (is_int($weekstart) && preg_match('(^([0-6]{1})$)', $weekstart)) {
			$this->week_start = (int) $weekstart;
		} // end if
	} // end function
	/**
	* Get the string from the $week_start variable
	*
	* @desc Get the string from the $week_start variable
	* @return (string) $shortDaynames
	* @see setWeekStart(), $week_start
	* @access public
	* @since 1.010 - 2002/10/26
	*/
	function &getWeekStart() {
		return (string) ((array_key_exists($this->week_start, $this->shortDaynames)) ? $this->shortDaynames[$this->week_start] : $this->shortDaynames[1]);
	} // end function

	/**
	* Set $attendees variable
	*
	* @desc Set $attendees variable
	* @param (array) $attendees
	* @return (void)
	* @see getAttendees(), $attendees
	* @access private
	* @since 1.001 - 2002/10/10
	*/
	function setAttendees($attendees = '') {
		if (is_array($attendees)) {
			$this->attendees = (array) $attendees;
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
	function &getAttendees() {
		return (array) $this->attendees;
	} // end function

	/**
	* Set $location variable
	*
	* @desc Set $location variable
	* @param (string) $location
	* @return (void)
	* @see getLocation(), $location
	* @access private
	* @since 1.000 - 2002/10/10
	*/
	function setLocation($location = '') {
		if (strlen(trim($location)) > 0) {
			$this->location = (string) $location;
		} // end if
	} // end function

	/**
	* Get $location variable
	*
	* @desc Get $location variable
	* @return (string) $location
	* @see setLocation(), $location
	* @access public
	* @since 1.000 - 2002/10/10
	*/
	function &getLocation() {
		return (string) $this->location;
	} // end function

	/**
	* Set $categories_array variable
	*
	* @desc Set $categories_array variable
	* @param (string) $categories
	* @return (void)
	* @see getCategoriesArray(), $categories_array
	* @access private
	* @since 1.000 - 2002/10/10
	*/
	function setCategoriesArray($categories = '') {
		$this->categories_array = (array) $categories;
	} // end function

	/**
	* Get $categories_array variable
	*
	* @desc Get $categories_array variable
	* @return (array) $categories_array
	* @see setCategoriesArray(), $categories_array
	* @access public
	* @since 1.000 - 2002/10/10
	*/
	function &getCategoriesArray() {
		return (array) $this->categories_array;
	} // end function

	/**
	* Set $categories variable
	*
	* @desc Set $categories variable
	* @param (string) $categories
	* @return (void)
	* @see getCategories(), $categories
	* @access private
	* @since 1.000 - 2002/10/10
	*/
	function setCategories($categories = '') {
		$this->setCategoriesArray($categories);
		$this->categories = (string) implode(',',$categories);
	} // end function

	/**
	* Get $categories variable
	*
	* @desc Get $categories variable
	* @return (string) $categories
	* @see setCategories(), $categories
	* @access public
	* @since 1.000 - 2002/10/10
	*/
	function &getCategories() {
		return (string) $this->categories;
	} // end function

	/**
	* Sets the frequency of a recurring event
	*
	* @desc Sets the frequency of a recurring event
	* @param (int) $int  Integer 0–7
	* @return (void)
	* @see getFrequency(), $frequencies
	* @access private
	* @since 1.010 - 2002/10/26
	*/
	function setFrequency($int = 0) {
		$this->frequency = (int) $int;
	} // end function

	/**
	* Get $frequency variable
	*
	* @desc Get $frequency variable
	* @return (string) $frequencies
	* @see setFrequency(), $frequencies
	* @access public
	* @since 1.010 - 2002/10/26
	*/
	function &getFrequency() {
		return (int) $this->frequency;
	} // end function

	/**
	* Set $status variable
	*
	* @desc Set $status variable
	* @param (int) $status
	* @return (void)
	* @see getStatus(), $status
	* @access private
	* @since 1.011 - 2002/12/22
	*/
	function setStatus($status = 1) {
		$this->status = (int) $status;
	} // end function

	/**
	* Get $status variable
	*
	* @desc Get $status variable
	* @return (string) $statuscode
	* @see setStatus(), $status
	* @access public
	* @since 1.011 - 2002/12/22
	*/
	function &getStatus() {
		return (int) $this->status;
	} // end function

	/**
	* Sets the interval for a recurring event (2 = every 2 [days|weeks|years|…])
	*
	* @desc Sets the interval for a recurring event
	* @param (int) $interval
	* @return (void)
	* @see getInterval(), $interval
	* @access private
	* @since 1.010 - 2002/10/26
	*/
	function setInterval($interval = 1) {
			$this->interval = (int) $interval;
	} // end function

	/**
	* Get $interval variable
	*
	* @desc Get $interval variable
	* @return (int) $interval
	* @see setInterval(), $interval
	* @access public
	* @since 1.010 - 2002/10/26
	*/
	function &getInterval() {
		return (int) $this->interval;
	} // end function

	/**
	* Sets an array of formated exeptions dates based on an array with timestamps
	*
	* @desc Sets an array of formated exeptions dates based on an array with timestamps
	* @param (array) $exeptdates
	* @return (void)
	* @see getExeptDates(), $exept_dates
	* @access private
	* @since 1.010 - 2002/10/26
	*/
	function setExeptDates($exeptdates = '') {
		if (!is_array($exeptdates)) {
			$this->exept_dates = (array) array();
		} else {
			foreach ($exeptdates as $timestamp) {
				$this->exept_dates[] = date('Ymd\THi00\Z',$timestamp);
			} // end foreach
		} // end if
	} // end function

	/**
	* Returns a string with exeptiondates
	*
	* @desc Returns a string with exeptiondates
	* @return (string) $return
	* @see setExeptDates(), $exept_dates
	* @access public
	* @since 1.010 - 2002/10/26
	*/
	function &getExeptDates() {
		$return = (string) '';
		foreach ($this->exept_dates as $date) {
			$return .= (string) $date . ',';
		} // end foreach
		$return = (string) substr($return,0,strlen($return)-1);
		return (string) $return;
	} // end function

	/**
	* Set $class variable
	*
	* @desc Set $class variable
	* @param (int) $int
	* @return (void)
	* @see getClass(), $class
	* @access private
	* @since 1.000 - 2002/10/10
	*/
	function setClass($int = 0) {
		$this->class = (int) $int;
	} // end function

	/**
	* Get $class variable
	*
	* @desc Get $class variable
	* @return (string) $class
	* @see setClass(), $class
	* @access public
	* @since 1.000 - 2002/10/10
	*/
	function &getClass() {
		return (int) $this->class;
	} // end function

	/**
	* Set $priority variable
	*
	* @desc Set $priority variable
	* @param (int) $int
	* @return (void)
	* @see getPriority(), $priority
	* @access private
	* @since 1.000 - 2002/10/10
	*/
	function setPriority($int = 5) {
		$this->priority = (int) ((is_int($int) && preg_match('(^([0-9]{1})$)', $int)) ? $int : 5);
	} // end function

	/**
	* Get $priority variable
	*
	* @desc Get $priority variable
	* @return (string) $priority
	* @see setPriority(), $priority
	* @access public
	* @since 1.000 - 2002/10/10
	*/
	function &getPriority() {
		return (int) $this->priority;
	} // end function

	/**
	* Set $last_mod_ts variable
	*
	* @desc Set $last_mod_ts variable
	* @param (int) $timestamp
	* @return (void)
	* @see getLastModTS(), $last_mod_ts
	* @access private
	* @since 1.020 - 2002/12/24
	*/
	function setLastModTS($timestamp = 0) {
		if (is_int($timestamp) && $timestamp > 0) {
			$this->last_mod_ts = (int) $timestamp;
		} // end if
	} // end function

	/**
	* Get $last_mod_ts variable
	*
	* @desc Get $last_mod_ts variable
	* @return (int) $last_mod_ts
	* @see setLastModTS(), $last_mod_ts
	* @access public
	* @since 1.020 - 2002/12/24
	*/
	function &getLastModTS() {
		return (int) $this->last_mod_ts;
	} // end function

	/**
	* Set $last_mod variable
	*
	* @desc Set $last_mod variable
	* @param (int) $last_mod
	* @return (void)
	* @see getLastMod(), $last_mod
	* @access private
	* @since 1.020 - 2002/12/24
	*/
	function setLastMod($timestamp = 0) {
		$this->setLastModTS($timestamp);
		$this->last_mod = (string) date('Ymd\THi00\Z',$this->last_mod_ts);
	} // end function

	/**
	* Get $last_mod variable
	*
	* @desc Get $last_mod variable
	* @return (int) $last_mod
	* @see setLastMod(), $last_mod
	* @access public
	* @since 1.020 - 2002/12/24
	*/
	function &getLastMod() {
		return (string) $this->last_mod;
	} // end function
} // end class iCalBase
?>