<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
include_once ('lib/blogs/bloglib.php');
$access->check_feature('feature_blogs');
$access->check_permission('tiki_p_blog_admin');
/*
if($prefs['feature_listPages'] != 'y') {
$smarty->assign('msg',tra("This feature is disabled"));
$smarty->display("error.tpl");
die;
}
*/
/*
// Now check permissions to access this page
if($tiki_p_view != 'y') {
$smarty->assign('errortype', 401);
$smarty->assign('msg',tra("Permission denied. You cannot view pages"));
$smarty->display("error.tpl");
die;
}
*/
if (isset($_REQUEST["remove"])) {
	$access->check_authenticity();
	$bloglib->remove_post($_REQUEST["remove"]);
}
// This script can receive the thresold
// for the information as the number of
// days to get in the log 1,3,4,etc
// it will default to 1 recovering information for today
if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'created_desc';
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
$listpages = $tikilib->list_posts($offset, $maxRecords, $sort_mode, $find);
$smarty->assign_by_ref('cant', $listpages["cant"]);
$smarty->assign_by_ref('listpages', $listpages["data"]);
//print_r($listpages["data"]);
ask_ticket('list-posts');
// Display the template
$smarty->assign('mid', 'tiki-list_posts.tpl');
$smarty->display("tiki.tpl");
