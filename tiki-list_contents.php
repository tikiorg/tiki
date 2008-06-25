<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-list_contents.php,v 1.17 2007-10-12 07:55:28 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');
include_once ('lib/dcs/dcslib.php');
$auto_query_args = array('sort_mode','offset','find');

if (!isset($dcslib)) {
	$dcslib = new DCSLib($dbTiki);
}

if ($prefs['feature_dynamic_content'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_dynamic_content");

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_admin_dynamic != 'y') {
	$smarty->assign('msg', tra("You do not have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}

if (isset($_REQUEST["remove"])) {
  $area = 'delcontents';
  if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
    key_check($area);
    $dcslib->remove_contents($_REQUEST["remove"]);
  } else {
    key_get($area);
  }
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

?>
