<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
$access->check_script($_SERVER["SCRIPT_NAME"], basename(__FILE__));
if (!empty($_REQUEST['w_use_dir'])) {
	if (substr($_REQUEST['w_use_dir'], -1) != "\\" && substr($_REQUEST['w_use_dir'], -1) != "/") {
		$_REQUEST['w_use_dir'] .= '/';
	}
	simple_set_value('w_use_dir');
}
if (isset($_REQUEST["dump"])) {
	check_ticket('admin-inc-wiki');
	include ("lib/tar.class.php");
	error_reporting(E_ERROR | E_WARNING);
	$adminlib->dump();
}
// Included for the forum dropdown
include_once ("lib/comments/commentslib.php");
if (isset($_REQUEST["createtag"])) {
	check_ticket('admin-inc-wiki');
	// Check existance
	if ($adminlib->tag_exists($_REQUEST["tagname"])) {
		$msg = tra("Tag already exists");
		$access->display_error(basename(__FILE__) , $msg);
	}
	$adminlib->create_tag($_REQUEST["tagname"]);
}
if (isset($_REQUEST["restoretag"])) {
	check_ticket('admin-inc-wiki');
	// Check existance
	if (!$adminlib->tag_exists($_REQUEST["tagname"])) {
		$msg = tra("Tag not found");
		$access->display_error(basename(__FILE__) , $msg);
	}
	$adminlib->restore_tag($_REQUEST["tagname"]);
}
if (isset($_REQUEST["removetag"])) {
	check_ticket('admin-inc-wiki');
	// Check existance
	$adminlib->remove_tag($_REQUEST["tagname"]);
}
if (isset($_REQUEST["rmvunusedpic"])) {
	check_ticket('admin-inc-wiki');
	$adminlib->remove_unused_pictures();
}
if (isset($_REQUEST["wikidiscussprefs"])) {
	check_ticket('admin-inc-wiki');
	simple_set_toggle('feature_wiki_discuss');
	simple_set_value('wiki_forum_id');
}
if (isset($_REQUEST["wikifeatures"])) {
	check_ticket('admin-inc-wiki');
	if ((isset($_REQUEST['feature_backlinks']) && $_REQUEST['feature_backlinks'] == 'on' && $prefs['feature_backlinks'] != 'y') || (empty($_REQUEST['feature_backlinks']) && $prefs['feature_backlinks'] == 'y')) {
		$backlinksChange = true;
	}
	if (isset($backlinksChange) && $backlinksChange) {
		global $wikilib;
		include_once ('lib/wiki/wikilib.php');
		$wikilib->refresh_backlinks();
	}
}
if (isset($_REQUEST["wikiset3d"])) {
	check_ticket('admin-inc-wiki');
	if (isset($_REQUEST["wiki_3d_autoload"]) && $_REQUEST["wiki_3d_autoload"] == "on") {
		$tikilib->set_preference("wiki_3d_autoload", 'true');
	} else {
		$tikilib->set_preference("wiki_3d_autoload", 'false');
	}
	if (isset($_REQUEST["wiki_3d_adjust_camera"]) && $_REQUEST["wiki_3d_adjust_camera"] == "on") {
		$tikilib->set_preference("wiki_3d_adjust_camera", 'true');
	} else {
		$tikilib->set_preference("wiki_3d_adjust_camera", 'false');
	}
}
$tags = $adminlib->get_tags();
$smarty->assign_by_ref("tags", $tags);
ask_ticket('admin-inc-wiki');
