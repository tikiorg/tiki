<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-edit_translation.php,v 1.16.2.12 2008-03-04 15:44:58 lphuberdeau Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//TODO: add permission, sea surfing controlling, add new object type
//TODO: list_articles must be replaced by something lighter
//TODO: list languages must used browser preferences
//QUESTION: can we translated all the objects or only those the user can see - if yes filter list_pages

// Initialization
require_once('tiki-setup.php');

include_once('lib/multilingual/multilinguallib.php');

if ($prefs['feature_multilingual'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_multilingual");
	$smarty->display("error.tpl");
	die;
}

if (!(isset($_REQUEST['page']) && $_REQUEST['page']) && !(isset($_REQUEST['id']) && $_REQUEST['id'])) {
	$smarty->assign('msg',tra("No object indicated"));
	$smarty->display("error.tpl");
	die;
}

include_once("lang/langmapping.php");

if (isset($_REQUEST['page']) && $_REQUEST['page']) {
	if ($prefs['feature_wikiapproval'] == 'y' && substr($_REQUEST['page'], 0, strlen($prefs['wikiapproval_prefix'])) == $prefs['wikiapproval_prefix']) {		
		$smarty->assign('msg',tra("Page is a staging copy. Translation must begin from the approved copy."));
		$smarty->display("error.tpl");
		die;
	}
	$info = $tikilib->get_page_info($_REQUEST['page']);
	if (empty($info)) {
		$smarty->assign('msg',tra("Page cannot be found"));
		$smarty->display("error.tpl");
		die;
	}
	$name = $_REQUEST['page'];
	$type = "wiki page";
	$objId = $info['page_id'];
	$langpage = $info['lang'];
	$fullLangName = $langmapping[$langpage][0];
	$smarty->assign( 'languageName', $fullLangName );

	$edit_data = $info['data'];
	$smarty->assign('pagedata', TikiLib::htmldecode($edit_data));
	$smarty->assign('translate_message', tra('Translation in progress.', $langpage));

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
		$langpage = $info['lang'];
	}
	else if ($_REQUEST['type'] == "article") {
		$info = $tikilib->get_article($_REQUEST["id"]);
		if (empty($info)) {
			$smarty->assign('msg', tra("Article not found"));
			$smarty->display("error.tpl");
			die;
		}
		$name = $info['title'];
		$type = "article";
		$objId = $_REQUEST['id'];
		$langpage = $info['lang'];
		$articles = $tikilib->list_articles(0, -1, 'title_asc', '', '', $user);
		$smarty->assign_by_ref('articles', $articles["data"]);
	}
}

$smarty->assign('name', $name);
$smarty->assign('type', $type);
$smarty->assign('id', $objId);

if (isset($_REQUEST['langpage']) && !empty($_REQUEST['langpage']) && $_REQUEST['langpage'] != "NULL"
				&& $langpage != $_REQUEST['langpage']) { // update the language

	$error = $multilinguallib->updatePageLang($type, $objId, $_REQUEST['langpage']);
	if ($error)
		$smarty->assign('error', $error);
	else {
		$info['lang'] = $_REQUEST['langpage'];	
		$langpage = $_REQUEST['langpage'];
	}

	$fullLangName = $langmapping[$langpage][0];
	$smarty->assign( 'languageName', $fullLangName );
}
$smarty->assign('langpage', $langpage);

if ($type == "wiki page") {
  $tikilib->get_perm_object($name, 'wiki page', $info, true);	
  if ($prefs['feature_wikiapproval'] == 'y' && $tiki_p_edit != 'y' && $tikilib->page_exists( $prefs['wikiapproval_prefix'] . $name ) && $tikilib->user_has_perm_on_object($user, $prefs['wikiapproval_prefix'] . $name, 'wiki page', 'tiki_p_edit', 'tiki_p_edit_categorized')) {
		$allowed_for_staging_only = 'y';
		$smarty->assign('allowed_for_staging_only', 'y');
  }  
  if ((!isset($allowed_for_staging_only) || $allowed_for_staging_only != 'y') && !($tiki_p_admin_wiki== 'y' || $tiki_p_edit == 'y' || ($prefs['wiki_creator_admin'] == 'y' && $user && $info['creator'] == $user) )) {
		$smarty->assign('msg', tra("Permission denied you cannot edit this page"));
		$smarty->display("error.tpl");
		die;
	}
}

