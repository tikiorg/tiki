<?php
// (c) Copyright 2002-2009 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: /cvsroot/tikiwiki/tiki/tiki-admin_include_ads.php,v 1.1.2.1 2008-03-16 16:57:50 luciash Exp $
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}
if (isset($_REQUEST["adssetup"])) {
	ask_ticket('admin-inc-ads');
	$pref_toggles = array(
		"feature_sitead",
		"sitead_publish"
	);
	foreach($pref_toggles as $toggle) {
		simple_set_toggle($toggle);
	}
	$pref_simple_values = array(
		"sitead"
	);
	foreach($pref_simple_values as $svitem) {
		simple_set_value($svitem);
	}
}
