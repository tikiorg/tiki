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
$feature_lastChanges = $tikilib->get_preference("feature_lastChanges", 'y');
$feature_dump = $tikilib->get_preference("feature_dump", 'y');
$feature_ranking = $tikilib->get_preference("feature_ranking", 'y');
$feature_listPages = $tikilib->get_preference("feature_listPages", 'y');
$feature_history = $tikilib->get_preference("feature_history", 'y');
$feature_backlinks = $tikilib->get_preference("feature_backlinks", 'y');
$feature_likePages = $tikilib->get_preference("feature_likePages", 'y');
$feature_search = $tikilib->get_preference("feature_search", 'y');
$feature_userVersions = $tikilib->get_preference("feature_userVersions", 'y');
$feature_galleries = $tikilib->get_preference("feature_galleries",'y');
$smarty->assign_by_ref('feature_lastChanges',$feature_lastChanges);
$smarty->assign_by_ref('feature_dump',$feature_dump);
$smarty->assign_by_ref('feature_ranking',$feature_ranking);
$smarty->assign_by_ref('feature_listPages', $feature_listPages);
$smarty->assign_by_ref('feature_history', $feature_history);
$smarty->assign_by_ref('feature_backlinks', $feature_backlinks);
$smarty->assign_by_ref('feature_likePages', $feature_likePages);
$smarty->assign_by_ref('feature_search', $feature_search);
$smarty->assign_by_ref('feature_userVersions', $feature_userVersions);
$smarty->assign_by_ref('feature_galleries',$feature_galleries);

// Other preferences
$popupLinks = $tikilib->get_preference("popupLinks",'n');
$anonCanEdit = $tikilib->get_preference("anonCanEdit",'n');
$allowRegister = $tikilib->get_preference("allowRegister",'n');
$title = $tikilib->get_preference("title","");
$maxRecords = $tikilib->get_preference("maxRecords",10);
$smarty->assign_by_ref('style',$style);
$smarty->assign_by_ref('popupLinks',$popupLinks);
$smarty->assign_by_ref('anonCanEdit',$anonCanEdit);
$smarty->assign_by_ref('allowRegister',$allowRegister);
$smarty->assign('user',$user);
$smarty->assign('lock',false);
$smarty->assign_by_ref('title',$title);
$smarty->assign_by_ref('maxRecords',$maxRecords);
include_once("tiki-modules.php");
?>