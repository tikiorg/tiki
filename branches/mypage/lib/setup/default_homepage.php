<?php

// $Id$
// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for
// details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'],'tiki-setup.php')!=FALSE) {
  header('location: index.php');
  exit;
}

$groupHome = $userlib->get_user_default_homepage($user);
if ( $groupHome != '' ) {
	if ( ! preg_match('/^(\/|https?:)/', $groupHome) ) {
		$prefs['wikiHomePage'] = $groupHome;
		$prefs['tikiIndex'] = 'tiki-index.php?page='.$prefs['wikiHomePage'];
		$smarty->assign('wikiHomePage', $prefs['wikiHomePage']);
	} else $prefs['tikiIndex'] = $groupHome;
}
