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
 * Name:     max_user_inscriptions
 * Purpose:  to use with the tracker field type "User inscription"
 * -------------------------------------------------------------
 */
function smarty_modifier_max_user_inscriptions( $text ) { 
  return substr($text,0,strpos($text,'#'));
}
?>
