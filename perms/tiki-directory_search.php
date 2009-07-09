<?php
// (c) Copyright 2002-2009 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: /cvsroot/tikiwiki/tiki/tiki-directory_search.php,v 1.11 2007-10-12 07:55:26 nyloth Exp $
$section = 'directory';
require_once ('tiki-setup.php');
include_once ('lib/directory/dirlib.php');
if ($prefs['feature_directory'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled") . ": feature_directory");
	$smarty->display("error.tpl");
	die;
}
if ($tiki_p_view_directory != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("Permission denied"));
	$smarty->display("error.tpl");
	die;
}
$smarty->assign('words', $_REQUEST['words']);
$smarty->assign('where', $_REQUEST['where']);
$smarty->assign('how', $_REQUEST['how']);
if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'hits_desc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}
if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}
if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}
$smarty->assign_by_ref('offset', $offset);
$smarty->assign_by_ref('sort_mode', $sort_mode);
$smarty->assign('find', $find);
if ($_REQUEST['where'] == 'all') {
	$items = $dirlib->dir_search($_REQUEST['words'], $_REQUEST['how'], $offset, $maxRecords, $sort_mode);
} else {
	$items = $dirlib->dir_search_cat($_REQUEST['parent'], $_REQUEST['words'], $_REQUEST['how'], $offset, $maxRecords, $sort_mode);
}
$smarty->assign_by_ref('cant_pages', $items["cant"]);
$smarty->assign_by_ref('items', $items["data"]);
include_once ('tiki-section_options.php');
// Display the template
$smarty->assign('mid', 'tiki-directory_search.tpl');
$smarty->display("tiki.tpl");
