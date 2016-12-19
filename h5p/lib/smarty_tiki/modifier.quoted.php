<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
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
	    $string = str_replace("\n", "\n>", $string);
	    $string = "\n>".$string;
	} elseif ($format == 'fancy') {
		$string = "{QUOTE(replyto=>$replyto)}" . $string . '{QUOTE}';
	}
	return $string;
}
