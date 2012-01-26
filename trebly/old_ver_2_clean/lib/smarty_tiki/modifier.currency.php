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
 * Name:     currency
 * Purpose:  formats a number as currency using number_format
 * -------------------------------------------------------------
 */
function smarty_modifier_currency($string, $dec_point=',', $thousands='.')
{
  return number_format($string, 2, $dec_point, $thousands);
}

?>
