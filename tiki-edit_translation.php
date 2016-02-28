<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('tiki-setup.php');

$multilinguallib = TikiLib::lib('multilingual');
include_once('modules/mod-func-translation.php');

require_once('lib/debug/Tracer.php');

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

$langLib = TikiLib::lib('language');
$rawLangs = $langLib->list_languages();
$languages = array();
foreach ( $rawLangs as $langInfo )
	if ( ! in_array($langInfo['value'], $usedLang) )
		$languages[] = $langInfo;
$smarty->assign_by_ref('languages', $languages);
if (count($languages) == 1) {
   $smarty->assign('only_one_language_left', 'y');
}

if(isset($_REQUEST['target_lang'])){
	smarty_assign_default_target_lang($langpage, $_REQUEST['target_lang'], $trads, $prefs['read_language']);
}

smarty_assign_translation_name();

ask_ticket('edit-translation');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the template
$smarty->assign('mid', 'tiki-edit_translation.tpl');
$smarty->display("tiki.tpl");

function execute_module_translation() 
{ 
	$smarty = TikiLib::lib('smarty');
	$module_reference = array(
		'name' => 'translation',
		'params' => array('show_language' => 'n'),
		'position' => 'r',
		'ord' => 1,
		'moduleId' => 0
	);

	$modlib = TikiLib::lib('mod');

	$out = $modlib->execute_module($module_reference);
	$smarty->assign('content_of_update_translation_section', $out);
}

function smarty_assign_default_target_lang($src_lang, $targ_lang_requested, $existing_translations, $user_langs)
{
    global $tracer;
	$multilinguallib = TikiLib::lib('multilingual');
	$smarty = TikiLib::lib('smarty');

    $default_target_lang = $targ_lang_requested;
    if (! isset($default_target_lang))
    {


        $collect_lang_callback = function($translation) {return $translation['lang'];};
        $langs_already_translated = array_map($collect_lang_callback, $existing_translations);
        $default_target_lang = $multilinguallib->defaultTargetLanguageForNewTranslation($src_lang, $langs_already_translated, $user_langs);

    }

    $smarty->assign('default_target_lang', $default_target_lang);
}

function smarty_assign_translation_name()
{
    $smarty = TikiLib::lib('smarty');

    $translation_name = '';
    if (isset($_REQUEST['translation_name']))
    {
        $translation_name = $_REQUEST['translation_name'];
    }
    $smarty->assign('translation_name', $translation_name);
}
