<?php

// $Header: 

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//TODO: permission, sea surfing controlling

// Initialization
require_once('tiki-setup.php');

include_once('lib/multilingual/multilinguallib.php');

if ($feature_multilingual != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_multilingual");
	$smarty->display("error.tpl");
	die;
}

//if ($tiki_p_edit_translation != 'y') {
//	$smarty->assign('msg', tra("You dont have permission to use this feature"));
//	$smarty->display("error.tpl");
//	die;
//}

if (!(isset($_REQUEST['page']) && $_REQUEST['page'])) {
	$smarty->assign('msg',tra("No page indicated"));
	$smarty->display("error.tpl");
	die;
}

$info = $tikilib->get_page_info($_REQUEST['page']);
if (empty($info)) {
	$smarty->assign('msg',tra("Page cannot be found"));
	$smarty->display("error.tpl");
	die;
}

$smarty->assign('page', $_REQUEST['page']);
$type = "wiki page";
$objId = $info['page_id'];
$lang = $info['lang'];

if (isset($_REQUEST['lang']) && !empty($_REQUEST['lang']) && $_REQUEST['lang'] != "NULL"
				&& $lang != $_REQUEST['lang']) { // update the language
	$info['lang'] = $_REQUEST['lang'];
	$multilinguallib->updatePageLang($objId, $_REQUEST['lang']);
	$lang = $_REQUEST['lang'];
}
$smarty->assign('lang', $lang);

if (isset($_REQUEST['srcName']) && $_REQUEST['srcName']) { // attach to a translation set
	if (empty($lang) || $lang == "NULL") {
		$error = "traLang";
		$smarty->assign('error', $error);
	}
	else {
		$srcInfo = $tikilib->get_page_info($_REQUEST['srcName']);
		if (empty($srcInfo)) {
			$error = "srcExists";
			$smarty->assign('error', $error);
		}
		else 
			if (!(isset($srcInfo['lang']) && $srcInfo['lang'])) {
				$error = "srcLang";
				$smarty->assign('error', $error);
			}
			//elseif (isset($_REQUEST['update'])) {
			//	updateTranslation($type, $srcInfo['page_id'], $objId, $lang);
			//}
			else {
				$error = $multilinguallib->insertTranslation($type, $srcInfo['page_id'], $srcInfo['lang'], $objId, $lang);
				if ($error)
					$smarty->assign('error', $error);
				else
					$_REQUEST['srcName'] = "";
			}
	}
	$smarty->assign('srcName', $_REQUEST['srcName']);
}

if (isset($_REQUEST['detach'])) { // detach from a translation set
	$multilinguallib->detachTranslation($type, $objId);
}
$trads = $multilinguallib->getTranslations('wiki page', $objId, $_REQUEST['page'], $lang, true);
$smarty->assign('trads', $trads);

$languages = $tikilib->list_languages();
$smarty->assign_by_ref('languages', $languages);

// Display the template
$smarty->assign('mid', 'tiki-edit_translation.tpl');
$smarty->display("tiki.tpl");

?>
