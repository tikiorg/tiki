<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
include_once ('lib/commcenter/commlib.php');
include_once ('lib/wiki/wikilib.php');
$auto_query_args = array('receivedPageId', 'sort_mode', 'offset', 'find', 'sort_modes');
$access->check_feature('feature_comm');
$access->check_permission('tiki_p_admin_received_pages');
if (!isset($_REQUEST["receivedPageId"])) {
	$_REQUEST["receivedPageId"] = 0;
}
$smarty->assign('receivedPageId', $_REQUEST["receivedPageId"]);
$errors = array();
if (isset($_REQUEST["accept"])) {
	check_ticket('received-pages');
	// CODE TO ACCEPT A PAGE HERE
	if (!$commlib->accept_page($_REQUEST["accept"])) {
		$info = $commlib->get_received_page($_REQUEST['accept']);
		$errors[] = array('error' => 'Page already exists');
	}
}
if ($_REQUEST["receivedPageId"]) {
	$info = $commlib->get_received_page($_REQUEST["receivedPageId"]);
} else {
	$info = array();
	$info["pageName"] = '';
	$info["data"] = '';
	$info["comment"] = '';
}
$smarty->assign('view', 'n');
if (isset($_REQUEST["view"])) {
	$info = $commlib->get_received_page($_REQUEST["view"]);
	$smarty->assign('view', 'y');
}
if (isset($_REQUEST["preview"])) {
	$info["pageName"] = $_REQUEST["pageName"];
	$info["data"] = $_REQUEST["data"];
	$info["comment"] = $_REQUEST["comment"];
}
$smarty->assign('pageName', $info["pageName"]);
$smarty->assign('data', $info["data"]);
$smarty->assign('comment', $info["comment"]);
// Assign parsed
$smarty->assign('parsed', $tikilib->parse_data($info["data"]));
if (isset($_REQUEST["remove"])) {
	$access->check_authenticity();
	$commlib->remove_received_page($_REQUEST["remove"]);
}
if (isset($_REQUEST["save"])) {
	check_ticket('received-pages');
	$commlib->update_received_page($_REQUEST["receivedPageId"], $_REQUEST["pageName"], $_REQUEST["data"], $_REQUEST["comment"]);
	$smarty->assign('pageName', $_REQUEST["pageName"]);
	$smarty->assign('data', $_REQUEST["data"]);
	$smarty->assign('comment', $_REQUEST["comment"]);
	$smarty->assign('receivedPageId', 0);
	$smarty->assign('parsed', $tikilib->parse_data($_REQUEST["data"]));
}
if (!empty($_REQUEST['checked']) && (!empty($_REQUEST['prefix']) || !empty($_REQUEST['postfix']))) {
	check_ticket('received-pages');
	foreach($_REQUEST['checked'] as $page) {
		$newpage = empty($_REQUEST['postfix']) ? $_REQUEST['prefix'] . $page : $page . $_REQUEST['postfix'];
		if ($tikilib->page_exists($newpage)) {
			$errors[] = array('error' => 'Page already exists', 'param' => $page);
		}
	}
	if (empty($errors)) {
		$commlib->rename_structure_pages($_REQUEST['checked'], isset($_REQUEST['prefix']) ? $_REQUEST['prefix'] : '', isset($_REQUEST['postfix']) ? $_REQUEST['postfix'] : '');
	}
}
if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'receivedDate_desc';
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
$channels = $tikilib->list_received_pages($offset, $maxRecords, $sort_mode, $find, 'p');
$smarty->assign_by_ref('channels', $channels["data"]);
$smarty->assign_by_ref('cant', $channels['cant']);
if (!isset($_REQUEST['sort_modes'])) {
	$sort_modes = 'receivedDate_desc';
} else {
	$sort_modes = $_REQUEST['sort_modes'];
}
$structures = $tikilib->list_received_pages(0, -1, $sort_modes, $find, 's');
$smarty->assign_by_ref('structures', $structures['data']);
if (!empty($errors)) {
	$smarty->assign_by_ref('errors', $errors);
}
ask_ticket('received-pages');
// Display the template
$smarty->assign('mid', 'tiki-received_pages.tpl');
$smarty->display("tiki.tpl");
