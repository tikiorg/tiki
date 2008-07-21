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
 * Name:     lcfirst
 * Purpose:  lowercase the initial character in a string
 * -------------------------------------------------------------
 */
function smarty_modifier_lcfirst( $s ) { return strtolower( $s{0} ). substr( $s, 1 ); }
?>
