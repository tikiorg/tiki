<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-print_pages.php,v 1.9 2004-03-28 07:32:23 mose Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');
include_once ('lib/structures/structlib.php');

if ($feature_wiki_multiprint != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_wiki_multiprint");

	$smarty->display("error.tpl");
	die;
}

// Now check permissions if user can view wiki pages
if ($tiki_p_view != 'y') {
	$smarty->assign('msg', tra("Permission denied you cannot view this page"));

	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["printpages"])) {
	$printpages = array();

	if (isset($_REQUEST["page_ref_id"]) ) {
		$struct = $structlib->get_subtree($_REQUEST["page_ref_id"]);
		foreach($struct as $struct_page) {
			// Handle dummy last entry
			if ($struct_page["pos"] != '' && $struct_page["last"] == 1) continue;
			$printpages[] = $struct_page["pageName"];
		}
	}elseif (isset($_REQUEST["page"]) && $tikilib->page_exists($_REQUEST["page"])) {
		$printpages[] = $_REQUEST["page"];
	}
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

ask_ticket('print-pages');

// Display the template
$smarty->assign('mid', 'tiki-print_pages.tpl');
$smarty->display("tiki.tpl");

?>
