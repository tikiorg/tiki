<?php
// (c) Copyright 2002-2009 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// This script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}
if (isset($_REQUEST["blogset"]) && isset($_REQUEST["homeBlog"])) {
	check_ticket('admin-inc-blogs');
	$tikilib->set_preference("home_blog", $_REQUEST["homeBlog"]);
}
if (isset($_REQUEST["blogfeatures"])) {
	check_ticket('admin-inc-blogs');
	simple_set_value('feature_blog_mandatory_category');
}
if (isset($_REQUEST['bloglistconf'])) {
	check_ticket('admin-inc-blogs');
}
if (isset($_REQUEST["blogcomprefs"])) {
	check_ticket('admin-inc-blogs');
}
if ($prefs['feature_categories'] == 'y') {
	include_once ('lib/categories/categlib.php');
	$catree = $categlib->get_all_categories();
	$smarty->assign('catree', $catree);
}
ask_ticket('admin-inc-blogs');
$blogs = $tikilib->list_blogs(0, -1, 'created_desc', '');
$smarty->assign_by_ref('blogs', $blogs["data"]);
