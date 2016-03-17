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

function smarty_modifier_tiki_long_date($string)
{
	global $prefs;
	$smarty = TikiLib::lib('smarty');
	$smarty->loadPlugin('smarty_modifier_tiki_date_format');
	return smarty_modifier_tiki_date_format($string, $prefs['long_date_format']);
}
