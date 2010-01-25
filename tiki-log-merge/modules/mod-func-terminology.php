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
			'root_category' => array(
				'name' => tra('Root category'),
				'description' => tra('All terms will automatically be put in that category. '.
				                     'Note that the category must already exist. '.
				                     'Defaults to \'Term\'')
			),
		)
	);
}

function module_terminology( $mod_reference, $module_params ) {
	global $smarty, $prefs;
	if ($prefs['feature_multilingual'] != 'y') {
		return;
	}
	
	init_from_parameters($module_params);
	
	global $multilinguallib; include_once('lib/multilingual/multilinguallib.php');
	
	$search_terms_in_lang = $multilinguallib->currentTermSearchLanguage();
	$smarty->assign('search_terms_in_lang', $search_terms_in_lang);

	$userLanguagesInfo = $multilinguallib->preferredLangsInfo();
	$smarty->assign('user_languages', $userLanguagesInfo);

	$smarty->assign('create_new_pages_using_template_name', 'Term Template');
}


function init_from_parameters($module_params) {
	global $smarty, $categlib;

	$root_category = 'Term';
	if (isset($module_params['root_category']) && $module_params['root_category'] != '') {
		$root_category = $module_params['root_category'];
	}

	include_once('lib/categories/categlib.php');
	$root_category_id = $categlib->get_category_id($root_category);

	if ($root_category_id == null) {
		$root_category_id = '';
	}
	
	$smarty->assign( 'term_root_category_id', $root_category_id);
}
