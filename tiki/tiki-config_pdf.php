<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-config_pdf.php,v 1.15 2006-12-08 00:26:36 mose Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');
include_once ('lib/structures/structlib.php');

//if($feature_wiki != 'y') {
//  die;
//}

//Permissions
if ($tiki_p_view != 'y') {
	$smarty->assign('msg', tra("Permission denied you cannot view this page"));
	$smarty->display("error.tpl");
	die;
}

//feature
$feature_wiki_pdf = $tikilib->get_preference('feature_wiki_pdf', 'n');

if ($feature_wiki_pdf != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_wiki_pdf");
	$smarty->display("error.tpl");
	die;
}

//defaults
if (!isset($_REQUEST["page_ref_id"])) {
	$_REQUEST["page_ref_id"] = '';
}

if (!isset($_REQUEST["media"])) {
	$_REQUEST["media"] = 'A4';
}

if (!isset($_REQUEST["scalepoints"])) {
	if(!$_POST){
		$_REQUEST["scalepoints"] = '1';
	} else {
		$_REQUEST["scalepoints"] = '0';
	}
}

if (!isset($_REQUEST["renderimages"])) {
	if(!$_POST){
		$_REQUEST["renderimages"] = '1';
	} else {
		$_REQUEST["renderimages"] = '0';
	}
}
if (!isset($_REQUEST["renderlinks"])) {
	if(!$_POST){
		$_REQUEST["renderlinks"] = '1';
	} else {
		$_REQUEST["renderlinks"] = '0';
	}
}

if (!isset($_REQUEST["leftmargin"])) {
	$_REQUEST["leftmargin"] = '15';
}
if (!isset($_REQUEST["rightmargin"])) {
	$_REQUEST["rightmargin"] = '15';
}
if (!isset($_REQUEST["topmargin"])) {
	$_REQUEST["topmargin"] = '15';
}
if (!isset($_REQUEST["bottommargin"])) {
	$_REQUEST["bottommargin"] = '15';
}
if (!isset($_REQUEST["landscape"])) {
	$_REQUEST["landscape"] = '0';
}
if (!isset($_REQUEST["pageborder"])) {
	$_REQUEST["pageborder"] = '0';
}
if (!isset($_REQUEST["encoding"])) {
	$_REQUEST["encoding"] = '';
}
if (!isset($_REQUEST["method"])) {
	$_REQUEST["method"] = 'fpdf';
}
if (!isset($_REQUEST["pdfversion"])) {
	$_REQUEST["pdfversion"] = '1.3';
}

if (!isset($_REQUEST["convertpages"])) {
	$convertpages = array();

	if (!empty($_REQUEST["page_ref_id"]) ) {
		$struct = $structlib->get_subtree($_REQUEST["page_ref_id"]);
		foreach($struct as $struct_page) {
			// Handle dummy last entry
			if ($struct_page["pos"] != '' && $struct_page["last"] == 1) continue;
			$convertpages[] = $struct_page["pageName"];
		}
	} elseif (isset($_REQUEST["page"]) && $tikilib->page_exists($_REQUEST["page"])) {
		$convertpages[] = $_REQUEST["page"];
	}
} else {
	$convertpages = unserialize(urldecode($_REQUEST['convertpages']));
}

if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}

// assign to smarty
$smarty->assign('media', $_REQUEST["media"]);
$smarty->assign('scalepoints', $_REQUEST["scalepoints"]);
$smarty->assign('renderimages', $_REQUEST["renderimages"]);
$smarty->assign('renderlinks', $_REQUEST["renderlinks"]);
$smarty->assign('leftmargin', $_REQUEST["leftmargin"]);
$smarty->assign('rightmargin', $_REQUEST["rightmargin"]);
$smarty->assign('topmargin', $_REQUEST["topmargin"]);
$smarty->assign('bottommargin', $_REQUEST["bottommargin"]);
$smarty->assign('landscape', $_REQUEST["landscape"]);
$smarty->assign('pageborder', $_REQUEST["pageborder"]);
$smarty->assign('encoding', $_REQUEST["encoding"]);
$smarty->assign('method', $_REQUEST["method"]);
$smarty->assign('pdfversion', $_REQUEST["pdfversion"]);
$smarty->assign('page_ref_id', $_REQUEST["page_ref_id"]);


//Format dropdown
$smarty->assign('format_options', array("Letter","Legal","Executive","A0Oversize","A0","A1","A2","A3","A4","A5","B5","Folio","A6","A7","A8","A9","A10"));


$smarty->assign('find', $find);

//add pages
if (isset($_REQUEST["addpage"])) {
	foreach (array_keys($_REQUEST["addpageName"])as $item) {
		if (!in_array($_REQUEST["addpageName"]["$item"], $convertpages)) {
			$convertpages[] = $_REQUEST["addpageName"]["$item"];
		}
	}
}

//remove pages
if (isset($_REQUEST["rempage"])) {
	foreach (array_keys($_REQUEST["rempageName"])as $item) {
		$key = array_search($_REQUEST["rempageName"]["$item"], $convertpages);
		if ($key !== NULL) {
			unset ($convertpages[$key]);
		}
	}
}

//clear
if (isset($_REQUEST["clearpages"])) {
	$convertpages = array();
}

$smarty->assign('convertpages', $convertpages);
$form_convertpages = urlencode(serialize($convertpages));
$smarty->assign('form_convertpages', $form_convertpages);

// insert pdfcreation code here
$pages = $tikilib->list_pages(0, -1, 'pageName_asc', $find);
$smarty->assign_by_ref('pages', $pages["data"]);

ask_ticket('pdf');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

$smarty->assign('mid', 'tiki-config_pdf.tpl');
$smarty->display("tiki.tpl");

?>
