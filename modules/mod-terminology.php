<?php
// 
// Support for multilingual terminology module
//

//print "-- mod-terminology.php: invoked<br>\n";

global $multilinguallib, $smarty, $tikilib;

include_once('lib/multilingual/multilinguallib.php');
include_once('lib/tikilib.php');

$search_terms_in_lang = $multilinguallib->currentSearchLanguage(true);
$smarty->assign('search_terms_in_lang', $search_terms_in_lang);

$userLanguagesInfo = $multilinguallib->preferedLangsInfo();
//print "-- mod-terminology: \$userLanguagesInfo="; var_dump($userLanguagesInfo); print "<br>\n";
$smarty->assign('user_languages', $userLanguagesInfo);


