<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     quoted
 * Purpose:  quote text by adding ">" or using {QUOTE()} plugin
 * -------------------------------------------------------------
 */
function smarty_modifier_quoted($string, $format='simple', $replyto='')
{
	if ($format == 'simple') {
	    $string = str_replace("\n","\n>",$string);
	    $string = '>'.$string;
	} elseif ($format == 'fancy') {
		$string = "{QUOTE(replyto=>$replyto)}" . $string . '{QUOTE}';
	}
	return $string;
}

/* vim: set expandtab: */

?>
