<?php # $Header: /cvsroot/tikiwiki/tiki/show_image.php,v 1.3 2003-01-04 19:34:16 rossta Exp $

include_once("tiki-setup_base.php");
// show_image.php
// application to display an image from the database with 
// option to resize the image dynamically creating a thumbnail on the fly.
// you have to check if the user has permission to see this gallery
if(!isset($_REQUEST["id"])) {
  die;
}

$gal_use_db=$tikilib->get_preference('gal_use_db','y');
$gal_use_dir=$tikilib->get_preference('gal_use_dir','');

$data = $tikilib->get_image($_REQUEST["id"]);
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
  $ter='';
  $tikilib->add_image_hit($_REQUEST["id"]);
  $type=$data["filetype"];
  $content = $data["data"];
} else {
  $ter='.thumb';
  if(!empty($data["t_data"])) {
    $type = $data["t_type"];
    
    $content = $data["t_data"];
  } else {
    $type=$data["filetype"];
    $content = $data["data"];
  }
}
header("Content-type: $type");
if($data["path"]) {
  readfile($gal_use_dir.$data["path"].$ter);
} else {
  echo "$content";    
}
echo $data;
?>