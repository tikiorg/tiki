<?php # $Header: /cvsroot/tikiwiki/tiki/show_image.php,v 1.5 2003-03-23 12:59:40 redflo Exp $

if (!isset($_REQUEST["nocache"]))
  session_cache_limiter('private_no_expire');

include_once("tiki-setup_base.php");
include_once("lib/imagegals/imagegallib.php");
// show_image.php
// application to display an image from the database with 
// option to resize the image dynamically creating a thumbnail on the fly.
// you have to check if the user has permission to see this gallery
if(!isset($_REQUEST["id"])) {
  die;
}

$gal_use_db=$tikilib->get_preference('gal_use_db','y');
$gal_use_dir=$tikilib->get_preference('gal_use_dir','');

$sxsize=0;
$sysize=0;
if(isset($_REQUEST["thumb"])) {
  $itype='t';
  }
elseif (isset($_REQUEST["scaled"])) {
  $itype='s';
  if (isset($_REQUEST["xsize"]) && is_numeric($_REQUEST["xsize"])) {$sxsize=$_REQUEST["xsize"];}
  if (isset($_REQUEST["ysize"]) && is_numeric($_REQUEST["ysize"])) {$sysize=$_REQUEST["ysize"];}
  }
else {
  $itype='o';
  }

$data = $imagegallib->get_image($_REQUEST["id"],$itype,$sxsize,$sysize);
$galleryId=$data["galleryId"];

$smarty->assign('individual','n');
if($userlib->object_has_one_permission($galleryId,'image gallery')) {
  $smarty->assign('individual','y');
  if($tiki_p_admin != 'y') {
    // Now get all the permissions that are set for this type of permissions 'image gallery'
    $perms = $userlib->get_permissions(0,-1,'permName_desc','','image galleries');
    foreach($perms["data"] as $perm) {
      $permName=$perm["permName"];
      if($userlib->object_has_permission($user,$galleryId,'image gallery',$permName)) {
        $$permName = 'y';
        $smarty->assign("$permName",'y');
      } else {
        $$permName = 'n';
        $smarty->assign("$permName",'n');
      }
    }
  }
}

if(!isset($tiki_p_view_image_gallery)) {
  die;
}

if(!isset($_REQUEST["thumb"])) {
  $imagegallib->add_image_hit($_REQUEST["id"]);
} 

$type=$data["filetype"];
$content = $data["data"];
header("Content-type: $type");
header("Content-Disposition: inline; filename=".$data["filename"]);
//if($data["path"]) {
//  readfile($gal_use_dir.$data["path"].$ter);
//} else {
  echo "$content";    
//}
// ????? echo $data;
?>
