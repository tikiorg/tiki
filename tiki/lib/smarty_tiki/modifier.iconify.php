<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/**
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     iconify
 * Purpose:  Returns a filetype icon if the filetype is known and there's an icon in pics/icons/mime. Returns a default file type icon in any other case
 * -------------------------------------------------------------
 */
function smarty_modifier_iconify($string)
{
  
  $filetype=strtolower(substr($string,strrpos($string, '.')+1));
 
  if(file_exists("pics/icons/mime/$filetype".".png")) {
    return "<img border='0' src='pics/icons/mime/{$filetype}.png' alt='{$filetype}' width='16' height='16' />";
  } else {
    return "<img border='0' src='pics/icons/mime/default.png' alt='{$filetype}' width='16' height='16' />";
  }     
	
}

?>
