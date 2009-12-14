<?php
// (c) Copyright 2002-2009 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: /cvsroot/tikiwiki/tiki/tiki-admin_include_general.php,v 1.59.2.7 2008-03-08 21:12:22 sylvieg Exp $
//this script may only be included - so its better to die if called directly.
//smarty is not there - we need setup
require_once ('tiki-setup.php');
$access->check_script($_SERVER["SCRIPT_NAME"], basename(__FILE__));
if (isset($_REQUEST["new_prefs"])) {
	$listgroups = $userlib->get_groups(0, -1, 'groupName_asc', '', '', 'n');
	$in = array();
	$out = array();
	foreach($listgroups['data'] as $gr) {
		if ($gr['groupName'] == 'Anonymous') continue;
		if ($gr['registrationChoice'] == 'y' && isset($_REQUEST['registration_choices']) && !in_array($gr['groupName'], $_REQUEST['registration_choices'])) // deselect
		$out[] = $gr['groupName'];
		elseif ($gr['registrationChoice'] != 'y' && isset($_REQUEST['registration_choices']) && in_array($gr['groupName'], $_REQUEST['registration_choices'])) //select
		$in[] = $gr['groupName'];
	}
	check_ticket('admin-inc-general');
	$pref_toggles = array(
		"anonCanEdit",
		"useUrlIndex",
		"permission_denied_login_box",
		"feature_wiki_1like_redirection",
	);
	foreach($pref_toggles as $toggle) {
		simple_set_toggle($toggle);
	}
	$pref_simple_values = array(
		"urlIndex",
		"ip_can_be_checked",
		"permission_denied_url",
		"highlight_group",
		'zend_mail_handler',
		'zend_mail_smtp_server',
		'zend_mail_smtp_auth',
		'zend_mail_smtp_user',
		'zend_mail_smtp_pass',
		'zend_mail_smtp_port',
		'zend_mail_smtp_security',
		'urlOnUsername',
	);
	foreach($pref_simple_values as $svitem) {
		simple_set_value($svitem);
	}
	$pref_byref_values = array(
		"display_field_order",
		"display_timezone",
		"server_timezone",
		"long_date_format",
		"long_time_format",
		"short_date_format",
		"short_time_format",
		"tikiIndex",
		"users_prefs_display_timezone"
	);
	foreach($pref_byref_values as $britem) {
		byref_set_value($britem);
	}
	$tikilib->set_preference("display_timezone", $tikilib->get_preference("server_timezone"));
	// Special handling for tied fields: tikiIndex, urlIndex and useUrlIndex
	if (!empty($_REQUEST["urlIndex"]) && isset($_REQUEST["useUrlIndex"]) && $_REQUEST["useUrlIndex"] == 'on') {
		$_REQUEST["tikiIndex"] = $_REQUEST["urlIndex"];
		$tikilib->set_preference("tikiIndex", $_REQUEST["tikiIndex"]);
	}
	$smarty->assign('pagetop_msg', "");
}
// Handle Password Change Request
if (isset($_REQUEST["newadminpass"])) {
	check_ticket('admin-inc-general');
	if ($_REQUEST["adminpass"] <> $_REQUEST["again"]) {
		$msg = tra("The passwords do not match");
		$access->display_error(basename(__FILE__) , $msg);
	}
	// Dont allow blank passwords here
	if (empty($_REQUEST["adminpass"])) {
		$smarty->assign("msg", tra("You cannot have a blank password"));
		$smarty->display("error.tpl");
		die;
	}
	// Validate password here
	if (strlen($_REQUEST["adminpass"]) < $prefs['min_pass_length']) {
		$text = tra("Password should be at least");
		$text.= " " . $prefs['min_pass_length'] . " ";
		$text.= tra("characters long");
		$access->display_error(basename(__FILE__) , $text);
	}
	$userlib->change_user_password('admin', $_REQUEST['adminpass']);
	$smarty->assign('pagetop_msg', tra('Your admin password has been changed'));
}
// Get list of time zones
$smarty->assign("timezones", TikiDate::getTimeZoneList());
// Get information for alternate homes
$smarty->assign("home_forum_url", "tiki-view_forum.php?forumId=" . $prefs['home_forum']);
$smarty->assign("home_blog_url", "tiki-view_blog.php?blogId=" . $prefs['home_blog']);
$smarty->assign("home_gallery_url", "tiki-browse_gallery.php?galleryId=" . $prefs['home_gallery']);
$smarty->assign("home_file_gallery_url", "tiki-list_file_gallery.php?galleryId=" . $prefs['home_file_gallery']);
if ($prefs['home_blog']) {
	$hbloginfo = $tikilib->get_blog($prefs['home_blog']);
	$smarty->assign("home_blog_name", substr($hbloginfo["title"], 0, 20));
} else {
	$smarty->assign("home_blog_name", '');
}
if ($prefs['home_gallery']) {
	$hgalinfo = $tikilib->get_gallery($prefs['home_gallery']);
	$smarty->assign("home_gal_name", substr($hgalinfo["name"], 0, 20));
} else {
	$smarty->assign("home_gal_name", '');
}
if ($prefs['home_forum']) {
	require_once ('lib/commentslib.php');
	if (!isset($commentslib)) {
		$commentslib = new Comments($dbTiki);
	}
	$hforuminfo = $commentslib->get_forum($prefs['home_forum']);
	$smarty->assign("home_forum_name", substr($hforuminfo["name"], 0, 20));
} else {
	$smarty->assign("home_forum_name", '');
}
if ($prefs['home_file_gallery']) {
	$hgalinfo = $tikilib->get_file_gallery($prefs['home_file_gallery']);
	$smarty->assign("home_fil_name", substr($hgalinfo["name"], 0, 20));
} else {
	$smarty->assign("home_fil_name", '');
}
$listgroups = $userlib->get_groups(0, -1, 'groupName_desc', '', '', 'n');
$smarty->assign("listgroups", $listgroups['data']);
ask_ticket('admin-inc-general');
