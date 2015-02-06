<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
$auto_query_args = array('sort_mode', 'offset', 'find');
//get_strings tra('Dynamic content')
$access->check_feature('feature_dynamic_content');
$access->check_permission('tiki_p_admin_dynamic');

$dcslib = TikiLib::lib('dcs');

if (isset($_REQUEST["remove"])) {
	$access->check_authenticity();
	$dcslib->remove_contents($_REQUEST["remove"]);
}
$smarty->assign('description', '');
$smarty->assign('contentLabel', '');
$smarty->assign('contentId', 0);
if (isset($_REQUEST["save"])) {
	check_ticket('list-contents');
	$smarty->assign('description', $_REQUEST["description"]);
	$smarty->assign('contentLabel', $_REQUEST["contentLabel"]);
	$id = $dcslib->replace_content($_REQUEST["contentId"], $_REQUEST["description"], $_REQUEST["contentLabel"]);
	$smarty->assign('contentId', $id);
}
if (isset($_REQUEST["edit"])) {
	$info = $dcslib->get_content($_REQUEST["edit"]);
	$smarty->assign('contentId', $info["contentId"]);
	$smarty->assign('description', $info["description"]);
	$smarty->assign('contentLabel', $info["contentLabel"]);
}
if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'contentId_desc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}
$smarty->assign_by_ref('sort_mode', $sort_mode);
// If offset is set use it if not then use offset =0
// use the maxRecords php variable to set the limit
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
$listpages = $dcslib->list_content($offset, $maxRecords, $sort_mode, $find);
$smarty->assign_by_ref('cant', $listpages['cant']);
$smarty->assign_by_ref('listpages', $listpages["data"]);
ask_ticket('list-contents');
// Display the template
$smarty->assign('mid', 'tiki-list_contents.tpl');
$smarty->display("tiki.tpl");
