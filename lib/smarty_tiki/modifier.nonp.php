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

/**
 * Smarty modifier plugin to remove ~np~ tags from smarty variable. For use in templates used by the {list} wiki plugin
 *
 * - type:     modifier
 * - name:     nonp (short for "no nonparsed")
 * - purpose:  to return a usable string
 *
 * @param string to be replaced (optional)
 * @return corrected string
 *
 * Example: {if $row.title|nonp eq ''}
 */

function smarty_modifier_nonp($string)
{
	return preg_replace('/~[\/]?np~/', '', $string);
}
