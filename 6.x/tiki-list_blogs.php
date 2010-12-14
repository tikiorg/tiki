<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'blogs';
require_once ('tiki-setup.php');
include_once ('lib/blogs/bloglib.php');
$smarty->assign('headtitle', tra('Blogs'));
if ($prefs['feature_categories'] == 'y') {
	include_once ('lib/categories/categlib.php');
}
$access->check_feature('feature_blogs');
$access->check_permission('tiki_p_read_blog');
if (isset($_REQUEST["remove"])) {
	// Check if it is the owner
	$data = $bloglib->get_blog($_REQUEST["remove"]);
	if ($data["user"] != $user) {
		if ($tiki_p_blog_admin != 'y') {
			$smarty->assign('errortype', 401);
			$smarty->assign('msg', tra("You do not have permission to remove this blog"));
			$smarty->display("error.tpl");
			die;
		}
	}
	$access->check_authenticity();
	$bloglib->remove_blog($_REQUEST["remove"]);
}
// This script can receive the thresold
// for the information as the number of
// days to get in the log 1,3,4,etc
// it will default to 1 recovering information for today
if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = $prefs['blog_list_order'];
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}
$smarty->assign_by_ref('sort_mode', $sort_mode);
// If offset is set use it if not then use offset =0
// use the maxRecords php variable to set the limit
// if sortMode is not set then use lastModif_desc
if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}
$smarty->assign_by_ref('offset', $offset);
if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}
$smarty->assign('find', $find);
// Get a list of last changes to the Wiki database
$listpages = $bloglib->list_blogs($offset, $maxRecords, $sort_mode, $find);
Perms::bulk( array( 'type' => 'blog' ), 'object', $listpages['data'], 'blogId' );
$temp_max = count($listpages["data"]);
for ($i = 0; $i < $temp_max; $i++) {
	$blogperms = Perms::get( array( 'type' => 'blog', 'object' => $listpages['data'][$i]['blogId'] ) );
	$listpages["data"][$i]["individual_tiki_p_read_blog"] = $blogperms->read_blog ? 'y' : 'n';
	$listpages["data"][$i]["individual_tiki_p_blog_post"] = $blogperms->blog_post ? 'y' : 'n';
	$listpages["data"][$i]["individual_tiki_p_create_blogs"] = $blogperms->create_blogs ? 'y' : 'n';
}
$smarty->assign_by_ref('listpages', $listpages["data"]);
$smarty->assign_by_ref('cant', $listpages["cant"]);
include_once ('tiki-section_options.php');
if ($prefs['feature_mobile'] == 'y' && isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'mobile') {
	include_once ("lib/hawhaw/hawtikilib.php");
	HAWTIKI_list_blogs($listpages, $tiki_p_read_blog);
}
ask_ticket('list-blogs');
// Display the template
$smarty->assign('mid', 'tiki-list_blogs.tpl');
$smarty->display("tiki.tpl");
