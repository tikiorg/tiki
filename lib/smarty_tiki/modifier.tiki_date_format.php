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
 * Name:     tiki_date_format
 * Purpose:  format datestamps via Pear::Date using TikiLib static call, (timezone adjusted to user specified timezone)
 * Input:    string: input date string
 *           format: strftime format for output
 *           _user: if specified, use this user timezone instead of the current user
 * -------------------------------------------------------------
 */
function smarty_modifier_tiki_date_format($string, $format, $_user = false)
{
	return TikiLib::date_format(tra($format), $string, $_user);
}
