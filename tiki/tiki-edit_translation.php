<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-edit_translation.php,v 1.2 2004-06-10 09:46:48 sylvieg Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//TODO: add permission, sea surfing controlling, add new object type
//TODO: list_articles must be replaced by something lighter
//TODO: list languages must used browser preferences
//QUESTION: can we translated all the objects or only those the user can see - if yes filter list_pages

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

if (!(isset($_REQUEST['page']) && $_REQUEST['page']) && !(isset($_REQUEST['id']) && $_REQUEST['id'])) {
	$smarty->assign('msg',tra("No object indicated"));
	$smarty->display("error.tpl");
	die;
}

if (isset($_REQUEST['page']) && $_REQUEST['page']) {
	$info = $tikilib->get_page_info($_REQUEST['page']);
	if (empty($info)) {
		$smarty->assign('msg',tra("Page cannot be found"));
		$smarty->display("error.tpl");
		die;
	}
	$name = $_REQUEST['page'];
	$type = "wiki page";
	$objId = $info['page_id'];
	$lang = $info['lang'];
}
else if ($_REQUEST['id']) {
	if (!isset($_REQUEST['type'])) {
		$smarty->assign('msg',tra("No type indicated"));
		$smarty->display("error.tpl");
		die;
	}
	if ($_REQUEST['type'] == "wiki page") {
		$info = $tikilib->get_page_info_from_id($_REQUEST['id']);
		if (empty($info)) {
			$smarty->assign('msg',tra("Page cannot be found"));
			$smarty->display("error.tpl");
			die;
		}
		$name = $info['pageName'];
		$type = "wiki page";
		$objId = $info['page_id'];
		$lang = $info['lang'];
	}
	else if ($_REQUEST['type'] == "article") {
		$info = $tikilib->get_article($_REQUEST["id"]);
		if (empty($info)) {
			$smarty->assign('msg', tra("Article not found"));
			$smarty->display("error.tpl");
			die;
		}
		$smarty->assign_by_ref('articles', $articles["data"]);
		$name = $info['title'];
		$type = "article";
		$objId = $_REQUEST['id'];
		$lang = $info['lang'];
		$find_objects = '';
		$articles = $tikilib->list_articles(0, -1, 'title_asc', $find_objects, '', $user);
		$smarty->assign_by_ref('articles', $articles["data"]);
	}
}

$smarty->assign('name', $name);
$smarty->assign('type', $type);
$smarty->assign('id', $objId);

if (isset($_REQUEST['lang']) && !empty($_REQUEST['lang']) && $_REQUEST['lang'] != "NULL"
				&& $lang != $_REQUEST['lang']) { // update the language

	$error = $multilinguallib->updatePageLang($type, $objId, $_REQUEST['lang']);
	if ($error)
		$smarty->assign('error', $error);
	else {
		$info['lang'] = $_REQUEST['lang'];	
		$lang = $_REQUEST['lang'];
	}
}
$smarty->assign('lang', $lang);

if (isset($_REQUEST['detach'])) { // detach from a translation set
	$multilinguallib->detachTranslation($type, $_REQUEST['srcId']);
}
else if (isset($_REQUEST['srcName']) && $_REQUEST['srcName']) { // attach to a translation set
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
else if  (isset($_REQUEST['srcId']) && $_REQUEST['srcId']) {
	if (empty($lang) || $lang == "NULL") {
		$error = "traLang";
		$smarty->assign('error', $error);
	}
	else {
		$srcInfo = $tikilib->get_article($_REQUEST["srcId"]);
	if (empty($srcInfo)) {
			$error = "srcExists";
			$smarty->assign('error', $error);
		}
		else 
			if (!(isset($srcInfo['lang']) && $srcInfo['lang'])) {
				$error = "srcLang";
				$smarty->assign('error', $error);
			}
			else {
				$error = $multilinguallib->insertTranslation($type, $srcInfo['articleId'], $srcInfo['lang'], $objId, $lang);
				if ($error)
					$smarty->assign('error', $error);
				else
					$_REQUEST['srcName'] = "";
			}
	}
	$smarty->assign('srcId', $_REQUEST['srcId']);
}

$trads = $multilinguallib->getTranslations($type, $objId, $name, $lang, true);
$smarty->assign('trads', $trads);

$languages = $tikilib->list_languages();
$smarty->assign_by_ref('languages', $languages);

// Display the template
$smarty->assign('mid', 'tiki-edit_translation.tpl');
$smarty->display("tiki.tpl");

?>
