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

$userLanguagesWithNames = preferedLangsWithNames();

print "-- mod-terminology: \$userLanguagesWithNames="; var_dump($userLanguagesWithNames); print "<br>\n";
$smarty->assign('user_languages', $userLanguagesWithNames);

function preferedLangsWithNames() {
   global $multilinguallib, $tikilib;
   print "-- mod-terminology.preferedLangsWithNames: invoked<br>\n";

   // Get IDs of user's preferred languages
   $userLangIDs = $multilinguallib->preferedLangs();
   
   // Get information about ALL languages supported by Tiki
   $allLangsInfo = $tikilib->list_languages(false,null,true);

   // Create a map of language ID (ex: 'en') to language name (ex: 'English') 
   // for ALL languages
   $langIDs2Names = array();
   foreach ($allLangsInfo as $someLangInfo){
      $langIDs2Names[$someLangInfo['value']] = $someLangInfo['name'];
   }

   // Create list of language IDs AND names for user's prefered
   // languages. 
   $userLangsWithNames = array();
   foreach ($userLangIDs as $someUserLangID) {
      $userLangsWithNames[$someUserLangID] = $langIDs2Names[$someUserLangID];
   }
   return $userLangsWithNames;
       
}

