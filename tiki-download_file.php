<?php
// Initialization
include_once("lib/init/initlib.php");
require_once('tiki-setup_base.php');
/*
if($feature_file_galleries != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("error.tpl");
  die;
}
*/

if(!isset($_REQUEST["fileId"])) {
  die;
}
$info = $tikilib->get_file($_REQUEST["fileId"]);

$fgal_use_db=$tikilib->get_preference('fgal_use_db','y');
$fgal_use_dir=$tikilib->get_preference('fgal_use_dir','');


$_REQUEST["galleryId"] = $info["galleryId"];

$smarty->assign('individual','n');
if($userlib->object_has_one_permission($_REQUEST["galleryId"],'file gallery')) {
  $smarty->assign('individual','y');
  if($tiki_p_admin != 'y') {
    // Now get all the permissions that are set for this type of permissions 'file gallery'
    $perms = $userlib->get_permissions(0,-1,'permName_desc','','file galleries');
    foreach($perms["data"] as $perm) {
      $permName=$perm["permName"];
      if($userlib->object_has_permission($user,$_REQUEST["galleryId"],'file gallery',$permName)) {
        $$permName = 'y';
        $smarty->assign("$permName",'y');
      } else {
        $$permName = 'n';
        $smarty->assign("$permName",'n');
      }
    }
  }
}
if($tiki_p_admin_file_galleries == 'y') {
  $tiki_p_download_files = 'y';
}


if($tiki_p_download_files != 'y') {
  $smarty->assign('msg',tra("You can not download files"));
  $smarty->display("error.tpl");
  die;
}



$foo = parse_url($_SERVER["REQUEST_URI"]);
$foo1=str_replace("tiki-browse_image","tiki-browse_image",$foo["path"]);
$foo2=str_replace("tiki-browse_image","show_image",$foo["path"]);
$smarty->assign('url_browse',httpPrefix().$foo1);
$smarty->assign('url_show',httpPrefix().$foo2);


$tikilib->add_file_hit($_REQUEST["fileId"]);

$type=&$info["filetype"];
$file=&$info["filename"];
$content=&$info["data"];


//print("File:$file<br />");
//die;
header("Content-type: $type");

// Added by Jenolan  31/8/2003 /////////////////////////////////////////////
// File galleries should always be attachments (files) not inline (textual)
header( "Content-Disposition: attachment; filename=\"$file\"" );
//header( "Content-Disposition: inline; filename=$file" );

if( $info["path"] )
{
	header("Content-Length: ". filesize( $fgal_use_dir.$info["path"] ) );
}
else
{
	header("Content-Length: ". $info[ "filesize" ] );
}

////////////////////////////////////////////////////////////////////////////
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Pragma: public");
if($info["path"]) {
  readfile($fgal_use_dir.$info["path"]);
} else {
  echo "$content";
}
?>
