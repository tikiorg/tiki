<?php

// $Header: /cvsroot/tikiwiki/tiki/lib/setup/patches.php,v 1.1 2007-10-06 15:18:45 nyloth Exp $
// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for
// details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'],'tiki-setup.php')!=FALSE) {
  header('location: index.php');
  exit;
}

// patch for Case-sensitivity perm issue
if ( $case_patched == 'n' ) {
	include_once 'db/case_patch.php';
	$tikilib->set_preference('case_patched','y');
}

// UPGRADE temporary for wysiwyg prefs. TODO REMOVE from release
if ($feature_wysiwyg == 'no' or $feature_wysiwyg == 'optional' or $feature_wysiwyg == 'default') {
	$par = $tikilib->get_preference('wiki_wikisyntax_in_html','');
	$def = $tikilib->get_preference('wysiwyg_default','y');
	if ($feature_wysiwyg == 'optional') {
		$tikilib->set_preference('feature_wysiwyg','y');
		$tikilib->set_preference('wysiwyg_optional','y');
		if ($def == 'y') {
			$tikilib->set_preference('wysiwyg_default','y');
		}
	} elseif ($feature_wysiwyg == 'default') {
		$tikilib->set_preference('feature_wysiwyg','y');
		$tikilib->set_preference('wysiwyg_optional','n');
		$tikilib->set_preference('wysiwyg_default','y');
	} else {
		$tikilib->set_preference('feature_wysiwyg','n');
	}
	if ($par == 'full') {
		$tikilib->set_preference('wysiwyg_wiki_parsed','y');
		$tikilib->set_preference('wysiwyg_wiki_semi_parsed','n');
	} elseif ($par == 'partial') {
		$tikilib->set_preference('wysiwyg_wiki_parsed','y');
		$tikilib->set_preference('wysiwyg_wiki_semi_parsed','y');
	} elseif ($par == 'none') {
		$tikilib->set_preference('wysiwyg_wiki_parsed','n');
		$tikilib->set_preference('wysiwyg_wiki_semi_parsed','n');
	}
}

// OBSOLETE
/*
$smarty->assign('http_domain', $http_domain = $url_host);
$smarty->assign('http_prefix', $http_prefix = $url_path);
$smarty->assign('http_login_url', $http_login_url = $login_url);
$smarty->assign('https_login_url', $https_login_url = $login_url);
if ( isset($https_login_required) && $https_login_required == 'y' ) {
	$tikilib->set_preference('https_login_required','');
	$smarty->assign('https_login', $https_login = 'required');
} elseif ( $https_login == 'y' ) $smarty->assign('https_login', $https_login = 'allowed');
elseif ( $https_login == 'n' ) $smarty->assign('https_login', $https_login = 'disabled');
*/
