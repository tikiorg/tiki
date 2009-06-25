<?php
// 
// Support for multilingual terminology module
//

global $multilinguallib, $smarty, $tikilib;

include_once('lib/multilingual/multilinguallib.php');
include_once('lib/tikilib.php');
require_once 'lib/profilelib/profilelib.php';
require_once 'lib/profilelib/installlib.php';
require_once 'lib/profilelib/listlib.php';

make_sure_terminology_profil_was_installed();

$search_terms_in_lang = $multilinguallib->currentSearchLanguage(true);
$smarty->assign('search_terms_in_lang', $search_terms_in_lang);

$userLanguagesInfo = $multilinguallib->preferedLangsInfo();
$smarty->assign('user_languages', $userLanguagesInfo);

$smarty->assign('create_new_pages_using_template_name', 'Term Template');


function make_sure_terminology_profil_was_installed() {
	global $smarty;
	$profile_installer = new Tiki_Profile_Installer;
	$profile = Tiki_Profile::fromNames('profiles.tikiwiki.org', 'Collaborative_Multilingual_Terminology');
	if ($profile_installer->isInstalled($profile)) {
		$smarty->assign('terminology_profile_was_installed', 'y');
	} else  {
		$smarty->assign('terminology_profile_was_installed', 'n');
	}
}
