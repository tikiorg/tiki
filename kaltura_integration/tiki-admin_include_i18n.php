<?php

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

// Get list of available languages
$languages = array();
$languages = $tikilib->list_languages(false,null,true);
$smarty->assign_by_ref("languages", $languages);
	
if (isset($_REQUEST["i18nsetup"])) {
ask_ticket('admin-inc-i18n');

    $pref_toggles = array(
		"feature_multilingual",
		'feature_translation',
		'feature_urgent_translation',
		"feature_multilingual_structures",
		"feature_best_language",
		'feature_sync_language',
        "feature_detect_language",
		"change_language",
		"quantify_changes",
        "lang_use_db",
		"record_untranslated",
		"feature_babelfish",
		"feature_babelfish_logo",
		'show_available_translations',
		'feature_multilingual_one_page',
    );


    foreach ($pref_toggles as $toggle) {
        simple_set_toggle ($toggle);
    }

	simple_set_value( 'language' );
	simple_set_value( 'available_languages', '', true );
}
if (!empty($_REQUEST['custom']) && !empty($_REQUEST['custom_lang'])) {
	ask_ticket('admin-inc-i18n');
	$custom_file = 'lang/'.$_REQUEST['custom_lang'].'/';
	if (!empty($tikidomain)) $custom_file .= "$tikidomain/";
	$custom_file .= "custom.php";
	$custom_translation = file_get_contents($custom_file);
	if (empty($custom_translation)) {
		$custom_translation = file_get_contents('lang/fr/custom.php_example');
	}
	$smarty->assign_by_ref('custom_translation', $custom_translation);
	$smarty->assign_by_ref('custom_lang', $_REQUEST['custom_lang']);
}
if (!empty($_REQUEST['custom_save']) && !empty($_REQUEST['custom_lang'])) {
	ask_ticket('admin-inc-i18n');
	$ok = false;
	foreach ($languages as $l) {
		if ($l['value'] == $_REQUEST['custom_lang']) {
			$ok = true;
			break;
		}
	}
	if (!$ok) {
		$smarty->assign('custom_error', 'param');
	} elseif (eval(str_replace(array('<?php','?>'), '', $_REQUEST['custom_translation'])) === false) {
		$smarty->assign_by_ref('custom_lang', $_REQUEST['custom_lang']);
		$smarty->assign_by_ref('custom_translation', $_REQUEST['custom_translation']);
		$smarty->assign('custom_error', 'parse');
	} else {
		$custom_file = 'lang/'.$_REQUEST['custom_lang'].'/';
		if (!empty($tikidomain)) $custom_file .= "$tikidomain/";
		$custom_file .= "custom.php";
		$smarty->assign('custom_file', $custom_file);
		if (!($fp = fopen($custom_file, 'w+'))) {
			$smarty->assign('custom_error', 'file');
		} else {
			if (!fwrite($fp, $_REQUEST['custom_translation'])) {
				$smarty->assign('custom_error', 'file');
			}
			fclose($fp);
			global $cachelib; include_once('lib/cache/cachelib.php');
			$cachelib->erase_dir_content("templates_c/$tikidomain");
		}
	}
}
