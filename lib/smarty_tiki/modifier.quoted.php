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
 * Name:     quoted
 * Purpose:  quote text by adding ">" or using {QUOTE()} plugin
 * -------------------------------------------------------------
 */
function smarty_modifier_quoted($string, $format='simple', $replyto='')
{
	if ($format == 'simple') {
	    $string = str_replace("\n","\n>",$string);
	    $string = "\n>".$string;
	} elseif ($format == 'fancy') {
		$string = "{QUOTE(replyto=>$replyto)}" . $string . '{QUOTE}';
	}
	return $string;
}



?>
