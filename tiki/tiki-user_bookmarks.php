<?php
// Initialization
require_once('tiki-setup.php');
include_once('lib/bookmarks/bookmarklib.php');

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
  $path = $bookmarklib->get_folder_path($_REQUEST["parentId"],$user);
  $p_info = $bookmarklib->get_folder($_REQUEST["parentId"],$user);
  $father = $p_info["parentId"];
} else {
  $path = tra("TOP");
  $father = 0;
}
$smarty->assign('parentId',$_REQUEST["parentId"]);
$smarty->assign('path',$path);

//chekck for edit folder
if(isset($_REQUEST["editfolder"])) {
  $folder_info = $bookmarklib->get_folder($_REQUEST["editfolder"],$user);
} else {
  $folder_info["name"]='';
  $_REQUEST["editfolder"]=0;
}
$smarty->assign('foldername',$folder_info["name"]);
$smarty->assign('editfolder',$_REQUEST["editfolder"]);

if(isset($_REQUEST["editurl"])) {
  $url_info = $bookmarklib->get_url($_REQUEST["editurl"]);
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
    $bookmarklib->update_folder($_REQUEST["editfolder"],$_REQUEST["foldername"],$user);
    $smarty->assign('editfolder',0);
    $smarty->assign('foldername','');
  } else {
    $bookmarklib->add_folder($_REQUEST["parentId"],$_REQUEST["foldername"],$user);
  }
}
if(isset($_REQUEST["removefolder"])) {
  $bookmarklib->remove_folder($_REQUEST["removefolder"],$user);
}

if(isset($_REQUEST["refreshurl"])) {
  $bookmarklib->refresh_url($_REQUEST["refreshurl"]);
}

if(isset($_REQUEST["addurl"])) {
    $urlid = $bookmarklib->replace_url($_REQUEST["editurl"],$_REQUEST["parentId"],$_REQUEST["urlname"],$_REQUEST["urlurl"],$user);
    if($_REQUEST["editurl"]==0 && $tiki_p_cache_bookmarks=='y') {
      $bookmarklib->refresh_url($urlid);
    }
    $smarty->assign('editurl',0);
    $smarty->assign('urlname','');
    $smarty->assign('urlurl','');
}
if(isset($_REQUEST["removeurl"])) {
  $bookmarklib->remove_url($_REQUEST["removeurl"],$user);
}


$urls = $bookmarklib->list_folder($_REQUEST["parentId"],0,-1,'name_asc','',$user);
$smarty->assign('urls',$urls["data"]);
$folders = $bookmarklib->get_child_folders($_REQUEST["parentId"],$user);
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
