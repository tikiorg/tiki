<?php
/**
 * class: TikiDate
 *
 * This class takes care of all time/date conversions for
 * storing dates in the DB and displaying dates to the user.
 *
 * The objectives are:
 *  - Dates will always stored in UTC in the database
 *  - Display dates will be computed based on the preferred
 *    display offset specified in the constructor
 *
 * TODO: As of 1.7, dates are still stored in server local time.
 * This should be changed for 1.7.1 (requires many module changes).
 *
 * Created by: Jeremy Jongsma (jjongsma@tickchat.com)
 * Created on: Sat Jul 26 11:51:31 CDT 2003
 */

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

require_once("Date.php");
class TikiDate extends Date {
	/**
	 * UTC offset to use for display
	 */
	var $display_offset;

	/**
	 * Current UTC offset of server
	 */
	var $server_offset;
	var $trad = array("January","February","March","April","May","June","July","August","September","October","Novembre","December","Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday","Mon","Tue","Wed","Thu","Fri","Sat","Sun","of");
	var $replace = NULL ;

	/**
	 * Default constructor
	 * $_display_offset: desired offset for date display, in minutes
	 */
	function TikiDate($_display_offset = 0) {
			Date::Date(date("Y-m-d H:i:s Z"));
		$this->display_offset = $_display_offset ;

		$this->server_offset = intval(date("Z"));
	}

	/**
	 * Convert a UTC timestamp to the preferred display offset.
	 * $timestamp: UTC timestamp to convert.
	 * returns: Display-offset timestamp.
	 */
	function getDisplayDateFromUTC($_timestamp) {
		return $_timestamp + $this->display_offset;
	}

	/**
	 * Convert a display-offset timestamp to UTC.
	 * $timestamp: Display timestamp to convert.
	 * returns: UTC timestamp.
	 */
	function getUTCFromDisplayDate($_timestamp) {
	        return $_timestamp - $this->display_offset;
	}

	/**
	 * Convert a UTC timestamp to the local server time.
	 * $timestamp: UTC timestamp to convert.
	 * returns: Server timestamp.
	 */
	function getServerDateFromUTC($_timestamp) {
		return $_timestamp + $this->server_offset;
	}

	/**
	 * Convert a local server timestamp to UTC.
	 * $timestamp: Server timestamp to convert.
	 * returns: UTC timestamp.
	 */
	function getUTCFromServerDate($_timestamp) {
	        return $_timestamp - $this->server_offset;
	}

	/**
	 * Convert a display timestamp to the local server time.
	 * $timestamp: Display timestamp to convert.
	 * returns: Server timestamp.
	 */
	function getServerDateFromDisplayDate($_timestamp) {
		return $this->getServerDateFromUTC($this->getUTCFromDisplayDate($_timestamp));
	}

	/**
	 * Convert a local server timestamp to a display timestamp.
	 * $timestamp: Server timestamp to convert.
	 * returns: Display timestamp.
	 */
	function getDisplayDateFromServerDate($_timestamp) {
		return $this->getDisplayDateFromUTC($this->getUTCFromServerDate($_timestamp));
	}

	/**
	 * Retrieve a current UTC timestamp.
	 */
	function getUTCTime() {
		return time() - $this->server_offset;
	}

	/**
	 * Get the name of the current timezone.
	 */
	function getTzName(){
			return $this->tz->shortname;
	}

	function format($format) {
		$output = parent::format($format);

		if ($this->replace == NULL) {
		  $this->replace = array_map("tra",$this->trad);
		}

	  	return str_replace($this->trad,$this->replace,$output);
	}

	function setDate($date,$format = DATE_FORMAT_ISO) {
		if (is_numeric($date)) {
			$this->setDate(gmdate("Y-m-d H:i:s", $date));
		} else {
			parent::setDate($date,$format);
		}
	}
}

?>
