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

$addonprefs = TikiLib::lib('prefs')->getAddonPrefs();
asort($addonprefs);
$smarty->assign('addonprefs', $addonprefs);

$smarty->assign('php_major_version', substr(PHP_VERSION, 0, strpos(PHP_VERSION, '.')));
ask_ticket('admin-inc-features');
