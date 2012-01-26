<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * class: TikiDate
 *
 * This class takes care of all time/date conversions for
 * storing dates in the DB and displaying dates to the user.
 *
 * Dates are always stored in UTC in the database
 *
 * Created by: Jeremy Jongsma (jjongsma@tickchat.com)
 * Created on: Sat Jul 26 11:51:31 CDT 2003
 */

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

require_once("lib/pear/Date.php");
class TikiDate extends Date
{
	var $trad = array("January","February","March","April","May","June","July","August","September","October","November","December","Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday","Mon","Tue","Wed","Thu","Fri","Sat","Sun","of");
	var $translated_trad = array();
	
	/**
	 * Default constructor
	 */
	function TikiDate() {
		Date::Date(date("Y-m-d H:i:s Z"));
	}

	function format($format, $is_strftime_format = true) {
		global $prefs;

		// Format the date
		$return = $is_strftime_format ? parent::format($format) : parent::format3($format);

		// Translate the date if we are not already in english

		// Divide the date into an array of strings by looking for dates elements (specified in $this->trad)
		$words = preg_split('/('.implode('|',$this->trad).')/', $return, -1, PREG_SPLIT_DELIM_CAPTURE);

		// For each strings in $words array...
		$return = '';
		foreach ( $words as $w ) {
			if (array_key_exists($w, $this->translated_trad)) {
                // ... we've loaded this previously
				$return .= $this->translated_trad["$w"];
			} else if ( in_array($w, $this->trad) ) {
				// ... or we have a date element that needs a translation
				$t = tra($w,'',true);
				$this->translated_trad["$w"] = $t;
				$return .= $t;
			} else {
				// ... or we have a string that should not be translated
				$return .= $w;
			}
		}

		return $return;
	}

	function setDate($date, $format = DATE_FORMAT_ISO) {
		if (is_numeric($date)) {
			$this->setDate(gmdate("Y-m-d H:i:s", $date));
		} else {
			parent::setDate($date, $format);
		}
	}

	function getTimezoneId() {
		return $this->tz->getID();
	}

	static function TimezoneIsValidId($tz_id) {
		return Date_TimeZone::isValidID($tz_id);
	}

	function getTimeZoneList() {
		return $GLOBALS['_DATE_TIMEZONE_DATA'];
	}
}
