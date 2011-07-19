<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     duration
 * Purpose:  formats a duration from seconds
 * -------------------------------------------------------------
 */
function smarty_modifier_duration_short($string) {
	$result='';
	if ($string > 60*60*24) {
		$days = floor($string/(60*60*24));
		$day_label = $days == 1 ? tra('day') : tra('days');
		$result = "$days " . $day_label;
	} elseif ($string > 60*60) {
		$hours = floor($string/(60*60));
		$hr_label = $hours == 1 ? tra('hour') : tra('hours');
		$result = "$hours " . $hr_label;
	} elseif ($string > 60) {
		$mins = floor($string/(60));
		$min_label = $mins == 1 ? tra('minute') : tra('minutes');
		$result = "$mins " . $min_label;
	} else {
		$sec_label = $string == 1 ? tra('second') : tra('seconds');
		$result = "$string " . $sec_label;
	}
	return $result;
}
