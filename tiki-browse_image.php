<?php
// Initialization
require_once('tiki-setup.php');

if($feature_galleries != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display('error.tpl');
  die;  
}


if(!isset($_REQUEST["imageId"])) {
  $smarty->assign('msg',tra("No image indicated"));
  $smarty->display('error.tpl');
  die;
}
$info = $tikilib->get_image($_REQUEST["imageId"]);
$gal_info = $tikilib->get_gallery($info["galleryId"]);
$_REQUEST["galleryId"] = $info["galleryId"];

if(!isset($_REQUEST["sort_mode"])) {
  $_REQUEST["sort_mode"] = "created_desc";
}
$sort_mode = $_REQUEST["sort_mode"];

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
  $smarty->assign('msg',tra("Permission denied you cant view this section"));
  $smarty->display('error.tpl');
  die;  
}


// BUILD NEXT AND PREV IMG WITH THE INFORMATION FROM SORT MODE OFFSET AND DESP
// $images = $tikilib->get_images($offset,$maxRecords,$sort_mode,$find,$_REQUEST["galleryId"]);
// Ver si offset es 0 y desp 0 entonces no hay mas
// VERRRRRRRRRRRRRRRRRr
if(!isset($_REQUEST["desp"])) {
  $_REQUEST["desp"]=0;
}
if(!isset($_REQUEST["offset"])) {
  $_REQUEST["offset"]=0;
}
$offset=$_REQUEST["offset"];
$image_prev = $tikilib->get_images($offset+$_REQUEST["desp"]-1,1,$sort_mode,'',$_REQUEST["galleryId"]);
if(count($image_prev["data"])==1) {
  $smarty->assign('previmg',$image_prev["data"][0]["imageId"]);
} else {
  $smarty->assign('previmg','');
}
$image_next = $tikilib->get_images($offset+$_REQUEST["desp"]+1,1,$sort_mode,'',$_REQUEST["galleryId"]);
if(count($image_next["data"])==1) {
  $smarty->assign('nextimg',$image_next["data"][0]["imageId"]);
} else {
  $smarty->assign('nextimg','');
}
$smarty->assign('offset',$_REQUEST["offset"]);
$smarty->assign('prevdesp',$_REQUEST["desp"]-1);
$smarty->assign('nextdesp',$_REQUEST["desp"]+1);
$smarty->assign('sort_mode',$_REQUEST["sort_mode"]);
$smarty->assign('galleryId',$_REQUEST["galleryId"]);


$foo = parse_url($_SERVER["REQUEST_URI"]);
$foo1=str_replace("tiki-browse_image","tiki-browse_image",$foo["path"]);
$foo2=str_replace("tiki-browse_image","show_image",$foo["path"]);
$smarty->assign('url_browse',$_SERVER["SERVER_NAME"].$foo1);
$smarty->assign('url_show',$_SERVER["SERVER_NAME"].$foo2);


$tikilib->add_image_hit($_REQUEST["imageId"]);
$info = $tikilib->get_image($_REQUEST["imageId"]);
$gal_info = $tikilib->get_gallery($info["galleryId"]);
//$smarty->assign_by_ref('theme',$gal_info["theme"]);
//$smarty->assign('use_theme','y');

// Everybody can browse images

if(isset($_REQUEST["move_image"])) {
  if($tiki_p_admin_galleries!='y' && (!$user || $user!=$gal_info["user"]) ) {
    $smarty->assign('msg',tra("Permission denied you cannot move images from this gallery"));
    $smarty->display('error.tpl');
    die;  
  }
  $tikilib->move_image($_REQUEST["imageId"],$_REQUEST["galleryId"]);
}



$smarty->assign_by_ref('owner',$gal_info["user"]);
$smarty->assign_by_ref('imageId',$_REQUEST["imageId"]);
$smarty->assign_by_ref('name',$info["name"]);
$smarty->assign_by_ref('galleryId',$info["galleryId"]);
$smarty->assign_by_ref('description',$info["description"]);
$smarty->assign_by_ref('created',$info["created"]);
$smarty->assign_by_ref('filename',$info["filename"]);
$smarty->assign_by_ref('xsize',$info["xsize"]);
$smarty->assign_by_ref('ysize',$info["ysize"]);
$smarty->assign_by_ref('hits',$info["hits"]);
$smarty->assign_by_ref('image_user',$info["user"]);

$galleries = $tikilib->list_visible_galleries(0,-1,'lastModif_desc', $user,'');
$smarty->assign_by_ref('galleries',$galleries["data"]);

// Display the template
$smarty->assign('mid','tiki-browse_image.tpl');
$smarty->display('tiki.tpl');
?>