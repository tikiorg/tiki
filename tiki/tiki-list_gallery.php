<?php
// Initialization
require_once('tiki-setup.php');

if($feature_galleries != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display('error.tpl');
  die;  
}

if($_REQUEST["galleryId"]==0 && $tiki_p_admin != 'y') {
  $smarty->assign('msg',tra("Permission denied you cannot access this gallery"));
  $smarty->display('error.tpl');
  die;  
}

/*
if($tiki_p_upload_images != 'y') {
  $smarty->assign('msg',tra("Permission denied you cannot upload images"));
  $smarty->display('error.tpl');
  die;  
}
*/

if(!isset($_REQUEST["galleryId"])) {
  $smarty->assign('msg',tra("No gallery indicated"));
  $smarty->display('error.tpl');
  die;
}

if($_REQUEST["galleryId"]!=0) {
// To browse the gallery the user has to be admin, the owner or the gallery has to be public
$gal_info = $tikilib->get_gallery($_REQUEST["galleryId"]);
//$smarty->assign_by_ref('theme',$gal_info["theme"]);
//$smarty->assign('use_theme','y');
/*
if($user!='admin' && $user!=$gal_info["user"] && $gal_info["public"]!='y') {
  $smarty->assign('msg',tra("Permission denied you cannot browse this gallery"));
  $smarty->display('error.tpl');
  die;  
}
*/
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

$tikilib->add_gallery_hit($_REQUEST["galleryId"]);

if(isset($_REQUEST["remove"])) {
  // To remove an image the user must be the owner or admin
  if($user!='admin' && $user!=$gal_info["user"]) {
    $smarty->assign('msg',tra("Permission denied you cannot remove images from this gallery"));
    $smarty->display('error.tpl');
    die;  
  }
  $tikilib->remove_image($_REQUEST["remove"]);
}

if(isset($_REQUEST["rebuild"])) {
 // To remove an image the user must be the owner or admin
  if($user!='admin' && $user!=$gal_info["user"]) {
    $smarty->assign('msg',tra("Permission denied you cannot remove images from this gallery"));
    $smarty->display('error.tpl');
    die;  
  }
  $tikilib->rebuild_thumbnails($_REQUEST["rebuild"]);
}

$smarty->assign('system','n');
if($_REQUEST["galleryId"]==0) {
  $info["thumbSizeX"]=100;
  $info["thumbSizeY"]=100;
  $info["galleryId"]=0;
  $info["user"]='admin';
  $info["name"]='System';
  $info["public"]='y';
  $info["description"]='System Gallery';
  $smarty->assign('system','y');
} else {
  $info = $tikilib->get_gallery($_REQUEST["galleryId"]);
}

if(!isset($info["maxRows"])) $info["maxRows"]=10;
if(!isset($info["rowImages"])) $info["rowImages"]=5;
if($info["maxRows"]==0) $info["maxRows"]=10;
if($info["rowImages"]==0) $info["rowImages"]=6;
$maxRecords = $info["maxRows"] * $info["rowImages"];
$smarty->assign_by_ref('rowImages',$info["rowImages"]);
$smarty->assign('rowImages2',$info["rowImages"]-1);
$smarty->assign_by_ref('thx',$info["thumbSizeX"]);
$smarty->assign_by_ref('thy',$info["thumbSizeY"]);
$smarty->assign_by_ref('name',$info["name"]);
$smarty->assign_by_ref('description',$info["description"]);


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

$images = $tikilib->get_images($offset,$maxRecords,$sort_mode,$find,$_REQUEST["galleryId"]);
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
$smarty->assign('mid','tiki-list_gallery.tpl');
$smarty->display('tiki.tpl');
?>