if (!isset($allowed_for_staging_only)) {
// people blocked from approved page cannot access the following settings

if (isset($_REQUEST['detach']) && isset($_REQUEST['srcId']) && $tiki_p_detach_translation == 'y') { // detach from a translation set
	check_ticket('edit-translation');
	$multilinguallib->detachTranslation($type, $_REQUEST['srcId']);
}
 else if (isset($_REQUEST['set']) && !empty($_REQUEST['srcName'])) { // attach to a translation set
	check_ticket('edit-translation');
	if ($prefs['feature_wikiapproval'] == 'y' && substr($_REQUEST['srcName'], 0, strlen($prefs['wikiapproval_prefix'])) == $prefs['wikiapproval_prefix']) {
		$smarty->assign('msg',tra("Page is a staging copy. Translation must begin from the approved copy."));
		$smarty->display("error.tpl");
		die;
	}	
	if (empty($langpage) || $langpage == "NULL") {
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
			elseif ($srcInfo['page_id'] != $objId) {
				$error = $multilinguallib->insertTranslation($type, $srcInfo['page_id'], $srcInfo['lang'], $objId, $langpage);
				if ($error)
					$smarty->assign('error', $error);
				else
					$_REQUEST['srcName'] = "";
			}
	}
	$smarty->assign('srcName', $_REQUEST['srcName']);
}
else if  (isset($_REQUEST['set']) && !empty($_REQUEST['srcId'])) {
	check_ticket('edit-translation');
	if (empty($langpage) || $langpage == "NULL") {
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
				$error = $multilinguallib->insertTranslation($type, $srcInfo['articleId'], $srcInfo['lang'], $objId, $langpage);
				if ($error)
					$smarty->assign('error', $error);
				else
					$_REQUEST['srcName'] = "";
			}
	}
	$smarty->assign('srcId', $_REQUEST['srcId']);
}

} // end of if $allowed_for_staging_only == 'y'

if ($type == "wiki page") {
	// Fetches the list of pages with a langage assigned
	// that is different than those already included in the
	// current set.
	$result = $tikilib->query("
		SELECT lang, pageName 
		FROM tiki_pages 
		WHERE
			lang IS NOT NULL
			AND lang <> ?
			AND page_id NOT IN(
				SELECT
					a.page_id
				FROM
					tiki_pages a
					INNER JOIN tiki_translated_objects b ON a.lang = b.lang
					INNER JOIN tiki_translated_objects c ON b.traId = c.traId
				WHERE
					c.type = 'wiki page'
					AND c.objId = ?
			)
			AND page_id NOT IN(
				SELECT
					a.objId
				FROM
					tiki_translated_objects a
					INNER JOIN tiki_translated_objects b ON a.traId = b.traId
				WHERE
					b.lang = ?
			)
		ORDER BY pageName ASC", array($langpage, $info['page_id'], $langpage) );
  $pages = array( 'data' => array() );
  while( $row = $result->fetchRow() )
    $pages['data'][] = $row;

  if ($prefs['feature_wikiapproval'] == 'y') {
  	// staging pages should be excluded from list as translation always happens only from the approved pages
  	$pages_data = array();
  	foreach($pages["data"] as $p) {
  		if (substr($p["pageName"], 0, strlen($prefs['wikiapproval_prefix'])) != $prefs['wikiapproval_prefix']) {
			$t_pages_data[] = $p;
  		}
  	}
  	$pages["data"] = $t_pages_data;
  }  
	$smarty->assign_by_ref('pages', $pages["data"]);
}
else if ($type == "article") {
	if ($tiki_p_admin_cms != 'y' && !$tikilib->user_has_perm_on_object($user, $id, 'article', 'tiki_p_edit_article') and ($info['author'] != $user or $info['creator_edit'] != 'y')) {
		$smarty->assign('msg', tra("Permission denied you cannot edit this article"));
		$smarty->display("error.tpl");
		die;
	}
}

$trads = $multilinguallib->getTranslations($type, $objId, $name, $langpage, true);
$smarty->assign('trads', $trads);

$usedLang = array();
foreach( $trads as $trad )
	$usedLang[] = $trad['lang'];

$rawLangs = $tikilib->list_languages();
$languages = array();
foreach( $rawLangs as $langInfo )
	if( ! in_array( $langInfo['value'], $usedLang ) )
		$languages[] = $langInfo;
$smarty->assign_by_ref('languages', $languages);

ask_ticket('edit-translation');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the template
$smarty->assign('mid', 'tiki-edit_translation.tpl');
$smarty->display("tiki.tpl");

?>
