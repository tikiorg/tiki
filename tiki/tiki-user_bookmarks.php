<?php
// Initialization
require_once('tiki-setup.php');

if($tiki_p_create_bookmarks != 'y') {
    $smarty->assign('msg',tra("You dont have permission to use this feature"));
    $smarty->display("styles/$style_base/error.tpl");
    die;
}


if(!$user) {
    $smarty->assign('msg',tra("You must log in to use this feature"));
    $smarty->display("styles/$style_base/error.tpl");
    die;
}

if($feature_user_bookmarks != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

if(!isset($_REQUEST["parentId"])) {
  $_REQUEST["parentId"]=0;
}

if($_REQUEST["parentId"]) {
  $path = $tikilib->get_folder_path($_REQUEST["parentId"],$user);
  $p_info = $tikilib->get_folder($_REQUEST["parentId"],$user);
  $father = $p_info["parentId"];
} else {
  $path = tra("TOP");
  $father = 0;
}
$smarty->assign('parentId',$_REQUEST["parentId"]);
$smarty->assign('path',$path);

//chekck for edit folder
if(isset($_REQUEST["editfolder"])) {
  $folder_info = $tikilib->get_folder($_REQUEST["editfolder"],$user);
} else {
  $folder_info["name"]='';
  $_REQUEST["editfolder"]=0;
}
$smarty->assign('foldername',$folder_info["name"]);
$smarty->assign('editfolder',$_REQUEST["editfolder"]);

if(isset($_REQUEST["editurl"])) {
  $url_info = $tikilib->get_url($_REQUEST["editurl"]);
} else {
  $url_info["name"]='';
  $url_info["url"]='';
  $_REQUEST["editurl"]=0;
}
$smarty->assign('urlname',$url_info["name"]);
$smarty->assign('urlurl',$url_info["url"]);
$smarty->assign('editurl',$_REQUEST["editurl"]);


// Create a folder inside the parentFolder here
if(isset($_REQUEST["addfolder"])) {
  if($_REQUEST["editfolder"]) {
    $tikilib->update_folder($_REQUEST["editfolder"],$_REQUEST["foldername"],$user);
    $smarty->assign('editfolder',0);
    $smarty->assign('foldername','');
  } else {
    $tikilib->add_folder($_REQUEST["parentId"],$_REQUEST["foldername"],$user);
  }
}
if(isset($_REQUEST["removefolder"])) {
  $tikilib->remove_folder($_REQUEST["removefolder"],$user);
}

if(isset($_REQUEST["refreshurl"])) {
  $tikilib->refresh_url($_REQUEST["refreshurl"]);
}

if(isset($_REQUEST["addurl"])) {
    $urlid = $tikilib->replace_url($_REQUEST["editurl"],$_REQUEST["parentId"],$_REQUEST["urlname"],$_REQUEST["urlurl"],$user);
    if($_REQUEST["editurl"]==0 && $tiki_p_cache_bookmarks=='y') {
      $tikilib->refresh_url($urlid);
    }
    $smarty->assign('editurl',0);
    $smarty->assign('urlname','');
    $smarty->assign('urlurl','');
}
if(isset($_REQUEST["removeurl"])) {
  $tikilib->remove_url($_REQUEST["removeurl"],$user);
}


$urls = $tikilib->list_folder($_REQUEST["parentId"],0,-1,'name_asc','',$user);
$smarty->assign('urls',$urls["data"]);
$folders = $tikilib->get_child_folders($_REQUEST["parentId"],$user);
$pf = Array(
  "name" => "..",
  "folderId" => $father,
  "parentId" => 0,
  "user" => $user
);
$pfs =Array($pf);
if($_REQUEST["parentId"]) {
  $folders = array_merge($pfs,$folders);
}
$smarty->assign('folders',$folders);

include_once('tiki-mytiki_shared.php');


// Display the template
$smarty->assign('mid','tiki-user_bookmarks.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>
