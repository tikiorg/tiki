<?php
require_once("setup.php");
require_once("lib/tikilib.php");
require_once("lib/userslib.php");

$userlib = new UsersLib($dbTiki);
if(isset($_SESSION["user"])) {
  $user = $_SESSION["user"];  
} else {
  $user = false;
}

$allperms = $userlib->get_permissions(0,-1,'permName_desc','','tiki');
$allperms = $allperms["data"];
foreach($allperms as $vperm) {
  $perm=$vperm["permName"];
  if($user != 'admin') {
    $$perm = 'n';
    $smarty->assign("$perm",'n');  
  } else {
    $$perm = 'y';
    $smarty->assign("$perm",'y');    
  }
}

// Permissions
// Get group permissions here
$perms = $userlib->get_user_permissions($user);
foreach($perms as $perm) {
  //print("Asignando permiso global : $perm<br/>");
  $smarty->assign("$perm",'y');  
  $$perm='y';
}


// Constants for permissions here
define('TIKI_PAGE_RESOURCE','tiki-page');
define('TIKI_P_EDIT','tiki_p_edit');
define('TIKI_P_VIEW','tiki_p_view');


$appname="tiki";
if(!isset($_SESSION["appname"])) {
  session_register("appname");
}
$smarty->assign("appname","tiki");


$tikilib = new TikiLib($dbTiki);
if(isset($GLOBALS["PHPSESSID"])) {
  $tikilib->update_session($GLOBALS["PHPSESSID"]);
}


$language = $tikilib->get_preference("language", 'en');
$smarty->assign('language',$language);
global $lang;
include_once('lang/'.$language.'/language.php');
function tra($content)
{
    global $lang;
    if ($content) {
      if(isset($lang[$content])) {
        return $lang[$content];  
      } else {
        return $content;        
      }
    }
}


// Check for FEATURES
$style = $tikilib->get_preference("style", 'default.css');
if($user) {
  $user_style = $tikilib->get_user_preference($user,'theme','');
  if($user_style) {
    $style = $user_style;
  }
}



$feature_lastChanges = $tikilib->get_preference("feature_lastChanges", 'y');
$feature_dump = $tikilib->get_preference("feature_dump", 'y');
$feature_ranking = $tikilib->get_preference("feature_ranking", 'y');
$feature_listPages = $tikilib->get_preference("feature_listPages", 'y');
$feature_history = $tikilib->get_preference("feature_history", 'y');
$feature_backlinks = $tikilib->get_preference("feature_backlinks", 'y');
$feature_likePages = $tikilib->get_preference("feature_likePages", 'y');
$feature_search = $tikilib->get_preference("feature_search", 'y');
$feature_sandbox = $tikilib->get_preference("feature_sandbox", 'y');
$feature_userPreferences = $tikilib->get_preference("feature_userPreferences", 'n');
$feature_userVersions = $tikilib->get_preference("feature_userVersions", 'y');
$feature_galleries = $tikilib->get_preference("feature_galleries",'y');
$feature_featuredLinks = $tikilib->get_preference("feature_featuredLinks",'y');
$feature_hotwords = $tikilib->get_preference("feature_hotwords",'y');
$smarty->assign_by_ref('feature_hotwords',$feature_hotwords);
$smarty->assign_by_ref('feature_lastChanges',$feature_lastChanges);
$smarty->assign_by_ref('feature_dump',$feature_dump);
$smarty->assign_by_ref('feature_ranking',$feature_ranking);
$smarty->assign_by_ref('feature_listPages', $feature_listPages);
$smarty->assign_by_ref('feature_history', $feature_history);
$smarty->assign_by_ref('feature_backlinks', $feature_backlinks);
$smarty->assign_by_ref('feature_likePages', $feature_likePages);
$smarty->assign_by_ref('feature_search', $feature_search);
$smarty->assign_by_ref('feature_sandbox', $feature_sandbox);
$smarty->assign_by_ref('feature_userPreferences', $feature_userPreferences);
$smarty->assign_by_ref('feature_userVersions', $feature_userVersions);
$smarty->assign_by_ref('feature_galleries',$feature_galleries);
$smarty->assign_by_ref('feature_featuredLinks',$feature_featuredLinks);
// Other preferences
$popupLinks = $tikilib->get_preference("popupLinks",'n');
$anonCanEdit = $tikilib->get_preference("anonCanEdit",'n');
$modallgroups = $tikilib->get_preference("modallgroups",'y');

$cachepages = $tikilib->get_preference("cachepages",'y');
$cacheimages = $tikilib->get_preference("cacheimages",'y');

$allowRegister = $tikilib->get_preference("allowRegister",'n');
$title = $tikilib->get_preference("title","");
$maxRecords = $tikilib->get_preference("maxRecords",10);
$maxArticles = $tikilib->get_preference("maxArticles",10);
$smarty->assign_by_ref('maxArticles',$maxArticles);
$smarty->assign_by_ref('style',$style);
$smarty->assign_by_ref('popupLinks',$popupLinks);
$smarty->assign_by_ref('modallgroups',$modallgroups);
$smarty->assign_by_ref('anonCanEdit',$anonCanEdit);
$smarty->assign_by_ref('allowRegister',$allowRegister);

$smarty->assign_by_ref('cachepages',$cachepages);
$smarty->assign_by_ref('cacheimages',$cacheimages);

$smarty->assign('user',$user);
$smarty->assign('lock',false);
$smarty->assign_by_ref('title',$title);
$smarty->assign_by_ref('maxRecords',$maxRecords);
include_once("tiki-modules.php");
?>