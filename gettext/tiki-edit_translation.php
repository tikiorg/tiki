<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('tiki-setup.php');

include_once('lib/multilingual/multilinguallib.php');
include_once('modules/mod-func-translation.php');

execute_module_translation();

$access->check_feature('feature_multilingual');
	
if (!(isset($_REQUEST['page']) && $_REQUEST['page']) && !(isset($_REQUEST['id']) && $_REQUEST['id'])) {
	$smarty->assign('msg',tra("No object indicated"));
	$smarty->display("error.tpl");
	die;
}

include_once("lang/langmapping.php");

if ((!isset($_REQUEST['type']) || $_REQUEST['type'] == 'wiki page' || $_REQUEST['type'] == 'wiki') && isset($_REQUEST['page']) && $_REQUEST['page']) {
	if ( $tikilib->get_approved_page($_REQUEST['page']) ) {		
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
	$cat_type = 'wiki page';
	$cat_objid = $name;

	$edit_data = $info['data'];
	$smarty->assign('pagedata', TikiLib::htmldecode($edit_data));
	
	if ($prefs['feature_translation_incomplete_notice'] == 'y') {
		$smarty->assign('translate_message', "^".tra("Translation of this page is incomplete.")."^\n\n");
	}
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
		$fullLangName = $langmapping[$langpage][0];
		$smarty->assign( 'languageName', $fullLangName );
		$cat_type = 'wiki page';
		$cat_objid = $name;
		
		$edit_data = $info['data'];
		$smarty->assign('pagedata', TikiLib::htmldecode($edit_data));
		
		if ($prefs['feature_translation_incomplete_notice'] == 'y') {
			$smarty->assign('translate_message', "^".tra("Translation of this page is incomplete.")."^\n\n");
		}
		
	}
	else if ($_REQUEST['type'] == "article") {
		global $artlib; require_once 'lib/articles/artlib.php';
		$info = $artlib->get_article($_REQUEST["id"]);
		if (empty($info)) {
			$smarty->assign('msg', tra("Article not found"));
			$smarty->display("error.tpl");
			die;
		}
		$name = $info['title'];
		$type = "article";
		$objId = $_REQUEST['id'];
		$langpage = $info['lang'];
		$cat_type = 'article';
		$cat_objid = $objId;
		$fullLangName = $langmapping[$langpage][0];
		$smarty->assign( 'languageName', $fullLangName );
	}
}

$smarty->assign('name', $name);
$smarty->assign('type', $type);
$smarty->assign('id', $objId);

include_once 'categorize_list.php';

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
  if ($prefs['feature_wikiapproval'] == 'y' && $tiki_p_edit != 'y' && $tikilib->page_exists( $prefs['wikiapproval_prefix'] . $name ) && $tikilib->user_has_perm_on_object($user, $prefs['wikiapproval_prefix'] . $name, 'wiki page', 'tiki_p_edit')) {
		$allowed_for_staging_only = 'y';
		$smarty->assign('allowed_for_staging_only', 'y');
  }  
  if ((!isset($allowed_for_staging_only) || $allowed_for_staging_only != 'y') && !($tiki_p_admin_wiki== 'y' || $tiki_p_edit == 'y' || ($prefs['wiki_creator_admin'] == 'y' && $user && $info['creator'] == $user) )) {
	  $smarty->assign('errortype', 401);
		$smarty->assign('msg', tra("You do not have permission to edit this page."));
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
	if ($prefs['feature_wikiapproval'] == 'y' && $tikilib->get_approved_page($_REQUEST['srcName']) ) {
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
		global $artlib; require_once 'lib/articles/artlib.php';
		$srcInfo = $artlib->get_article($_REQUEST["srcId"]);
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
					AND a.type = 'wiki page'
					AND b.type = 'wiki page'
			)
		ORDER BY pageName ASC", array($langpage, $info['page_id'], $langpage) );
  $pages = array( 'data' => array() );
  while( $row = $result->fetchRow() )
    $pages['data'][] = $row;

  if ($prefs['feature_wikiapproval'] == 'y') {
  	// staging pages should be excluded from list as translation always happens only from the approved pages
  	$pages_data = array();
  	foreach($pages["data"] as $p) {
  		if ( $tikilib->get_staging_page($p['pageName']) ) {
			$t_pages_data[] = $p;
  		}
  	}
  	$pages["data"] = $t_pages_data;
  }  
	$smarty->assign_by_ref('pages', $pages["data"]);
}
else if ($type == "article") {
	if ($tiki_p_admin_cms != 'y' && !$tikilib->user_has_perm_on_object($user, $id, 'article', 'tiki_p_edit_article') and ($info['author'] != $user or $info['creator_edit'] != 'y')) {
		$smarty->assign('errortype', 401);
		$smarty->assign('msg', tra("You do not have permission to edit this article"));
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
if (count($languages) == 1) {
   $smarty->assign('only_one_language_left', 'y');
}

ask_ticket('edit-translation');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

if ($type == 'article') {
	$articles = $artlib->list_articles(0, -1, 'title_asc', '', '', '', $user);
	$smarty->assign('articles', $articles["data"]);
}

// Display the template
$smarty->assign('mid', 'tiki-edit_translation.tpl');
$smarty->display("tiki.tpl");

function execute_module_translation() { 
	global $smarty;
	$module_reference = array(
		'name' => 'translation',
		'params' => '',
		'position' => 'r',
		'ord' => 1,
		'moduleId' => 0
	);

	global $modlib; require_once 'lib/modules/modlib.php';	

	$out = $modlib->execute_module( $module_reference );
	$smarty->assign('content_of_update_translation_section', $out);
}
