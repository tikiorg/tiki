<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_terminology_info() {
	return array(
		'name' => tra('Terminology'),
		'description' => tra('Support for multilingual terminology'),
		'prefs' => array("terminology_profile_installed"),
		'params' => array(
		)
	);
}

function module_terminology( $mod_reference, $module_params ) {
	global $smarty, $prefs;
	if ($prefs['feature_multilingual'] != 'y') {
		return;
	}
	global $multilinguallib; include_once('lib/multilingual/multilinguallib.php');
	
	$search_terms_in_lang = $multilinguallib->currentSearchLanguage(true);
	$smarty->assign('search_terms_in_lang', $search_terms_in_lang);

	$userLanguagesInfo = $multilinguallib->preferredLangsInfo();
	$smarty->assign('user_languages', $userLanguagesInfo);

	$smarty->assign('create_new_pages_using_template_name', 'Term Template');
}
