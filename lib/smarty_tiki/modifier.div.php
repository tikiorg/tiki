<?php

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

/* vim: set expandtab: */

?>
