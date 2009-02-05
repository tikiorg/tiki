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
 * Name:     htmldecode
 * Purpose:  Convert all HTML entities to their applicable characters
 * -------------------------------------------------------------
 */
function smarty_modifier_htmldecode( $s ) { return TikiLib::htmldecode( $s ); }
?>
