<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
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
	$smarty->assign('msg', tra("No object indicated"));
	$smarty->display("error.tpl");
	die;
}

if (isset($_REQUEST['type'], $_REQUEST['id']) && $_REQUEST['type'] == 'wiki page') {
	$_REQUEST['page'] = $tikilib->get_page_name_from_id($_REQUEST['id']);
}

include_once("lang/langmapping.php");

if ((!isset($_REQUEST['type']) || $_REQUEST['type'] == 'wiki page' || $_REQUEST['type'] == 'wiki') && isset($_REQUEST['page']) && $_REQUEST['page']) {
	$info = $tikilib->get_page_info($_REQUEST['page']);
	if (empty($info)) {
		$smarty->assign('msg', tra("Page cannot be found"));
		$smarty->display("error.tpl");
		die;
	}
	$name = $_REQUEST['page'];
	$type = "wiki page";
	$objId = $info['page_id'];
	$langpage = $info['lang'];
	$fullLangName = $langmapping[$langpage][0];
	$smarty->assign('languageName', $fullLangName);
	$cat_type = 'wiki page';
	$cat_objid = $name;

	$edit_data = $info['data'];
	$smarty->assign('pagedata', TikiLib::htmldecode($edit_data));
	
	if ($prefs['feature_translation_incomplete_notice'] == 'y') {
		$smarty->assign('translate_message', "^".tra("Translation of this page is incomplete.")."^\n\n");
	}
} else if ($_REQUEST['id']) {
	$smarty->assign('msg', tra("Only wiki pages are supported."));
	$smarty->display("error.tpl");
	die;
}

$smarty->assign('name', $name);
$smarty->assign('type', $type);
$smarty->assign('id', $objId);

include_once 'categorize_list.php';

if (isset($_REQUEST['langpage']) && !empty($_REQUEST['langpage']) && $_REQUEST['langpage'] != "NULL"
				&& $langpage != $_REQUEST['langpage']) { // update the language

	$error = $multilinguallib->updateObjectLang($type, $objId, $_REQUEST['langpage']);
	if ($error)
		$smarty->assign('error', $error);
	else {
		$info['lang'] = $_REQUEST['langpage'];	
		$langpage = $_REQUEST['langpage'];
	}

	$fullLangName = $langmapping[$langpage][0];
	$smarty->assign('languageName', $fullLangName);
}
$smarty->assign('langpage', $langpage);

if ($type == "wiki page") {
  $tikilib->get_perm_object($name, 'wiki page', $info, true);	
	if ( !($tiki_p_edit == 'y' || ($prefs['wiki_creator_admin'] == 'y' && $user && $info['creator'] == $user) )) {
	  $smarty->assign('errortype', 401);
		$smarty->assign('msg', tra("You do not have permission to edit this page."));
		$smarty->display("error.tpl");
		die;
	}
}

$trads = $multilinguallib->getTranslations($type, $objId, $name, $langpage, true);

$usedLang = array();
foreach ( $trads as $trad )
	$usedLang[] = $trad['lang'];

$rawLangs = $tikilib->list_languages();
$languages = array();
foreach ( $rawLangs as $langInfo )
	if ( ! in_array($langInfo['value'], $usedLang) )
		$languages[] = $langInfo;
$smarty->assign_by_ref('languages', $languages);
if (count($languages) == 1) {
   $smarty->assign('only_one_language_left', 'y');
}

ask_ticket('edit-translation');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the template
$smarty->assign('mid', 'tiki-edit_translation.tpl');
$smarty->display("tiki.tpl");

function execute_module_translation() 
{ 
	global $smarty;
	$module_reference = array(
		'name' => 'translation',
		'params' => '',
		'position' => 'r',
		'ord' => 1,
		'moduleId' => 0
	);

	global $modlib; require_once 'lib/modules/modlib.php';	

	$out = $modlib->execute_module($module_reference);
	$smarty->assign('content_of_update_translation_section', $out);
}
