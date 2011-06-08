<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
require_once('lib/language/Language.php');
require_once('lib/language/LanguageTranslations.php');

$access->check_feature('lang_use_db');
$access->check_permission('tiki_p_edit_languages');

// Get available languages
$languages = $tikilib->list_languages();
$smarty->assign_by_ref('languages', $languages);

// check if is possible to write to lang/
// TODO: check if each language file is writable instead of the whole lang/ dir
if (is_writable('lang/')) {
	$smarty->assign('langIsWritable', true);
} else {
	$smarty->assign('langIsWritable', false);
}

// preserving variables
if (isset($_REQUEST["edit_language"])) {
	$smarty->assign('edit_language', $_REQUEST["edit_language"]);
	$edit_language = $_REQUEST["edit_language"];
} else {
	$smarty->assign('edit_language', $prefs['language']);
	$edit_language = $prefs['language'];
}

$translations = new LanguageTranslations($edit_language);

if (!isset($_REQUEST["action"])) {
	$_REQUEST['action'] = 'edit_tran_sw';
}
$smarty->assign('action', $_REQUEST["action"]);

if (isset($_REQUEST['only_db_translations']) && $_REQUEST['only_db_translations'] != 'n') {
	$smarty->assign('only_db_translations', 'y');
} else {
	$smarty->assign('only_db_translations', 'n');
}

if (isset($_REQUEST['only_db_untranslated']) && $_REQUEST['only_db_untranslated'] != 'n') {
	$smarty->assign('only_db_untranslated', 'y');
} else {
	$smarty->assign('only_db_untranslated', 'n');
}

// Adding strings
if (isset($_REQUEST["add_tran"])) {
	check_ticket('edit-languages');
	$add_tran_source = $_REQUEST["add_tran_source"];
	$add_tran_tran = $_REQUEST["add_tran_tran"];

	if (strlen($add_tran_source) != 0 && strlen($add_tran_tran) != 0) {
		$add_tran_source = strip_tags($add_tran_source);
		$add_tran_tran = strip_tags($add_tran_tran);

		$translations->updateTrans($add_tran_source, $add_tran_tran);
	}
}

// Delete all db translations
if (isset($_REQUEST['delete_all']) && $tiki_p_admin) {
	$translations->deleteTranslations();
}

//Selection for untranslated Strings and edit translations
if (isset($_REQUEST["action"])) {
	$action = $_REQUEST["action"];
} else {
	$action = "";
}

if ($action == "edit_rec_sw" || $action == "edit_tran_sw") {
	check_ticket('edit-languages');
	
	$offset = isset($_REQUEST["offset"]) ? $_REQUEST['offset'] : 0;
	$smarty->assign('offset', $offset);
	
	$maxRecords = (isset($_REQUEST['maxRecords']) && $_REQUEST['maxRecords'] > 0) ? $_REQUEST['maxRecords'] : $prefs['maxRecords'];
	$smarty->assign('maxRecords', $maxRecords);
	
	//check if user has translated something
	for ($i = 0; $i < $maxRecords; $i++) {
		// Handle edits in untranslated strings
		if (isset($_REQUEST["edit_tran_$i"]) || isset($_REQUEST['translate_all'])) {
			// Handle edits in edit translations
			if (strlen($_REQUEST["tran_$i"]) > 0 && strlen($_REQUEST["source_$i"]) > 0) {
				$translations->updateTrans($_REQUEST["source_$i"], $_REQUEST["tran_$i"]);
			}
		} elseif (isset($_REQUEST["del_tran_$i"])) {
			// Handle deletes here
			if (strlen($_REQUEST["source_$i"]) > 0) {
				$translations->deleteTranslation($_REQUEST["source_$i"]);
			}
		}
	} // end of for ...
	// for resetting untranslated
	if (isset($_REQUEST["tran_reset"])) {
		$translations->deleteAllUntranslated();
	}

	// update language array with new translations
	$query = "select `source`, `tran` from `tiki_language` where `lang`=?";
	$result = $tikilib->fetchAll($query, array($edit_language));

	foreach( $result as $row ) {
		${"lang_$edit_language"}[ $row['source'] ] = $row['tran'];
	}

	//Handle searches
	$find = '';

	if (isset($_REQUEST['find']) && strlen($_REQUEST['find']) > 0) {
		$find = $_REQUEST['find'];
		$smarty->assign('find', $find);
	}

	$sort_mode = "source_asc";

	$data = array();

	if ($action == "edit_rec_sw") {
		if (isset($_REQUEST['only_db_untranslated']) && $_REQUEST['only_db_untranslated'] != 'n') {
			// display only database stored untranslated strings
			$data = $translations->getDbUntranslated($maxRecords, $offset, $find);
		} else {
			// display all untranslated strings (language.php + db)
			$data = $translations->getAllUntranslated($maxRecords, $offset, $find);
		}
	} elseif ($action == "edit_tran_sw") {
		if (isset($_REQUEST['only_db_translations']) && $_REQUEST['only_db_translations'] != 'n') {
			// display only database stored translations
			$data = $translations->getDbTranslations($sort_mode, $maxRecords, $offset, $find);
		} else {
			// display all available translations (db + custom.php + language.php)
			$data = $translations->getAllTranslations($maxRecords, $offset, $find);
		}
	}

	$smarty->assign_by_ref('translations', $data['translations']);
	$smarty->assign('total', $data['total']);
	$smarty->assign('hasDbTranslations', $translations->hasDbTranslations);
}

if (isset($_REQUEST["exp_language"])) {
	$exp_language = $_REQUEST["exp_language"];
	$export_language = new LanguageTranslations($exp_language);
	$smarty->assign('exp_language', $exp_language);
} else {
	$smarty->assign('exp_language', $prefs['language']);
}

// Export
if (isset($_REQUEST['downloadFile'])) {
	check_ticket('edit-languages');
	$data = $export_language->createCustomFile();
	header ("Content-type: application/unknown");
	header ("Content-Disposition: inline; filename=language.php");
	header ("Content-encoding: UTF-8");
	echo $data;
	exit (0);
}

// Write to language.php
if (isset($_REQUEST['exportToLanguage']) && $tiki_p_admin == 'y') {
	try {
		$stats = $export_language->writeLanguageFile();
	} catch (Exception $e) {
		$smarty->assign('msg', $e->getMessage());
		$smarty->display('error.tpl');
		die;
	}

	$expmsg = sprintf(tra('Wrote %d new strings and updated %d to lang/%s/language.php'), $stats['new'], $stats['modif'], $export_language->lang);
	$smarty->assign('expmsg', $expmsg);
}

$db_languages = Language::getDbTranslatedLanguages();
$db_languages = $tikilib->format_language_list($db_languages);
$smarty->assign_by_ref('db_languages', $db_languages);

ask_ticket('edit-languages');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

$headerlib->add_cssfile('css/admin.css');
$headerlib->add_jsfile('lib/language/tiki-edit_languages.js');

$headtitle = tra('Edit languages');
$description = tra('Edit or export languages');
$crumbs[] = new Breadcrumb($headtitle, $description, '', '', '');
$headtitle = breadcrumb_buildHeadTitle($crumbs);
$smarty->assign('headtitle', $headtitle);
$smarty->assign('trail', $crumbs);

$smarty->assign('mid', 'tiki-edit_languages.tpl');
$smarty->display("tiki.tpl");
