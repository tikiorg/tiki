<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-lastchanges.php,v 1.19.2.1 2007-11-08 21:31:12 ricks99 Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
$section = 'wiki page';
$section_class = "wiki_page manage";	// This will be body class instead of $section
require_once ('tiki-setup.php');
include_once ('lib/wiki/histlib.php');

$auto_query_args = array('sort_mode', 'offset', 'find', 'days');

$smarty->assign('headtitle',tra('Last Changes'));

if ($prefs['feature_wiki'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_wiki");
	$smarty->display("error.tpl");
	die;
}

if ($prefs['feature_lastChanges'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_lastChanges");
	$smarty->display("error.tpl");
	die;
}

if($tiki_p_view != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg',tra("Permission denied you cannot view this page"));
	$smarty->display("error.tpl");
	die;  
}

if (!isset($_REQUEST["find"])) {
	$findwhat = '';
} else {
	$findwhat = $_REQUEST["find"];
}

$smarty->assign('find', $findwhat);

if (!isset($_REQUEST["days"])) {
	$days = 1;
} else {
	$days = $_REQUEST["days"];
}

if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'lastModif_desc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}

$smarty->assign_by_ref('days', $days);
$smarty->assign_by_ref('findwhat', $findwhat);
$smarty->assign_by_ref('sort_mode', $sort_mode);

if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}

$smarty->assign_by_ref('offset', $offset);

// Get a list of last changes to the Wiki database
$more = 0;
$lastchanges = $histlib->get_last_changes($days, $offset, $maxRecords, $sort_mode, $findwhat);
$smarty->assign_by_ref('cant_records', $lastchanges["cant"]);

// If there're more records then assign next_offset
$cant_pages = ceil($lastchanges["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages', $cant_pages);
$smarty->assign('actual_page', 1 + ($offset / $maxRecords));

if ($lastchanges["cant"] > ($offset + $maxRecords)) {
	$smarty->assign('next_offset', $offset + $maxRecords);
} else {
	$smarty->assign('next_offset', -1);
}

if ($offset > 0) {
	$smarty->assign('prev_offset', $offset - $maxRecords);
} else {
	$smarty->assign('prev_offset', -1);
}

$smarty->assign_by_ref('lastchanges', $lastchanges["data"]);
ask_ticket('lastchanges');

include_once ('tiki-section_options.php');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the template
$smarty->assign('mid', 'tiki-lastchanges.tpl');
$smarty->display("tiki.tpl");

?>
