<?php
// 
// Support for multilingual terminology module
//

//print "-- mod-terminology.php: invoked<br>\n";

global $multilinguallib, $smarty;

include_once('lib/multilingual/multilinguallib.php');

$lang = $multilinguallib->currentSearchLanguage(true);
$smarty->assign('search_terms_in_lang', $lang);

$userLanguages = $multilinguallib->preferedLangs();
$smarty->assign('user_languages', $userLanguages);


