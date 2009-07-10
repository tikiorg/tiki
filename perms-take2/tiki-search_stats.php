<?php
// (c) Copyright 2002-2009 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: /cvsroot/tikiwiki/tiki/tiki-search_stats.php,v 1.14 2007-10-12 07:55:32 nyloth Exp $
require_once ('tiki-setup.php');
include_once ('lib/search/searchstatslib.php');
if ($prefs['feature_search_stats'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled") . ": feature_search_stats");
	$smarty->display("error.tpl");
	die;
}
if ($tiki_p_admin != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("You do not have permission to use this feature"));
	$smarty->display("error.tpl");
	die;
}
if (isset($_REQUEST["clear"])) {
	check_ticket('search-stats');
	$searchstatslib->clear_search_stats();
}
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
$smarty->assign_by_ref('offset', $offset);
if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}
$smarty->assign('find', $find);
$smarty->assign_by_ref('sort_mode', $sort_mode);
$channels = $searchstatslib->list_search_stats($offset, $maxRecords, $sort_mode, $find);
$smarty->assign_by_ref('cant_pages', $channels["cant"]);
$smarty->assign_by_ref('channels', $channels["data"]);
ask_ticket('search-stats');
// Display the template
$smarty->assign('mid', 'tiki-search_stats.tpl');
$smarty->display("tiki.tpl");
