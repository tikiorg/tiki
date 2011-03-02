<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     adjust
 * Purpose:  Adjust a string to a certain length if necessary,
 *           optionally splitting in the middle of a word, and 
 *           appending the $etc string or padding the string
 *			 using $pad as filler.
 * -------------------------------------------------------------
 */
function smarty_modifier_simplewiki($string) {
	global $tikilib;

	$string = htmlentities( $string, ENT_QUOTES, 'UTF-8' );
	return $tikilib->parse_data_simple( $string );
}
