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
 * Name:     kbsize
 * Purpose:  returns size in Mb, Kb or bytes.
 * -------------------------------------------------------------
 */
function smarty_modifier_kbsize($string)
{
  if($string>1000000) {
    $string=number_format($string/1000000,2).' Mb';
  } elseif($string>1000) {
    $string=number_format($string/1000,2).' Kb';
  } else {
    $string=$string.' b';
  }
  return $string;	
}

?>
