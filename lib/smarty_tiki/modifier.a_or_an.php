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
 * Prepends an "a " or "an " depending on whether word starts with vowel.
 * @param caps, if set will cause "A " or "An "
 * -------------------------------------------------------------
 */
function smarty_modifier_a_or_an($string, $caps = false)
{
	global $prefs;
	if (substr($prefs['language'], 0, 2) != 'en') {
		return $string;
	}
	$vowels = array('a', 'e', 'i', 'o', 'u');
	$initial = strtolower(substr($string, 0, 1));
	if (in_array($initial, $vowels)) {
		$prefix = $caps ? 'An ' : 'an ';
	} else {
		$prefix = $caps ? 'A ' : 'a ';	
	}
	return $prefix . $string;
}
