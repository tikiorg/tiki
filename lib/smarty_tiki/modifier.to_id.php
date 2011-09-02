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

/**
 * Smarty to_id modifier plugin
 *
 * Type:     modifier
 * Name:     to_id
 * Purpose:  Convert all whitespace from a string, turning any non-empty string to a string that can be used as an HTML id attribute.
 * ids still need to be non-empty and unique. This modifier should be avoided, use OIDs instead.
 * @param string The string from which an id should be built
 * @return string A string to use as an id, encoded for an HTML element attribute context
 */
function smarty_modifier_to_id($string)
{
	return TikiLib::clean_id_string( $string );
}
