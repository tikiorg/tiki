<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
$access->check_script($_SERVER['SCRIPT_NAME'], basename(__FILE__));

if (!empty($_REQUEST['w_use_dir'])) {
	if (substr($_REQUEST['w_use_dir'], -1) != '\\' && substr($_REQUEST['w_use_dir'], -1) != '/') {
		$_REQUEST['w_use_dir'] .= '/';
	}
	simple_set_value('w_use_dir');
}

if (isset($_REQUEST['dump'])) {
	check_ticket('admin-inc-wiki');
	include ('lib/tar.class.php');
	error_reporting(E_ERROR | E_WARNING);
	$adminlib->dump();
}

if (!empty($_REQUEST['moveWikiUp'])) {
	check_ticket('admin-inc-wiki');
	$filegallib = TikiLib::lib('filegal');
	$errorsWikiUp = $feedbacksWikiUp = array();
	$info = $filegallib->get_file_gallery_info($prefs['home_file_gallery']);
	if (empty($info)) {
		$errorsWikiUp[] = tra('You must set a home file gallery');
	} else {
		$filegallib->moveAllWikiUpToFgal($prefs['home_file_gallery'], $errorsWikiUp, $feedbacksWikiUp);
		$smarty->assign_by_ref('feedbacksWikiUp', $feedbacksWikiUp);
		$smarty->assign_by_ref('moveWikiUp', $_REQUEST['moveWikiUp']);
	}
	$smarty->assign_by_ref('errorsWikiUp', $errorsWikiUp);
}

// Included for the forum dropdown
if (isset($_REQUEST['createtag'])) {
	check_ticket('admin-inc-wiki');
	// Check existance
	if ($adminlib->tag_exists($_REQUEST['tagname'])) {
		$msg = tra('Tag already exists');
		$access->display_error(basename(__FILE__), $msg);
	}
	$adminlib->create_tag($_REQUEST['tagname']);
}

if (isset($_REQUEST['restoretag'])) {
	check_ticket('admin-inc-wiki');
	// Check existance
	if (!$adminlib->tag_exists($_REQUEST['tagname'])) {
		$msg = tra('Tag not found');
		$access->display_error(basename(__FILE__), $msg);
	}
	$adminlib->restore_tag($_REQUEST['tagname']);
}

if (isset($_REQUEST['removetag'])) {
	check_ticket('admin-inc-wiki');
	// Check existance
	$adminlib->remove_tag($_REQUEST['tagname']);
}

if (isset($_REQUEST['rmvunusedpic'])) {
	check_ticket('admin-inc-wiki');
	$adminlib->remove_unused_pictures();
}

if (isset($_REQUEST['wikidiscussprefs'])) {
	check_ticket('admin-inc-wiki');
	simple_set_toggle('feature_wiki_discuss');
	simple_set_value('wiki_forum_id');
}

if (isset($_REQUEST['wikifeatures'])) {
	check_ticket('admin-inc-wiki');
	if ((isset($_REQUEST['feature_backlinks']) && $_REQUEST['feature_backlinks'] == 'on' && $prefs['feature_backlinks'] == 'y')
			|| (empty($_REQUEST['feature_backlinks']) && $prefs['feature_backlinks'] == 'y')
	) {
		$backlinksChange = true;
	}

	if (isset($backlinksChange) && $backlinksChange) {
		$wikilib = TikiLib::lib('wiki');
		$wikilib->refresh_backlinks();
	}
}

$tags = $adminlib->get_tags();
$smarty->assign_by_ref('tags', $tags);
ask_ticket('admin-inc-wiki');
