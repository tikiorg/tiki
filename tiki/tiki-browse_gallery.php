<?php
// Initialization
require_once('tiki-setup.php');
include_once("lib/imagegals/imagegallib.php");

if($feature_galleries != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

if($_REQUEST["galleryId"]==0 && $tiki_p_admin_galleries != 'y') {
  $smarty->assign('msg',tra("Permission denied you cannot access this gallery"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

if(!isset($_REQUEST["galleryId"])) {
  $smarty->assign('msg',tra("No gallery indicated"));
  $smarty->display("styles/$style_base/error.tpl");
  die;
}

$smarty->assign('individual','n');
if($userlib->object_has_one_permission($_REQUEST["galleryId"],'image gallery')) {
  $smarty->assign('individual','y');
  if($tiki_p_admin != 'y') {
    // Now get all the permissions that are set for this type of permissions 'image gallery'
    $perms = $userlib->get_permissions(0,-1,'permName_desc','','image galleries');
    foreach($perms["data"] as $perm) {
      $permName=$perm["permName"];
      if($userlib->object_has_permission($user,$_REQUEST["galleryId"],'image gallery',$permName)) {
        $$permName = 'y';
        $smarty->assign("$permName",'y');
      } else {
        $$permName = 'n';
        $smarty->assign("$permName",'n');
      }
    }
  }
}
if($tiki_p_admin_galleries == 'y') {
  $tiki_p_view_image_gallery = 'y';
  $smarty->assign("tiki_p_view_image_gallery",'y');
  $tiki_p_upload_images = 'y';
  $smarty->assign("tiki_p_upload_images",'y');
  $tiki_p_create_galleries = 'y';
  $smarty->assign("tiki_p_create_galleries",'y');
}

if($tiki_p_view_image_gallery != 'y') {
  $smarty->assign('msg',tra("Permission denied you can not view this section"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}



if($_REQUEST["galleryId"]!=0) {
// To browse the gallery the user has to be admin, the owner or the gallery has to be public
$gal_info = $imagegallib->get_gallery($_REQUEST["galleryId"]);
//$smarty->assign_by_ref('theme',$gal_info["theme"]);
//$smarty->assign('use_theme','y');
/*
if($user!='admin' && $user!=$gal_info["user"] && $gal_info["public"]!='y') {
  $smarty->assign('msg',tra("Permission denied you cannot browse this gallery"));
  $smarty->display("styles/$style_base/error.tpl");
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

$imagegallib->add_gallery_hit($_REQUEST["galleryId"]);

if(isset($_REQUEST["remove"])) {
  // To remove an image the user must be the owner or admin
  if(($tiki_p_admin_galleries != 'y')  && (!$user || $user!=$gal_info["user"])) {
    $smarty->assign('msg',tra("Permission denied you cannot remove images from this gallery"));
    $smarty->display("styles/$style_base/error.tpl");
    die;  
  }
  $imagegallib->remove_image($_REQUEST["remove"]);
}

if(isset($_REQUEST["rebuild"])) {
 // To remove an image the user must be the owner or admin
  if(($tiki_p_admin_galleries != 'y') && (!$user || $user!=$gal_info["user"])) {
    $smarty->assign('msg',tra("Permission denied you cannot rebuild thumbnails in this gallery"));
    $smarty->display("styles/$style_base/error.tpl");
    die;  
  }
  $imagegallib->rebuild_thumbnails($_REQUEST["rebuild"]);
}

if(isset($_REQUEST["rotateright"])) {
 // To rotate an image the user must be the owner or admin
  if(($tiki_p_admin_galleries != 'y') && (!$user || $user!=$gal_info["user"])) {
    $smarty->assign('msg',tra("Permission denied you cannot rotate images in this gallery"));
    $smarty->display("styles/$style_base/error.tpl");
    die; 
  }
  $imagegallib->rotate_right_image($_REQUEST["rotateright"]);
}

if(isset($_REQUEST["rotateleft"])) {
 // To rotate an image the user must be the owner or admin
  if(($tiki_p_admin_galleries != 'y') && (!$user || $user!=$gal_info["user"])) {
    $smarty->assign('msg',tra("Permission denied you cannot rotate images in this gallery"));
    $smarty->display("styles/$style_base/error.tpl");
    die; 
  }
  $imagegallib->rotate_left_image($_REQUEST["rotateleft"]);
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
  $info = $imagegallib->get_gallery($_REQUEST["galleryId"]);
  $nextscaleinfo = $imagegallib->get_gallery_next_scale($_REQUEST["galleryId"]);
}

if(!isset($info["maxRows"])) $info["maxRows"]=10;
if(!isset($info["rowImages"])) $info["rowImages"]=5;
if(!isset($nextscaleinfo["xsize"])) {
  $nextscaleinfo["xsize"]=0;
  $nextscaleinfo["ysize"]=0;}
if($info["maxRows"]==0) $info["maxRows"]=10;
if($info["rowImages"]==0) $info["rowImages"]=6;
$maxRecords = $info["maxRows"] * $info["rowImages"];
$smarty->assign_by_ref('rowImages',$info["rowImages"]);
$smarty->assign('rowImages2',$info["rowImages"]-1);
$smarty->assign_by_ref('thx',$info["thumbSizeX"]);
$smarty->assign_by_ref('thy',$info["thumbSizeY"]);
$smarty->assign_by_ref('name',$info["name"]);
$smarty->assign_by_ref('description',$info["description"]);
$smarty->assign_by_ref('nextx',$nextscaleinfo["xsize"]);
$smarty->assign_by_ref('nexty',$nextscaleinfo["ysize"]);

// Can we rotate images
/*
if (function_exists("imagerotate")) {
  $smarty->assign('imagerotate',true);
} else {
  $smarty->assign('imagerotate',false);
}
*/
// Above disabled until imagerotate is bug-free
$smarty->assign('imagerotate',false);

if(!isset($_REQUEST["sort_mode"])) {
  $sort_mode = 'created_desc'; 
  $_REQUEST["sort_mode"] = 'created_desc'; 
} else {
  $sort_mode = $_REQUEST["sort_mode"];
} 
$smarty->assign_by_ref('sort_mode',$sort_mode);

// If offset is set use it if not then use offset =0
// use the maxRecords php variable to set the limit
// if sortMode is not set then use lastModif_desc
if(!isset($_REQUEST["offset"])) {
  $offset = 0;
  $_REQUEST["offset"]=0;
} else {
  $offset = $_REQUEST["offset"]; 
}
$smarty->assign_by_ref('offset',$offset);

if(isset($_REQUEST["find"])) {
  $find = $_REQUEST["find"];  
} else {
  $find = ''; 
  $_REQUEST["find"] = '';
}

$images = $imagegallib->get_images($offset,$maxRecords,$sort_mode,$find,$_REQUEST["galleryId"]);
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

if($feature_image_galleries_comments == 'y') {
  $comments_per_page = $image_galleries_comments_per_page;
  $comments_default_ordering = $image_galleries_comments_default_ordering;
  $comments_vars=Array('galleryId');
  $comments_prefix_var='image_gallery';
  $comments_object_var='galleryId';
  include_once("comments.php");
}

if($feature_image_galleries_comments == 'y') {
  $comments_per_page = $image_galleries_comments_per_page;
  $comments_default_ordering = $image_galleries_comments_default_ordering;
  $comments_vars=Array('galleryId','offset','sort_mode');
  $comments_prefix_var='image gallery';
  $comments_object_var='galleryId';
  include_once("comments.php");
}

$section='galleries';
include_once('tiki-section_options.php');

if($feature_theme_control == 'y') {
	$cat_type='image gallery';
	$cat_objid = $_REQUEST["galleryId"];
	include('tiki-tc.php');
}


// Display the template
$smarty->assign('mid','tiki-browse_gallery.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>
