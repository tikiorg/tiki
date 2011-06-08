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
    $result = "$days ".tra('days');
  } elseif ($string > 60*60) {
    $hours = floor($string/(60*60));
    $result = "$hours ".tra('hours');
  } elseif ($string > 60) {
    $mins = floor($string/(60));
    $result = "$mins ".tra('minutes');
  } else {
	  $result = "$string ".tra('seconds');
  }
  return $result;
}
