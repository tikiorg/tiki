<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     spacify
 * Purpose:  add spaces between characters in a string
 * -------------------------------------------------------------
 */
function smarty_modifier_quoted($string)
{
    $string = str_replace("\n","\n>",$string);
    return '>'.$string;
}

/* vim: set expandtab: */

?>
