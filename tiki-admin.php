<?php
// Initialization
require_once('tiki-setup.php');

// Only an admin can use this script
if($user != 'admin') {
  if($tiki_p_admin != 'y') {
    $smarty->assign('msg',tra("You dont have permission to use this feature"));
    $smarty->display('error.tpl');
    die;
  }
}

if((!isset($_SESSION["user"])) || ($_SESSION["user"]!='admin')) {
  header("location: tiki-index.php");
  die; 
}

// Change preferences
if(isset($_REQUEST["prefs"])) {
  if(isset($_REQUEST["style"])) {
    $tikilib->set_preference("style",$_REQUEST["style"]); 
    $smarty->assign_by_ref('style',$_REQUEST["style"]);
  }
  if(isset($_REQUEST["language"])) {
    $tikilib->set_preference("language",$_REQUEST["language"]); 
    $smarty->assign_by_ref('language',$_REQUEST["language"]);
  }
  if(isset($_REQUEST["anonCanEdit"]) && $_REQUEST["anonCanEdit"]=="on") {
    $tikilib->set_preference("anonCanEdit",'y'); 
  } else {
    $tikilib->set_preference("anonCanEdit",'n');
  }
  if(isset($_REQUEST["popupLinks"]) && $_REQUEST["popupLinks"]=="on") {
    $tikilib->set_preference("popupLinks",'y'); 
  } else {
    $tikilib->set_preference("popupLinks",'n');
  }
  if(isset($_REQUEST["allowRegister"]) && $_REQUEST["allowRegister"]=="on") {
    $tikilib->set_preference("allowRegister",'y'); 
  } else {
    $tikilib->set_preference("allowRegister",'n');
  }
  $tikilib->set_preference("maxRecords",$_REQUEST["maxRecords"]);
  $tikilib->set_preference("maxVersions",$_REQUEST["maxVersions"]);
  $tikilib->set_preference("title",$_REQUEST["title"]);
}

if(isset($_REQUEST["features"])) {
  if(isset($_REQUEST["feature_lastChanges"]) && $_REQUEST["feature_lastChanges"]=="on") {
    $tikilib->set_preference("feature_lastChanges",'y'); 
    $smarty->assign("feature_lastChanges",'y');
  } else {
    $tikilib->set_preference("feature_lastChanges",'n');
    $smarty->assign("feature_lastChanges",'n');
  }
  
  if(isset($_REQUEST["feature_dump"]) && $_REQUEST["feature_dump"]=="on") {
    $tikilib->set_preference("feature_dump",'y'); 
    $smarty->assign("feature_dump",'y');
  } else {
    $tikilib->set_preference("feature_dump",'n');
    $smarty->assign("feature_dump",'n');
  }
  
  if(isset($_REQUEST["feature_ranking"]) && $_REQUEST["feature_ranking"]=="on") {
    $tikilib->set_preference("feature_ranking",'y'); 
    $smarty->assign("feature_ranking",'y');
  } else {
    $tikilib->set_preference("feature_ranking",'n');
    $smarty->assign("feature_ranking",'n');
  }
  
  if(isset($_REQUEST["feature_listPages"]) && $_REQUEST["feature_listPages"]=="on") {
    $tikilib->set_preference("feature_listPages",'y'); 
    $smarty->assign("feature_listPages",'y');
  } else {
    $tikilib->set_preference("feature_listPages",'n');
    $smarty->assign("feature_listPages",'n');
  }
  
  if(isset($_REQUEST["feature_history"]) && $_REQUEST["feature_history"]=="on") {
    $tikilib->set_preference("feature_history",'y'); 
    $smarty->assign("feature_history",'y');
  } else {
    $tikilib->set_preference("feature_history",'n');
    $smarty->assign("feature_history",'n');
  }
  
  if(isset($_REQUEST["feature_backlinks"]) && $_REQUEST["feature_backlinks"]=="on") {
    $tikilib->set_preference("feature_backlinks",'y'); 
    $smarty->assign("feature_backlinks",'y');
  } else {
    $tikilib->set_preference("feature_backlinks",'n');
    $smarty->assign("feature_backlinks",'n');
  }
  
  if(isset($_REQUEST["feature_likePages"]) && $_REQUEST["feature_likePages"]=="on") {
    $tikilib->set_preference("feature_likePages",'y'); 
    $smarty->assign("feature_likePages",'y');
  } else {
    $tikilib->set_preference("feature_likePages",'n');
    $smarty->assign("feature_likePages",'n');
  }
  
  if(isset($_REQUEST["feature_search"]) && $_REQUEST["feature_search"]=="on") {
    $tikilib->set_preference("feature_search",'y'); 
    $smarty->assign("feature_search",'y');
  } else {
    $tikilib->set_preference("feature_search",'n');
    $smarty->assign("feature_search",'n');
  }

  if(isset($_REQUEST["feature_galleries"]) && $_REQUEST["feature_galleries"]=="on") {
    $tikilib->set_preference("feature_galleries",'y'); 
    $smarty->assign("feature_galleries",'y');
  } else {
    $tikilib->set_preference("feature_galleries",'n');
    $smarty->assign("feature_galleries",'n');
  }

  
  if(isset($_REQUEST["feature_userVersions"]) && $_REQUEST["feature_userVersions"]=="on") {
    $tikilib->set_preference("feature_userVersions",'y'); 
    $smarty->assign("feature_userVersions",'y');
  } else {
    $tikilib->set_preference("feature_userVersions",'n');
    $smarty->assign("feature_userVersions",'n');
  }
}

