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
 * 
 * -------------------------------------------------------------
 */
function smarty_modifier_div($string,$num,$max=10)
{
	if($num==0) return 0;
	if(ceil(strlen($string)/$num)>$max) return $max;
    return ceil(strlen($string)/$num);
}



?>
