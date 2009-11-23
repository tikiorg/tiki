<?php
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

class TikiDate {
	var $trad = array("January","February","March","April","May","June","July","August","September","October","November","December","Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday","Mon","Tue","Wed","Thu","Fri","Sat","Sun","of");
	var $translated_trad = array();
	var $date;
	var	$translation_array = array ("%a" => "D",
				"%A" => "l",
				"%b" => "M",
				"%B" => "F",
				"%C" => "",
				"%d" => "d",
				"%D" => "m/d/y",
				"%e" => "j",
				"%E" => "",
				"%g" => "",
				"%G" => "Y",
				"%h" => "G",
				"%H" => "H",
				"%i" => "h",
				"%I" => "h",
				"%j" => "z",
				"%m" => "m",
				"%M" => "i",
				"%o" => "P",
				"%O" => "P",
				"%p" => "a",
				"%P" => "A",
				"%r" => "h:i:s A",
				"%R" => "h:i",
				"%s" => "s",
				"%S" => "s",
				"%t" => "\t",
				"%T" => "h:i:s",
				"%u" => "N",
				"%U" => "W",
				"%V" => "W",
				"%w" => "w",
				"%W" => "W",
				"%y" => "y",
				"%Y" => "Y",
				"%Z" => "T");

	/**
	 * Default constructor
	 */
	function TikiDate() {

		if (function_exists('date_default_timezone_set')) {			// function not available < PHP 5.1
			
			if (isset($_SERVER['TZ']) && !empty($_SERVER['TZ'])) {	// apache - can be set in .htaccess
				$tz = $_SERVER['TZ'];
			} else if (ini_get('date.timezone')) {					// set in php.ini
				$tz = ini_get( 'date.timezone');
			} else if (getenv('TZ')) {								// system env setting
				$tz = getenv('TZ');
			} else {
				$tz = 'UTC';
			}
			date_default_timezone_set($tz);
		}
		
		$this->date = new DateTime();	// was: DateTime(date("Y-m-d H:i:s Z"))
										// the Z (timezone) param was causing an error
										// DateTime constructor defaults to "now" anyway so unnecessary?
		$this->search = array_keys($this->translation_array);
		$this->replace = array_values($this->translation_array);
	}

	static function getTimeZoneList() {
		$tz = array();
		$now = new DateTime("now",new DateTimeZone("GMT"));
		$tz_list = DateTimeZone::listIdentifiers();
		ksort($tz_list);
		foreach($tz_list as $tz_id) {
			$tmp_now = new DateTime("now",new DateTimeZone($tz_id));
			$tmp = $tmp_now->getOffset() - 3600*$tmp_now->format("I");
			$tz[$tz_id]['offset'] = $tmp*1000;
		}
		return $tz;
	}

	function format($format, $is_strftime_format = true) {
		global $prefs;

		// Format the date
		if ( $is_strftime_format ) {
			$format = preg_replace("/(?<!%)([a-zA-Z])/",'\\\$1',$format);
			$return = $this->date->format(str_replace($this->search,$this->replace,$format));
		} else {
			$return = $this->date->format($format);
		}

		// Translate the date if we are not already in english

		// Divide the date into an array of strings by looking for dates elements
		// (specified in $this->trad)
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

	function addDays($days) {
		if ($days >= 0)
			$this->date->modify("+$days day");
		else
			$this->date->modify("$days day");
	}

	function getTime() {
		return (int)$this->date->format("U");
	}

	function getWeekOfYear() {
		return (int)$this->date->format("W");
	}

	function setDate($date, $format = DATE_FORMAT_ISO) {
		if (is_numeric($date)) {
			$this->date = new DateTime(date("Y-m-d H:i:s", $date));
		} else {
			$this->date = new DateTime($date);
		}
	}
	
	function setLocalTime($day, $month, $year, $hour, $minute, $second, $partsecond ) {
		$this->date->setDate($year,$month,$day);
		$this->date->setTime($hour,$minute,$second);
	}

	function setTZbyID($tz_id) {
		if (!empty($tz_id)) {
			$this->date->setTimeZone(new DateTimeZone($tz_id));
		}
	}

	function convertTZbyID($tz_id) {
		if (!empty($tz_id)) {
			$this->date->setTimeZone(new DateTimeZone($tz_id));
		}
	}

	function getTimezoneId() {
		return $this->date->format("e");
	}

	static function TimezoneIsValidId($id) {
		return array_key_exists( strtolower($id), timezone_abbreviations_list() );
	}

}

class Date_Calc {

	function daysInMonth($month,$year) {
		return cal_days_in_month(CAL_GREGORIAN, $month, $year);
	}
}
