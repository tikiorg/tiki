<?php
// Initialization
require_once('tiki-setup.php');
include_once('lib/directory/dirlib.php');

if($feature_directory != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

if($tiki_p_submit_link != 'y') {
  $smarty->assign('msg',tra("Permission denied"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

// If no parent category then the parent category is 0
if(!isset($_REQUEST["parent"])) $_REQUEST["parent"]=0;
$smarty->assign('parent',$_REQUEST["parent"]);
$all=0;
if($_REQUEST["parent"]==0) {
  $parent_name = 'Top';
  $all=1;
} else {
  $parent_info = $dirlib->dir_get_category($_REQUEST['parent']);
  $parent_name = $parent_info['name'];
}
$smarty->assign('parent_name',$parent_name);


if(isset($parent_info)&&$user) {
  if(in_array($parent_info['editorGroup'],$userlib->get_user_groups($user))) {
    $tiki_p_autosubmit_link = 'y';
    $smarty->assign('tiki_p_autosubmit_link','y'); 
  }
}


// Now get the path to the parent category
$path = $dirlib->dir_get_category_path_admin($_REQUEST["parent"]);
$smarty->assign_by_ref('path',$path);

// If no site is being edited set it to zero
$_REQUEST["siteId"]=0;
$smarty->assign('siteId',$_REQUEST["siteId"]);

// If we are editing an existing category then get the category information
// If not initialize the information to zero
if($_REQUEST["siteId"]) {
  $info = $dirlib->dir_get_site($_REQUEST["siteId"]);
} else {
  $info = Array();
  $info["name"]='';
  $info["description"]='';
  $info["url"]='';
  $info["country"]='United_States';
  $info["isValid"]='y';
}
$smarty->assign_by_ref('info',$info);

$smarty->assign('save','n');
// Replace (add or edit) a site
if(isset($_REQUEST["save"])) {
  $smarty->assign('save','y');
  if(empty($_REQUEST["name"])) {
    $smarty->assign('msg',tra("Must enter a name to add a site"));
    $smarty->display("styles/$style_base/error.tpl");
    die;  
  }
  if(empty($_REQUEST["url"])) {
    $smarty->assign('msg',tra("Must enter a url to add a site"));
    $smarty->display("styles/$style_base/error.tpl");
    die;  
  }
  if(substr($_REQUEST["url"],0,7)<>'http://') {
    $_REQUEST["url"]='http://'.$_REQUEST["url"];
  }
  
  if($dirlib->dir_url_exists($_REQUEST['url'])) {
    $smarty->assign('msg',tra("URL already added to the directory. Duplicate site?"));
    $smarty->display("styles/$style_base/error.tpl");
    die;  
  }
  
  if($directory_validate_urls == 'y') {
    @$fsh = fopen($_REQUEST['url'],'r');
    if(!$fsh) {
      $smarty->assign('msg',tra("URL cannot be accessed wrong URL or site is offline and cannot be added to the directory"));
      $smarty->display("styles/$style_base/error.tpl");
      die;  
    }
  }
  
  if(!isset($_REQUEST["siteCats"])||count($_REQUEST["siteCats"])==0) {
    $smarty->assign('msg',tra("Must select a category"));
    $smarty->display("styles/$style_base/error.tpl");
    die;  
  }
  if(isset($_REQUEST["isValid"])&&$_REQUEST["isValid"]=='on') $_REQUEST["isValid"]='y'; else $_REQUEST["isValid"]='n';
  
  if($tiki_p_autosubmit_link == 'y') {
    $_REQUEST["isValid"] = 'y';
  }
  
  $siteId=$dirlib->dir_replace_site($_REQUEST["siteId"],$_REQUEST["name"],$_REQUEST["description"], $_REQUEST["url"], $_REQUEST["country"], $_REQUEST["isValid"]);
  $dirlib->remove_site_from_categories($siteId);
  foreach($_REQUEST["siteCats"] as $acat) {
    $dirlib->dir_add_site_to_category($siteId,$acat);
  }
  $info = Array();
  $info["name"]=$_REQUEST['name'];
  $info["description"]=$_REQUEST['description'];
  $info["url"]=$_REQUEST['url'];
  $info["country"]=$_REQUEST['country'];
  $info["isValid"]='y';
  $smarty->assign('siteId',0);
}

// Listing: categories in the parent category
// Pagination resolution
if(!isset($_REQUEST["sort_mode"])) {  $sort_mode = 'created_desc'; } else {  $sort_mode = $_REQUEST["sort_mode"];} 
if(!isset($_REQUEST["offset"])) {  $offset = 0;} else {  $offset = $_REQUEST["offset"]; }
if(isset($_REQUEST["find"])) {  $find = $_REQUEST["find"];  } else {  $find = ''; }
$smarty->assign_by_ref('offset',$offset);
$smarty->assign_by_ref('sort_mode',$sort_mode);
$smarty->assign('find',$find);
// What are we paginating: items
if($all) {
  $items = $dirlib->dir_list_all_sites($offset,$maxRecords,$sort_mode,$find);
} else {
  $items = $dirlib->dir_list_sites($_REQUEST["parent"],$offset,$maxRecords,$sort_mode,$find,$isValid='');
}
$cant_pages = ceil($items["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages',$cant_pages);
$smarty->assign('actual_page',1+($offset/$maxRecords));
if($items["cant"] > ($offset+$maxRecords)) {  $smarty->assign('next_offset',$offset + $maxRecords);} else {  $smarty->assign('next_offset',-1); }
if($offset>0) {  $smarty->assign('prev_offset',$offset - $maxRecords);  } else {  $smarty->assign('prev_offset',-1); }
$smarty->assign_by_ref('items',$items["data"]);

$categs = $dirlib->dir_get_all_categories_accept_sites(0,-1,'name asc',$find,$_REQUEST["siteId"]);
$smarty->assign('categs',$categs);

$countries=Array();
$h=opendir("img/flags");
while($file=readdir($h)) {
  if(is_file('img/flags/'.$file)) {
    $name=explode('.',$file);
    $countries[]=$name[0];
  }
}
closedir($h);
$smarty->assign_by_ref('countries',$countries);

// Display the template
$smarty->assign('mid','tiki-directory_add_site.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>