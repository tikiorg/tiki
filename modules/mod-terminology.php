<?php
// 
// Support for multilingual terminology module
//

global $multilinguallib, $smarty, $tikilib, $prefs;

include_once('lib/multilingual/multilinguallib.php');
include_once('lib/tikilib.php');
require_once 'lib/profilelib/profilelib.php';
require_once 'lib/profilelib/installlib.php';
require_once 'lib/profilelib/listlib.php';

if( $prefs['terminology_profile_installed'] == 'y' ) {

	$search_terms_in_lang = $multilinguallib->currentSearchLanguage(true);
	$smarty->assign('search_terms_in_lang', $search_terms_in_lang);

	$userLanguagesInfo = $multilinguallib->preferedLangsInfo();
	$smarty->assign('user_languages', $userLanguagesInfo);

	$smarty->assign('create_new_pages_using_template_name', 'Term Template');
}