if(isset($_REQUEST["createtag"])) {
  // Check existance
  if($tikilib->tag_exists($_REQUEST["tagname"])) {
      $smarty->assign('msg',tra("Tag already exists"));
      $smarty->display('error.tpl');
      die;  
  }
  $tikilib->create_tag($_REQUEST["tagname"]);  
}
if(isset($_REQUEST["restoretag"])) {
  // Check existance
  if(!$tikilib->tag_exists($_REQUEST["tagname"])) {
      $smarty->assign('msg',tra("Tag not found"));
      $smarty->display('error.tpl');
      die;    
  }
  $tikilib->restore_tag($_REQUEST["tagname"]);  
}
if(isset($_REQUEST["removetag"])) {
  // Check existance
  $tikilib->remove_tag($_REQUEST["tagname"]);  
}


if(isset($_REQUEST["newadminpass"])) {
  if($_REQUEST["adminpass"] <> $_REQUEST["again"]) {
     $smarty->assign('msg',tra("The passwords dont match"));
     $smarty->display('error.tpl');
     die;    
  }
  $userlib->set_admin_pass($_REQUEST["adminpass"]);
}

if(isset($_REQUEST["dump"])) {
  include("lib/tar.class.php");
  error_reporting(E_ERROR|E_WARNING);
  $tikilib->dump(); 
}

$styles=Array();
$h=opendir("styles/");
while($file=readdir($h)) {
  if(strstr($file,"css")) {
    $styles[]=$file;
  }
}
closedir($h);
$smarty->assign_by_ref('styles',$styles);

$languages=Array();
$h=opendir("lang/");
while($file=readdir($h)) {
  if($file!='.' && $file!='..' && is_dir('lang/'.$file)) {
    $languages[]=$file;
  }
}
closedir($h);
$smarty->assign_by_ref('languages',$languages);

$tags = $tikilib->get_tags();
$smarty->assign_by_ref("tags",$tags);

// Preferences to load
// anonCanEdit
// maxVersions
$anonCanEdit = $tikilib->get_preference("anonCanEdit",'n');
$allowRegister = $tikilib->get_preference("allowRegister",'n');
$maxVersions = $tikilib->get_preference("maxVersions", 20);
$maxRecords = $tikilib->get_preference("maxRecords",10);
$title = $tikilib->get_preference("title","");
$popupLinks = $tikilib->get_preference("popupLinks",'n');
$smarty->assign_by_ref('popupLinks',$popupLinks);
$smarty->assign_by_ref('anonCanEdit',$anonCanEdit);
$smarty->assign_by_ref('allowRegister',$allowRegister);
$smarty->assign_by_ref('maxVersions',$maxVersions);
$smarty->assign_by_ref('title',$title);
$smarty->assign_by_ref('maxRecords',$maxRecords);
// Display the template
$smarty->assign('mid','tiki-admin.tpl');
$smarty->display('tiki.tpl');
?>