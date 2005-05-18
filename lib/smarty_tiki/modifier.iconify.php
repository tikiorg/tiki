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
 * Name:     capitalize
 * Purpose:  capitalize words in the string
 * -------------------------------------------------------------
 */
function smarty_modifier_iconify($string)
{
  
  $string=strtolower(substr($string,strlen($string)-3));
  if(file_exists("img/icn/$string".".gif")) {
    return "<img border='0' src='img/icn/${string}.gif' alt='icon' />";
  } else {
    return "<img border='0' src='img/icn/else.gif' alt='icon' />";
  }     
	
}

?>
