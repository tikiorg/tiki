<?php

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
  } elseif($string>10000) {
    $string=number_format($string/100000,2).' Kb';
  } else {
    $string=$string.' b';
  }
  return $string;	
}

?>
