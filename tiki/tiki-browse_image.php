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

// To browse the image the user has to have access to browser the gallery
/*
if($user!='admin' && $user!=$gal_info["user"] && $gal_info["public"]!='y') {
  $smarty->assign('msg',tra("Permission denied you cannot browse this gallery"));
  $smarty->display('error.tpl');
  die;  
}
*/

if(isset($_REQUEST["move_image"])) {
  if($user!='admin' && $user!=$gal_info["user"] ) {
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
$smarty->assign_by_ref('hits',$info["hits"]);
$smarty->assign_by_ref('image_user',$info["user"]);

$galleries = $tikilib->list_galleries(0,-1,'lastModif_desc', $user,'');
$smarty->assign_by_ref('galleries',$galleries["data"]);



// Display the template
$smarty->assign('mid','tiki-browse_image.tpl');
$smarty->display('tiki.tpl');
?>