<?php
// class_calendar.php EXTENDS: None Abstract: No.
// 
// The calendar class provides basic functions to build calendars,
// like validating dates, getting day of week etc...
//
// Constructor:
//
// new calendar('lan')           : Language for the calendar valid options: (ar,br,en)
// Tiki: doesn't use the language translation, the translation is done at the calling level
//
// Methods:
//
// validDate($day,$month,$year)           : Returns true if date is valid.
// validTime($hour,$minute,$second)       : Returns true if time is valid.
// isLeap($year)                          : true/false
// nameOfMonth($monthnum,[$lan])          : Name of the month, language is optional.
// daysInMonth($monthnum,$year)           : Number of days in month for the given year.
// dayOfWeek($day,$month,$year)           : Day of week in 1-7 format.
// dayOfWeekStr($day,$month,$year,[$lan]) : String representing of day of week, language optional.
// getDisplayMatrix($day,$month,$year)    : Builds a 425 (7x6) matrix where
//                                          representing the calendar, the actual day appears as +NN
// buildMonthBox($name,$m)                : Builds an HTML select box for months,the second argument indicates selected month
// buildYearBox($name,$ny)                : Builds a select box for years
// buildDayOfWeekBox($name,$mul=false,$d=1): Builds a select box for days of the week, multiple selection may be allowed
// buildDayBox($name,$d,$m,$y)            : Bulds a select box for days
// buildHourBox($name,$h)                 : Builds a select box for hours
// buildMinBox($name,$h)                  : Builds a select box for minutes
// buildIntBox($name,$min,$max,$inter,$def=0) : Generic Select box indicating minimum, maximum, interval and default values
// buildIntBoxMul($name,$min,$max,$inter,$def=0,$cu=0) : Idem but allowing multiple selections

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class Calendar {
	var $lan;

	function Calendar($lan = 'en') {
		$this->lan = $lan;
	}

	function validDate($d, $m, $y) {
		return checkdate($m, $d, $y);
	}

	function buildYearBox($name, $ny) {
		print ("<select name=\"$name\">");

		print ("<option value=\"$ny\">$ny</option>");

		for ($i = 0; $i < 10; $i++) {
			$y = $ny + $i;

			print ("<option value=\"$y\">$y</option>");
		}

		print ("</select>");
	}

	function buildMonthBox($name, $m) {
		print ("<select name=\"$name\">");

		for ($i = 0; $i < 12; $i++) {
			$z = $i + 1;

			if ($z < 10) {
				$zst = '0' . $z;
			} else {
				$zst = $z;
			}

			$name = $this->nameOfMonth($z);

			if ($z == $m) {
				print ("<option value=\"$zst\" selected>$name</option>");
			} else {
				print ("<option value=\"$zst\">$name</option>");
			}
		}

		print ("</select>");
	}

	function buildDayOfWeekBox($name, $mul = false, $d = 1) {
		if ($mul) {
			$st = 'multiple size="1"';
		} else {
			$st = '';
		}

		print ("<select name=\"$name\" $st>");

		for ($i = 0; $i < 7; $i++) {
			$z = $i + 1;

			$dia = $this->dayOfWeekStrFromNo($z);

			if ($z == $d) {
				print ("<option value=\"$z\" selected>$dia</option>");
			} else {
				print ("<option value=\"$z\">$dia</option>");
			}
		}

		print ("</select>");
	}

	function buildDayBox($name, $d, $m, $y) {
		$cuantos = $this->daysInMonth($m, $y);

		print ("<select name=\"$name\">");

		for ($i = 0; $i < $cuantos; $i++) {
			$z = $i + 1;

			if ($z < 10) {
				$zst = '0' . $z;
			} else {
				$zst = $z;
			}

			if ($z == $d) {
				print ("<option value=\"$zst\" selected>$zst</option>");
			} else {
				print ("<option value=\"$zst\">$zst</option>");
			}
		}

		print ("</select>");
	}

	function buildHourBox($name, $h) {
		print ("<select name=\"$name\">");

		for ($i = 0; $i < 24; $i++) {
			if ($i < 10) {
				$zst = '0' . $i;
			} else {
				$zst = $i;
			}

			if ($i == $h) {
				print ("<option value=\"$zst\" selected>$zst</option>");
			} else {
				print ("<option value=\"$zst\">$zst</option>");
			}
		}

		print ("</select>");
	}

	function buildMinBox($name, $m) {
		print ("<select name=\"$name\">");

		for ($i = 0; $i < 60; $i++) {
			if ($i < 10) {
				$zst = '0' . $i;
			} else {
				$zst = $i;
			}

			if ($i == $m) {
				print ("<option value=\"$zst\" selected>$zst</option>");
			} else {
				print ("<option value=\"$zst\">$zst</option>");
			}
		}

		print ("</select>");
	}

	function buildMinBoxI($name, $m = 60) {
		print ("<select name=\"$name\">");

		for ($i = 5; $i < 66; $i += 5) {
			if ($i < 10) {
				$zst = '0' . $i;
			} else {
				$zst = $i;
			}

			if ($i == $m) {
				print ("<option value=\"$zst\" selected>$zst</option>");
			} else {
				print ("<option value=\"$zst\">$zst</option>");
			}
		}

		print ("</select>");
	}

	function buildIntBox($name, $min, $max, $inter, $def = 0) {
		print ("<select name=\"$name\">");

		for ($i = $min; $i <= $max; $i += $inter) {
			if ($i < 10) {
				$zst = '0' . $i;
			} else {
				$zst = $i;
			}

			if ($i == $def) {
				print ("<option value=\"$zst\" selected>$zst</option>");
			} else {
				print ("<option value=\"$zst\">$zst</option>");
			}
		}

		print ("</select>");
	}

	function buildIntBoxMul($name, $min, $max, $inter, $def = 0, $cu = 0) {
		print ("<select name=\"$name\" multiple size=\"$cu\">");

		for ($i = $min; $i <= $max; $i += $inter) {
			if ($i < 10) {
				$zst = '0' . $i;
			} else {
				$zst = $i;
			}

			if ($i == $def) {
				print ("<option value=\"$zst\" selected>$zst</option>");
			} else {
				print ("<option value=\"$zst\">$zst</option>");
			}
		}

		print ("</select>");
	}

	function getDisplayMatrix($d, $m, $y) {
		$dw = $this->dayOfWeek(1, $m, $y);

		$cu = $this->daysInMonth($m, $y);
		$hd = date('d');
		$hm = date('m');
		$hy = date('Y');

		//Inicializo la matriz horrible...
		for ($i = 0; $i < 42; $i++) {
			$mat[$i] = '  ';
		}

		for ($j = 0; $j < $cu; $j++) {
			$v = $j + 1;

			$mat[$j + ($dw - 1)] = "$v";

			if ($hm == $m && $hy == $y && $hd == $v) {
				$mat[$j + ($dw - 1)] = '+' . $mat[$j + ($dw - 1)] . '';
			}
		}

		return $mat;
	}

	function getPureMatrix($d, $m, $y) {
		$dw = $this->dayOfWeek(1, $m, $y);

		$cu = $this->daysInMonth($m, $y);

		//Inicializo la matriz horrible...
		for ($i = 0; $i < 42; $i++) {
			$mat[$i] = '  ';
		}

		for ($j = 0; $j < $cu; $j++) {
			$v = $j + 1;

			$mat[$j + ($dw - 1)] = "$v";
		}

		return $mat;
	}

	function validTime($h, $m, $s) {
		return (($h <= 24) && ($h >= 0) && ($m <= 60) && ($m >= 0) && ($s >= 0) && ($s <= 60));
	}

	// Returns true if the given year is 'leap' false if not. Year MUST use 4 digits!
	function isLeap($year) {
		if (($year % 4 == 0) && (($year % 100 <> 0) || ($year % 400 == 0))) {
			return true;
		} else {
			return false;
		}
	}

	// Returns the name of month in the given language.
	function nameOfMonth($month, $lan = false) {
		$month = ceil($month);

		if (!$lan) {
			$lan = $this->lan;
		}

		$en = array(
			'',
			'January',
			'February',
			'March',
			'April',
			'May',
			'June',
			'July',
			'August',
			'September',
			'October',
			'November',
			'December'
		);

		include_once('lib/init/tra.php');
		return tra($en[$month], $lan);
	}

	// Returns the number of days in the given month for a give 4 DIGITS year.
	function daysInMonth($month, $year) {
		if (substr($month, 0, 1) == '0') {
			$month = substr($month, 1, 1);
		}

		$vec = array(
			0,
			31,
			28,
			31,
			30,
			31,
			30,
			31,
			31,
			30,
			31,
			30,
			31
		);

		if ($this->isLeap($year)) {
			$vec[2] += 1;
		}

		return $vec[$month];
	}

	// Returns the day of week for the passed date. 1=Sun,2=Mon,...,7=Sat
	function dayOfWeek($dia, $mes, $anio) {
		$u = mktime(10, 1, 1, $mes, $dia, $anio);

		$w = date('w', $u);
		return $w + 1;
	}

	function dayOfWeekStrFromNo($x, $lan = false) {
		if (!$lan) {
			$lan = $this->lan;
		}

		$en = array(
			'',
			'Sunday',
			'Monday',
			'Tuesday',
			'Wednesday',
			'Thursday',
			'Friday',
			'Saturday'
		);

		include_once('lib/init/tra.php');
		return tra($en[$x], $lan);
	}

	// Returns the day_of_week in the language choosen.
	function dayOfWeekStr($dia, $mes, $anio, $lan = false) {
		if (!$lan) {
			$lan = $this->lan;
		}

		$en = array(
			'',
			'Sunday',
			'Monday',
			'Tuesday',
			'Wednesday',
			'Thursday',
			'Friday',
			'Saturday'
		);

		$w = $this->dayOfWeek($dia, $mes, $anio);
		include_once('lib/init/tra.php');
		return tra($en[$w], $lan);
	}
}

?>

<?php

// example
//$c=new calendar('en');
//$x=$c->day_of_week_str(2,3,2000,'po');
//print("$x\n");
//'ç'

?>
