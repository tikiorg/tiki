<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'wiki page';
require_once ('tiki-setup.php');
include_once ('lib/structures/structlib.php');
$auto_query_args = array('page_ref_id', 'page', 'find', 'pageName', 'structureId', 'offset', 'printpages', 'printstructures');

$access->check_feature('feature_wiki_multiprint');
$access->check_permission('tiki_p_view');

$smarty->assign('headtitle', tra('Print'));
if (!isset($cookietab)) { $cookietab = '1'; }
if (!isset($_REQUEST['printpages']) && !isset($_REQUEST['printstructures'])) {
	$printpages = array();
	$printstructures = array();
	if (isset($_REQUEST["page_ref_id"])) {
		$info = $structlib->s_get_page_info($_REQUEST['page_ref_id']);
		if (!empty($info)) {
			$printstructures[] = $_REQUEST['page_ref_id'];
		}
	} elseif (isset($_REQUEST["page"]) && $tikilib->page_exists($_REQUEST["page"])) {
		$printpages[] = $_REQUEST["page"];
	}
} else {
	$printpages = unserialize(urldecode($_REQUEST["printpages"]));
	$printstructures = unserialize(urldecode($_REQUEST['printstructures']));
}
if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}
$smarty->assign('find', $find);
if (isset($_REQUEST["addpage"])) {
	if (!in_array($_REQUEST["pageName"], $printpages)) {
		foreach($_REQUEST['pageName'] as $value) {
			$printpages[] = $value;
		}
	}
	$cookietab = 2;
}
if (isset($_REQUEST["removepage"])) {
		foreach($_REQUEST['selectedpages'] as $value) {
			unset($printpages[$value]);
		}
		$printpages = array_merge($printpages);
	$cookietab = 2;
}
if (isset($_REQUEST["clearpages"])) {
	$printpages = array();
	$cookietab = 2;
}
if (isset($_REQUEST["clearstructures"])) {
	$printstructures = array();
}
if (isset($_REQUEST['addstructurepages'])) {
	$struct = $structlib->get_subtree($_REQUEST["structureId"]);
	foreach($struct as $struct_page) {
		// Handle dummy last entry
		if ($struct_page["pos"] != '' && $struct_page["last"] == 1) continue;
		$printpages[] = $struct_page["pageName"];
	}
	$cookietab = 2;
}
if (isset($_REQUEST['addstructure'])) {
	$info = $structlib->s_get_page_info($_REQUEST['structureId']);
	if (!empty($info)) {
		$printstructures[] = $_REQUEST['structureId'];
	}
}
$smarty->assign_by_ref('printpages', $printpages);
$smarty->assign_by_ref('printstructures', $printstructures);
$form_printpages = urlencode(serialize($printpages));
$smarty->assign_by_ref('form_printpages', $form_printpages);
$form_printstructures = urlencode(serialize($printstructures));
$smarty->assign_by_ref('form_printstructures', $form_printstructures);
$pages = $tikilib->list_pageNames(0, -1, 'pageName_asc', $find);
$smarty->assign_by_ref('pages', $pages["data"]);
$structures = $structlib->list_structures(0, -1, 'pageName_asc', $find);
$smarty->assign_by_ref('structures', $structures["data"]);
foreach($printstructures as $page_ref_id) {
	foreach($structures['data'] as $struct) {
		if ($struct['page_ref_id'] == $page_ref_id) {
			$printnamestructures[] = $struct['pageName'];
			break;
		}
	}
}
$smarty->assign_by_ref('printnamestructures', $printnamestructures);
$smarty->assign('cookietab', $cookietab);
include_once ('tiki-section_options.php');
ask_ticket('print-pages');
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

$smarty->assign('mid', 'tiki-print_pages.tpl');
$smarty->display("tiki.tpl");
