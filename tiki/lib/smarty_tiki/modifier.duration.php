<?php

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
  $result=Array();
  if($string > 60*60*24) {
    $days = floor($string/(60*60*24));
    if ($days > 1) {
	$s = tra('days');
    } else {
	$s = tra('day');
    }
    if ($long) {
	$s = " $s";
    } else {
	$s = substr($s, 0, 1);
    }
    $result[]="$days$s";
    $string = $string % (60*60*24);
  }
  if($string > 60*60) {
    $hours = floor($string/(60*60));
    if ($hours > 1) {
	$s = tra('hours');
    } else {
	$s = tra('hour');
    }
    if ($long) {
	$s = " $s";
    } else {
	$s = substr($s, 0, 1);
    }
    $result[]="$hours$s";
    $string = $string % (60*60);
  }
  if($string > 60 && ($long || (!$long && empty($days)))) {
    $mins = floor($string/(60));
    if ($mins > 1) {
	$s = tra('mins');
    } else {
	$s = tra('min');
    }
    if ($long) {
	$s = " $s";
    } else {
	$s = substr($s, 0, 1);
    }
    $result[]="$mins$s";
    $string = $string % (60);
  }
  if($string > 0 && ($long || (!$long && empty($days) && empty($hours)))) {
    if ($string > 1) {
	$s = tra('secs');
    } else {
	$s = tra('sec');
    }
    if ($long) {
	$s = " $s";
    } else {
	$s = substr($s, 0, 1);
    }
    $result[]="$string$s";
  }
  
  return implode(' ',$result);
}

?>
