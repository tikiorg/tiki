<?php
// Initialization
require_once('tiki-setup.php');
include_once("lib/imagegals/imagegallib.php");

if($feature_galleries != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}


if(!isset($_REQUEST["imageId"])) {
  $smarty->assign('msg',tra("No image indicated"));
  $smarty->display("styles/$style_base/error.tpl");
  die;
}

// get info for scaled images
$scaleinfo = $imagegallib->get_gallery_scale_info($_REQUEST["galleryId"]);
$sxsize=0;
$sysize=0;
if(isset($_REQUEST["xsize"])) $sxsize=$_REQUEST["xsize"];
if(isset($_REQUEST["ysize"])) $sysize=$_REQUEST["ysize"];

$prevx=0;
$prevy=0;
$prevt='o';
$nextx=0;
$nexty=0;
$nextt='o';
while (list ($key, $val) = each ($scaleinfo)) {
  if ($val["xsize"] < $sxsize && $val["ysize"] < $sysize ) {
    $prevx=$val["xsize"];
    $prevy=$val["ysize"];
    $prevt='s';
  }
  if ($val["xsize"] > $sxsize && $val["ysize"] > $sysize ) {
    $nextx=$val["xsize"];
    $nexty=$val["ysize"];
    $nextt='s';
  }
}


if(!isset($_REQUEST["scaled"])) 
{
  $itype='o';
  $sxsize=0;
  $sysize=0;
} else {
  $itype='s';
  $sxsize=$_REQUEST["xsize"];
  $sysize=$_REQUEST["ysize"];
}

$smarty->assign_by_ref('itype',$itype);
$smarty->assign_by_ref('sxsize',$sxsize);
$smarty->assign_by_ref('sysize',$sysize);
$smarty->assign_by_ref('nextx',$nextx);
$smarty->assign_by_ref('nexty',$nexty);
$smarty->assign_by_ref('nextt',$nextt);
$smarty->assign_by_ref('prevx',$prevx);
$smarty->assign_by_ref('prevy',$prevy);
$smarty->assign_by_ref('prevt',$prevt);

  

$info = $imagegallib->get_image_info($_REQUEST["imageId"],$itype,$sxsize,$sysize);
$gal_info = $imagegallib->get_gallery($info["galleryId"]);
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
  $smarty->assign('msg',tra("Permission denied you can not view this section"));
  $smarty->display("styles/$style_base/error.tpl");
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
$image_prev = $imagegallib->get_images($offset+$_REQUEST["desp"]-1,1,$sort_mode,'',$_REQUEST["galleryId"]);
if(count($image_prev["data"])==1) {
  $smarty->assign('previmg',$image_prev["data"][0]["imageId"]);
} else {
  $smarty->assign('previmg','');
}
$image_next = $imagegallib->get_images($offset+$_REQUEST["desp"]+1,1,$sort_mode,'',$_REQUEST["galleryId"]);
if(count($image_next["data"])==1) {
  $smarty->assign('nextimg',$image_next["data"][0]["imageId"]);
} else {
  $smarty->assign('nextimg','');
}
$smarty->assign('offset',$_REQUEST["offset"]);
$smarty->assign('prevdesp',$_REQUEST["desp"]-1);
$smarty->assign('nextdesp',$_REQUEST["desp"]+1);
$smarty->assign('desp',$_REQUEST["desp"]);
$smarty->assign('sort_mode',$_REQUEST["sort_mode"]);
$smarty->assign('galleryId',$_REQUEST["galleryId"]);


$foo = parse_url($_SERVER["REQUEST_URI"]);
$foo1=str_replace("tiki-browse_image","tiki-browse_image",$foo["path"]);
$foo2=str_replace("tiki-browse_image","show_image",$foo["path"]);
$smarty->assign('url_browse',httpPrefix().$foo1);
$smarty->assign('url_show',httpPrefix().$foo2);


$imagegallib->add_image_hit($_REQUEST["imageId"]);
$info = $imagegallib->get_image_info($_REQUEST["imageId"]); //todo: already known???
$gal_info = $imagegallib->get_gallery($info["galleryId"]);
//$smarty->assign_by_ref('theme',$gal_info["theme"]);
//$smarty->assign('use_theme','y');

// Everybody can browse images

if(isset($_REQUEST["move_image"])) {
  if($tiki_p_admin_galleries!='y' && (!$user || $user!=$gal_info["user"]) ) {
    $smarty->assign('msg',tra("Permission denied you cannot move images from this gallery"));
    $smarty->display("styles/$style_base/error.tpl");
    die;  
  }
  $imagegallib->move_image($_REQUEST["imageId"],$_REQUEST["newgalleryId"]);
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

$galleries = $imagegallib->list_visible_galleries(0,-1,'lastModif_desc', $user,'');
$smarty->assign_by_ref('galleries',$galleries["data"]);

// Init vars
$smarty->assign('popup','');
$smarty->assign('popupsize','');
$winxsize = 0; $winysize = 0;


// Calculate PopUp Window size for the popup link
$winx = $info['xsize']; if ($winx < 320) { $winx = 320; }
$winy = $info['ysize']; if ($winy < 200) { $winy = 200; }
// Give it some more pixels for the links and a little margin
$winx += 40;
$winy += 80;
// Now get'em to the template
$smarty->assign('winx',$winx);
$smarty->assign('winy',$winy);

$section='galleries';
include_once('tiki-section_options.php');
if($feature_theme_control == 'y') {
	$cat_type='image gallery';
	$cat_objid = $_REQUEST["galleryId"];
	include('tiki-tc.php');
}

// now set it if needed
if (isset($_REQUEST['popup']) and ($_REQUEST['popup'])) {
	$smarty->assign('popup','y');
	$smarty->assign('feature_top_bar','n');
	$smarty->assign('feature_left_column','n');
	$smarty->assign('feature_right_column','n');
	$smarty->assign('feature_bot_bar','n');
}

// Display the template
$smarty->assign('mid','tiki-browse_image.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>
