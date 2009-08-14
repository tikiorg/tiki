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
function smarty_modifier_kbsize($string, $bytes = false, $nb_decimals = 2)
{
  if ( $string == '' ) return '';

  // 1024 x 1024 = 1048576
  if ( $string > 1048576 ) { 
   $string = number_format($string/1048576,$nb_decimals);
   $kb_string = 'M';
  } 
  elseif ( $string > 1024 ) { 
   $string = number_format($string/1024,$nb_decimals);
   $kb_string = 'K';
  } 
  else { 
   $string = $string;
   $kb_string = '';
  }; 
  
  $kb_string = $kb_string.(($bytes) ? 'B' : 'b');

  return $string.'&nbsp;'.tra($kb_string);
}
