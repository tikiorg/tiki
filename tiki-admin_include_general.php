<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_include_general.php,v 1.5 2003-08-12 10:59:44 redflo Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
include_once ("lib/imagegals/imagegallib.php");

include_once ("lib/commentslib.php");
$commentslib = new Comments($dbTiki);

// Handle Update
if (isset($_REQUEST["prefs"])) {
	$pref_toggles = array(
		"anonCanEdit",
		"cacheimages",
		"cachepages",
		"count_admin_pvs",
		"direct_pagination",
		"feature_menusfolderstyle",
		"feature_obzip",
		"lang_use_db",
		"modallgroups",
		"popupLinks",
		"record_untranslated",
		"useGroupHome",
		"useUrlIndex",
		"use_proxy"
	);

	foreach ($pref_toggles as $toggle) {
		simple_set_toggle ($toggle);
	}

	$pref_simple_values = array(
		"contact_user",
		"feature_server_name",
		"maxRecords",
		"sender_email",
		"system_os",
		"urlIndex",
		"proxy_host",
		"proxy_port"
	);

	foreach ($pref_simple_values as $svitem) {
		simple_set_value ($svitem);
	}

	$pref_byref_values = array(
		"display_timezone",
		"language",
		"long_date_format",
		"long_time_format",
		"short_date_format",
		"short_time_format",
		"siteTitle",
		"slide_style",
		"tikiIndex"
	);

	foreach ($pref_byref_values as $britem) {
		byref_set_value ($britem);
	}

	// Set value(s) with alternate pref name
	byref_set_value("site_style", "style");

	// Special handling for tied fields: tikiIndex, urlIndex and useUrlIndex
	if (!empty($_REQUEST["urlIndex"]) && isset($_REQUEST["useUrlIndex"]) && $_REQUEST["useUrlIndex"] == 'on') {
		$_REQUEST["tikiIndex"] = $_REQUEST["urlIndex"];

		$tikilib->set_preference("tikiIndex", $_REQUEST["tikiIndex"]);
		$smarty->assign_by_ref("tikiIndex", $_REQUEST["tikiIndex"]);
	}

	// Special handling for tmpDir, which has a default value
	if (isset($_REQUEST["tmpDir"])) {
		$tikilib->set_preference("tmpDir", $_REQUEST["tmpDir"]);

		$smarty->assign_by_ref("tmpDir", $_REQUEST["tmpDir"]);
	} else {
		$tdir = TikiSetup::tempdir();

		$tikilib->set_preference("tmpDir", $tdir);
		$smarty->assign("tmpDir", $tdir);
	}
}

	// Handle Password Change Request
	else if (isset($_REQUEST["newadminpass"])) {
	if ($_REQUEST["adminpass"] <> $_REQUEST["again"]) {
		$smarty->assign("msg", tra("The passwords dont match"));

		$smarty->display("styles/$style_base/error.tpl");
		die;
	}

	// Validate password here
	if (strlen($_REQUEST["adminpass"]) < $min_pass_length) {
		$text = tra("Password should be at least");

		$text .= " " . $min_pass_length . " ";
		$text .= tra("characters long");
		$smarty->assign("msg", $text);
		$smarty->display("styles/$style_base/error.tpl");
		die;
	}

	$userlib->change_user_password("admin", $_REQUEST["adminpass"]);
}

// Get list of available styles
$styles = array();
$h = opendir("styles/");

while ($file = readdir($h)) {
	if (strstr($file, "css")) {
		$styles[] = $file;
	}
}

closedir ($h);
$smarty->assign_by_ref("styles", $styles);

// Get list of available slideshow styles
$slide_styles = array();
$h = opendir("styles/slideshows");

while ($file = readdir($h)) {
	if (strstr($file, "css")) {
		$slide_styles[] = $file;
	}
}

closedir ($h);
$smarty->assign_by_ref("slide_styles", $slide_styles);

// Get list of available languages
$languages = array();
$languages = $tikilib->list_languages();
$smarty->assign_by_ref("languages", $languages);

// Get list of time zones
$timezone_options = $tikilib->get_timezone_list(false);
$smarty->assign_by_ref("timezone_options", $timezone_options);

// Get TimeZone information
$server_time = new Date();
$display_timezone = $tikilib->get_preference("display_timezone", $server_time->tz->getID());
$smarty->assign_by_ref("display_timezone", $display_timezone);

$timezone_server = $timezone_options[$server_time->tz->getID()];
$smarty->assign_by_ref("timezone_server", $timezone_server);

// Set defaults
$smarty->assign("language", $tikilib->get_preference("language", "en"));
$smarty->assign("lang_use_db", $tikilib->get_preference("lang_use_db", 'n'));
$smarty->assign("useUrlIndex", $tikilib->get_preference("useUrlIndex", 'n'));
$smarty->assign("urlIndex", $tikilib->get_preference("urlIndex", ''));
$smarty->assign("maxRecords", $tikilib->get_preference("maxRecords", 10));
$smarty->assign("title", $tikilib->get_preference("title", ""));
$smarty->assign("popupLinks", $tikilib->get_preference("popupLinks", 'n'));
$smarty->assign("style_site", $tikilib->get_preference("style", "default.css"));

// Get information for alternate homes
$smarty->assign("home_forum_url", "tiki-view_forum.php?forumId=" . $home_forum);
$smarty->assign("home_blog_url", "tiki-view_blog.php?blogId=" . $home_blog);
$smarty->assign("home_gallery_url", "tiki-browse_gallery.php?galleryId=" . $home_gallery);
$smarty->assign("home_file_gallery_url", "tiki-list_file_gallery.php?galleryId=" . $home_file_gallery);

if ($home_blog) {
	$hbloginfo = $tikilib->get_blog($home_blog);

	$smarty->assign("home_blog_name", substr($hbloginfo["title"], 0, 20));
} else {
	$smarty->assign("home_blog_name", '');
}

if ($home_gallery) {
	$hgalinfo = $imagegallib->get_gallery($home_gallery);

	$smarty->assign("home_gal_name", substr($hgalinfo["name"], 0, 20));
} else {
	$smarty->assign("home_gal_name", '');
}

if ($home_forum) {
	$hforuminfo = $commentslib->get_forum($home_forum);

	$smarty->assign("home_forum_name", substr($hforuminfo["name"], 0, 20));
} else {
	$smarty->assign("home_forum_name", '');
}

if ($home_file_gallery) {
	$hgalinfo = $imagegallib->get_gallery($home_file_gallery);

	$smarty->assign("home_fil_name", substr($hgalinfo["name"], 0, 20));
} else {
	$smarty->assign("home_fil_name", '');
}

// Get Date/Time preferences
$long_date_format = $tikilib->get_preference("long_date_format", "%A %d " . tra("of"). " %B, %Y");
$smarty->assign_by_ref("long_date_format", $long_date_format);

$short_date_format = $tikilib->get_preference("short_date_format", "%a %d " . tra("of"). " %b, %Y");
$smarty->assign_by_ref("short_date_format", $short_date_format);

$long_time_format = $tikilib->get_preference("long_time_format", "%H:%M:%S %Z");
$smarty->assign_by_ref("long_time_format", $long_time_format);

$short_time_format = $tikilib->get_preference("short_time_format", "%H:%M %Z");
$smarty->assign_by_ref("short_time_format", $short_time_format);

?>
