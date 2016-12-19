<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
include_once ('lib/refererstats/refererlib.php');
$access->check_feature('feature_referer_stats');
$access->check_permission('tiki_p_view_referer_stats');

if (isset($_REQUEST["clear"])) {
	$access->check_authenticity();
	$refererlib->clear_referer_stats();
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
$channels = $refererlib->list_referer_stats($offset, $maxRecords, $sort_mode, $find);
$smarty->assign_by_ref('cant_pages', $channels["cant"]);
$smarty->assign_by_ref('channels', $channels["data"]);
ask_ticket('referer-stats');
// Display the template
$smarty->assign('mid', 'tiki-referer_stats.tpl');
$smarty->display("tiki.tpl");
