<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$bypass_siteclose_check = 'y';
require_once ('tiki-setup.php');

if (!empty($_REQUEST['page'])) {
	$out_page =  $_REQUEST['page'];
	if ($tikilib->page_exists($out_page)) {
		global $wikilib; include_once( 'lib/wiki/wikilib.php');
		$out_page = $wikilib->sefurl($out_page);
	} else {
		$out_page = '';
	}
} else if ($prefs['useGroupHome'] === 'y') {
	$out_page = $userlib->get_group_home('Anonymous');
} elseif ($prefs["feature_sefurl"] == 'y' && $prefs['site_tikiIndex'] == 'tiki-index.php' && $prefs['wikiHomePage']) {
	global $wikilib; include_once( 'lib/wiki/wikilib.php');
	$out_page = $wikilib->sefurl($prefs['wikiHomePage']);
} else {
	$out_page = '';
}

$userlib->user_logout($user, false, $out_page);
