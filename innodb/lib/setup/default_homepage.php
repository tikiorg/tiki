<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

$groupHome = $userlib->get_user_default_homepage($user);
if ( $groupHome != '' ) {
	if ( ! preg_match('/^(\/|https?:)/', $groupHome) ) {
		$prefs['wikiHomePage'] = $groupHome;
		global $wikilib; include_once('lib/wiki/wikilib.php');
		$prefs['tikiIndex'] = $wikilib->sefurl($prefs['wikiHomePage']);
		$smarty->assign('wikiHomePage', $prefs['wikiHomePage']);
	} else $prefs['tikiIndex'] = $groupHome;
}
