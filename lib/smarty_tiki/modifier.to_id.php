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
 * Purpose:  Convert any string to a string that can be used as an HTML id attribute
 * @param string The string from which an id should be built
 * @return string A string to use as an id
 */
function smarty_modifier_escape($string)
{
	return TikiLib::lib('tiki')->clean_id_string( $string );
}
