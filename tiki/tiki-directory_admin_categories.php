<?php
// Initialization
require_once('tiki-setup.php');
include_once('lib/directory/dirlib.php');

if($feature_directory != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}



// If no parent category then the parent category is 0
if(!isset($_REQUEST["parent"])) $_REQUEST["parent"]=0;
$smarty->assign('parent',$_REQUEST["parent"]);

if($_REQUEST["parent"]==0) {
  $parent_name = 'Top';
} else {
  $parent_info = $dirlib->dir_get_category($_REQUEST['parent']);
  $parent_name = $parent_info['name'];
}
$smarty->assign('parent_name',$parent_name);


// Now get the path to the parent category
$path = $dirlib->dir_get_category_path_admin($_REQUEST["parent"]);
$smarty->assign_by_ref('path',$path);

// If no category is being edited set it to zero
if(!isset($_REQUEST["categId"])) $_REQUEST["categId"]=0;
$smarty->assign('categId',$_REQUEST["categId"]);

// If we are editing an existing category then get the category information
// If not initialize the information to zero
if($_REQUEST["categId"]) {
  $info = $dirlib->dir_get_category($_REQUEST["categId"]);
} else {
  $info = Array();
  $info["name"]='';
  $info["childrenType"]='c';
  $info["viewableChildren"]=3;
  $info["allowSites"]='y';
  $info["showCount"]='y';
  $info["editorGroup"]='admin';
}
$smarty->assign_by_ref('info',$info);

// Remove a category
if(isset($_REQUEST["remove"])) {
  $dirlib->dir_remove_category($_REQUEST["remove"]);
}

// Replace (add or edit) a category
if(isset($_REQUEST["save"])) {
  if(isset($_REQUEST["allowSites"])&&$_REQUEST["allowSites"]=='on') $_REQUEST["allowSites"]='y'; else $_REQUEST["allowSites"]='n';
  if(isset($_REQUEST["showCount"])&&$_REQUEST["showCount"]=='on') $_REQUEST["showCount"]='y'; else $_REQUEST["showCount"]='n';
  $dirlib->dir_replace_category($_REQUEST["parent"],$_REQUEST["categId"], $_REQUEST["name"], $_REQUEST["description"], $_REQUEST["childrenType"], $_REQUEST["viewableChildren"], $_REQUEST["allowSites"], $_REQUEST["showCount"], $_REQUEST["editorGroup"]);
}

// Listing: categories in the parent category
// Pagination resolution
if(!isset($_REQUEST["sort_mode"])) {  $sort_mode = 'name_asc'; } else {  $sort_mode = $_REQUEST["sort_mode"];} 
if(!isset($_REQUEST["offset"])) {  $offset = 0;} else {  $offset = $_REQUEST["offset"]; }
if(isset($_REQUEST["find"])) {  $find = $_REQUEST["find"];  } else {  $find = ''; }
$smarty->assign_by_ref('offset',$offset);
$smarty->assign_by_ref('sort_mode',$sort_mode);
$smarty->assign('find',$find);
// What are we paginating: items
$items = $dirlib->dir_list_categories($_REQUEST["parent"],$offset,$maxRecords,$sort_mode,$find);
$cant_pages = ceil($items["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages',$cant_pages);
$smarty->assign('actual_page',1+($offset/$maxRecords));
if($items["cant"] > ($offset+$maxRecords)) {  $smarty->assign('next_offset',$offset + $maxRecords);} else {  $smarty->assign('next_offset',-1); }
if($offset>0) {  $smarty->assign('prev_offset',$offset - $maxRecords);  } else {  $smarty->assign('prev_offset',-1); }
$smarty->assign_by_ref('items',$items["data"]);

$groups = $userlib->get_groups(0,-1,'groupName_asc','');
$smarty->assign_by_ref('groups',$groups["data"]);

$categs = $dirlib->dir_get_all_categories(0,-1,'name asc',$find);
$smarty->assign('categs',$categs);

// Display the template
$smarty->assign('mid','tiki-directory_admin_categories.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>