<?php
// Initialization
require_once('tiki-setup.php');

/*
if($feature_file_galleries != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display('error.tpl');
  die;  
}
*/

if($_REQUEST["galleryId"]==0) {
  $smarty->assign('msg',tra("Unexistant gallery"));
  $smarty->display('error.tpl');
  die;  
}

if(!isset($_REQUEST["galleryId"])) {
  $smarty->assign('msg',tra("No gallery indicated"));
  $smarty->display('error.tpl');
  die;
}

if($_REQUEST["galleryId"]!=0) {
  $gal_info = $tikilib->get_file_gallery($_REQUEST["galleryId"]);
} else {
  $gal_info["galleryId"]=0;
  $gal_info["user"]='admin';
  $gal_info["name"]='System';
  $gal_info["public"]='y';
  $gal_info["description"]='System Gallery';
}

$smarty->assign_by_ref('owner',$gal_info["user"]);
$smarty->assign_by_ref('public',$gal_info["public"]);
$smarty->assign_by_ref('galleryId',$_REQUEST["galleryId"]);

$tikilib->add_file_gallery_hit($_REQUEST["galleryId"]);

if(isset($_REQUEST["remove"])) {
  // To remove an image the user must be the owner or admin
  if($tiki_p_admin_file_galleries != 'y' && (!$user || $user!=$gal_info["user"])) {
    $smarty->assign('msg',tra("Permission denied you cannot remove files from this gallery"));
    $smarty->display('error.tpl');
    die;  
  }
  $tikilib->remove_file($_REQUEST["remove"]);
}

if(!isset($gal_info["maxRows"])) $gal_info["maxRows"]=10;
if($gal_info["maxRows"]==0) $gal_info["maxRows"]=10;
$maxRecords = $gal_info["maxRows"];
$smarty->assign_by_ref('name',$gal_info["name"]);
$smarty->assign_by_ref('description',$gal_info["description"]);


if(!isset($_REQUEST["sort_mode"])) {
  $sort_mode = 'created_desc'; 
} else {
  $sort_mode = $_REQUEST["sort_mode"];
} 
$smarty->assign_by_ref('sort_mode',$sort_mode);

// If offset is set use it if not then use offset =0
// use the maxRecords php variable to set the limit
// if sortMode is not set then use lastModif_desc
if(!isset($_REQUEST["offset"])) {
  $offset = 0;
} else {
  $offset = $_REQUEST["offset"]; 
}
$smarty->assign_by_ref('offset',$offset);

if(isset($_REQUEST["find"])) {
  $find = $_REQUEST["find"];  
} else {
  $find = ''; 
}

$images = $tikilib->get_files($offset,$maxRecords,$sort_mode,$find,$_REQUEST["galleryId"]);
$cant_pages = ceil($images["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages',$cant_pages);
$smarty->assign('actual_page',1+($offset/$maxRecords));
if($images["cant"] > ($offset+$maxRecords)) {
  $smarty->assign('next_offset',$offset + $maxRecords);
} else {
  $smarty->assign('next_offset',-1); 
}
// If offset is > 0 then prev_offset
if($offset>0) {
  $smarty->assign('prev_offset',$offset - $maxRecords);  
} else {
  $smarty->assign('prev_offset',-1); 
}


$smarty->assign_by_ref('images',$images["data"]);

// Display the template
$smarty->assign('mid','tiki-list_file_gallery.tpl');
$smarty->display('tiki.tpl');
?>