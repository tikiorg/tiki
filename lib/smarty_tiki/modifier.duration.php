<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
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
function smarty_modifier_duration($string, $long=true)
{
	if (!is_numeric($string)) {
		return $string;
	}
	$values = array(31536000, 2628000, 604800, 86400, 3600, 60, 1);
	$output = array(tra('year'), tra('month'), tra('week'), tra('day'), tra('hour'), tra('minute'), tra('second'));
	$ouputs = array(tra('years'), tra('months'), tra('weeks'), tra('days'), tra('hours'), tra('minutes'), tra('seconds'));
	$result = array();
	foreach ($values as $i=>$value) {
		if ($string >= $value) {
			$nb = floor($string / $value);
			// add a zero before seconds or minutes with just one digit if $long == false
			$nb = (!$long && !empty($result) && ($i == 5 || $i == 6) && strlen($nb) == 1) ? 0 . $nb : $nb;
			$s = ($nb > 1)?$ouputs[$i]: $output[$i];
			$s = $long? " $s": substr($s, 0, 1);
			$string = $string % $value;
			$result[] = "$nb$s";
		}
	}
	return implode(' ',$result);
}
