<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

if (basename($_SERVER['SCRIPT_NAME']) === basename(__FILE__)) {
	die('This script may only be included.');
}

$groupHome = $userlib->get_user_default_homepage($user);
if ($groupHome != '') {
	if (! preg_match('/^(\/|https?:)/', $groupHome)) {
		$prefs['wikiHomePage'] = $groupHome;
		$wikilib = TikiLib::lib('wiki');
		$prefs['tikiIndex'] = $wikilib->sefurl($prefs['wikiHomePage']);
		$smarty->assign('wikiHomePage', $prefs['wikiHomePage']);
	} else {
		$prefs['tikiIndex'] = $groupHome;
	}
}
