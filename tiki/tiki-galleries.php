<?php
// Initialization
require_once('tiki-setup.php');

// Now check permissions to access this page
/*
if($tiki_p_view != 'y') {
  $smarty->assign('msg',tra("Permission denied you cannot view pages like this page"));
  $smarty->display('error.tpl');
  die;  
}
*/

if($feature_galleries != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display('error.tpl');
  die;  
}


if(isset($_REQUEST["find"])) {
  $find = $_REQUEST["find"];  
} else {
  $find = ''; 
}


$foo = parse_url($_SERVER["REQUEST_URI"]);
$foo["path"]=str_replace("tiki-galleries","tiki-browse_gallery",$foo["path"]);
$smarty->assign('url',$_SERVER["SERVER_NAME"].$foo["path"]);

// Init smarty variables to blank values
//$smarty->assign('theme','');
$smarty->assign('name','');
$smarty->assign('description','');
$smarty->assign('maxRows',10);
$smarty->assign('rowImages',6);
$smarty->assign('thumbSizeX',80);
$smarty->assign('thumbSizeY',80);
$smarty->assign('public','n');
$smarty->assign('edited','n');
$smarty->assign('edit_moed','n');
if(isset($_REQUEST["edit_mode"])) {
 $smarty->assign('edit_mode','y');
}
// Process the insertion or modification of a gallery here
if(isset($_REQUEST["edit"])) {
  // Check permission to edit
  // No need to check here since the replace_gallery function in the library does the checking  
  // Fill smarty variables and replace record
  
  $smarty->assign('edit_mode','y');
  //$smarty->assign_by_ref('theme',$_REQUEST["theme"]);
  $smarty->assign_by_ref('name',$_REQUEST["name"]);
  $smarty->assign_by_ref('description',$_REQUEST["description"]);
  $smarty->assign_by_ref('maxRows',$_REQUEST["maxRows"]);
  $smarty->assign_by_ref('rowImages',$_REQUEST["rowImages"]);
  $smarty->assign_by_ref('thumbSizeX',$_REQUEST["thumbSizeX"]);
  $smarty->assign_by_ref('thumbSizeY',$_REQUEST["thumbSizeY"]);
  if(isset($_REQUEST["public"]) && $_REQUEST["public"]=="on") {
    $smarty->assign('public','y');
    $public ='y';
  } else {
    $public ='n';
  }
  $tikilib->replace_gallery($_REQUEST["name"],$_REQUEST["description"],'',$user,$_REQUEST["maxRows"],$_REQUEST["rowImages"],$_REQUEST["thumbSizeX"],$_REQUEST["thumbSizeY"],$public);
}

// If we are editing an existing gallery prepare smarty variables
if(isset($_REQUEST["editgal"])) {
  // Get information about this galleryID and fill smarty variables
  $smarty->assign('edit_mode','y');
  $smarty->assign('edited','y');
  $smarty->assign_by_ref('editgal',$_REQUEST["editgal"]);
  $info = $tikilib->get_gallery_info($_REQUEST["editgal"]);
  //$smarty->assign_by_ref('theme',$info["theme"]);
  $smarty->assign_by_ref('name',$info["name"]);
  $smarty->assign_by_ref('description',$info["description"]);
  $smarty->assign_by_ref('maxRows',$info["maxRows"]);
  $smarty->assign_by_ref('rowImages',$info["rowImages"]);
  $smarty->assign_by_ref('thumbSizeX',$info["thumbSizeX"]);
  $smarty->assign_by_ref('thumbSizeY',$info["thumbSizeY"]);
  $smarty->assign_by_ref('public',$info["public"]);
}

if(isset($_REQUEST["removegal"])) {
  // No need to check here since the library checks that the user is admin or the gallery creator
  $tikilib->remove_gallery($_REQUEST["removegal"],$user);
}

if(!isset($_REQUEST["sort_mode"])) {
  $sort_mode = 'name_desc'; 
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

// Get the list of libraries available for this user (or public galleries)
// GET ALL GALLERIES SINCE ALL GALLERIES ARE BROWSEABLE
$galleries = $tikilib->list_galleries($offset,$maxRecords,$sort_mode, 'admin',$find);
// If there're more records then assign next_offset
$cant_pages = ceil($galleries["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages',$cant_pages);
$smarty->assign('actual_page',1+($offset/$maxRecords));

if($galleries["cant"] > ($offset + $maxRecords)) {
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

$smarty->assign_by_ref('galleries',$galleries["data"]);
//print_r($galleries["data"]);

// Display the template
$smarty->assign('mid','tiki-galleries.tpl');
$smarty->display('tiki.tpl');
?>
