<?php

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
  
  $string=substr($string,strlen($string)-3);
  if(file_exists("img/icn/$string".".gif")) {
    return "<img border='0' src='img/icn/${string}.gif' alt='icon' />";
  } else {
    return "<img border='0' src='img/icn/else.gif' alt='icon' />";
  }     
	
}

?>
