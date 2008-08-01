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
	
	/**
	 * Default constructor
	 */
	function TikiDate() {
		$this->date = new DateTime(date("Y-m-d H:i:s Z"));
	}

	function format($format) {
		global $prefs;
		$toto = array ("%a" => "D",
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
		$search = array_keys($toto);
		$replace = array_values($toto);

		//FIXME not quite good
		$format = preg_replace("/([^% ][a-zA-Z])/",'\\\$1',$format);
		// Format the date
		$return = $this->date->format(str_replace($search,$replace,$format));

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

	function addDays($days) {
		//echo "AVANT = ".$this->date->format("Y-m-d H:i:s");
		$this->date->modify("+$days day");
		//echo " APRES = ".$this->date->format("Y-m-d H:i:s")."<br/>";
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
//		echo "AVANT = ".$this->date->format("Y-m-d H:i:s")."<br/>";
	}
	
	function setLocalTime($day, $month, $year, $hour, $minute, $second, $partsecond ) {
		$this->date->setDate($year,$month,$day);
		$this->date->setTime($hour,$minute,$second);
		/*
		echo "month=$month,day=$day,year=$year ";
		echo $this->date->format("Y-m-d H:i:s");
		echo "<br/>";
		*/
	}

	function setTZbyID($tz_id) {
		$this->date->setTimeZone(new DateTimeZone(timezone_name_from_abbr($tz_id)));
		/*
		echo $this->date->format("Y-m-d H:i:s");
		echo "<br/>";
		*/
	}

	function convertTZbyID($tz_id) {
		$this->date->setTimeZone(new DateTimeZone(timezone_name_from_abbr($tz_id)));
		/*
		echo $this->date->format("Y-m-d H:i:s");
		echo "<br/>";
		*/
	}


}

?>
