<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-print_pages.php,v 1.4 2003-08-07 04:33:57 rossta Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

if ($feature_wiki_multiprint != 'y') {
	$smarty->assign('msg', tra("This feature is disabled"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

// Now check permissions if user can view wiki pages
if ($tiki_p_view != 'y') {
	$smarty->assign('msg', tra("Permission denied you cannot view this page"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

if (!isset($_REQUEST["printpages"])) {
	$printpages = array();
} else {
	$printpages = unserialize(urldecode($_REQUEST["printpages"]));
}

if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}

$smarty->assign('find', $find);

if (isset($_REQUEST["addpage"])) {
	if (!in_array($_REQUEST["pageName"], $printpages)) {
		$printpages[] = $_REQUEST["pageName"];
	}
}

if (isset($_REQUEST["clearpages"])) {
	$printpages = array();
}

$smarty->assign('printpages', $printpages);
$form_printpages = urlencode(serialize($printpages));
$smarty->assign('form_printpages', $form_printpages);

$pages = $tikilib->list_pages(0, -1, 'pageName_asc', $find);
$smarty->assign_by_ref('pages', $pages["data"]);

// Display the template
$smarty->assign('mid', 'tiki-print_pages.tpl');
$smarty->display("styles/$style_base/tiki.tpl");

?>