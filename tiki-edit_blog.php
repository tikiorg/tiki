<?php
// Initialization
require_once('tiki-setup.php');

if($feature_blogs != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display('error.tpl');
  die;  
}

// Now check permissions to access this page
if($tiki_p_create_blogs != 'y') {
  $smarty->assign('msg',tra("Permission denied you cannot create or edit blogs"));
  $smarty->display('error.tpl');
  die;  
}

if(isset($_REQUEST["blogId"])) {
  $blogId = $_REQUEST["blogId"];
} else {
  $blogId = 0;
}

$smarty->assign('individual','n');
if($userlib->object_has_one_permission($blogId,'blog')) {
  $smarty->assign('individual','y');
  if($tiki_p_admin != 'y') {
    // Now get all the permissions that are set for this type of permissions 'image gallery'
    $perms = $userlib->get_permissions(0,-1,'permName_desc','','blogs');
    foreach($perms["data"] as $perm) {
      $permName=$perm["permName"];
      if($userlib->object_has_permission($user,$_REQUEST["blogId"],'blog',$permName)) {
        $$permName = 'y';
        $smarty->assign("$permName",'y');
      } else {
        $$permName = 'n';
        $smarty->assign("$permName",'n');
      }
    }
  }
}


$smarty->assign('blogId',$blogId);
$smarty->assign('title','');
$smarty->assign('description','');
$smarty->assign('public','n');
$smarty->assign('maxPosts',10);

// If the articleId is passed then get the article data
if(isset($_REQUEST["blogId"]) && $_REQUEST["blogId"]>0) {
  // Check permission
  $data = $tikilib->get_blog($_REQUEST["blogId"]);
  if($data["user"]!=$user || !$user) {
    if($tiki_p_admin_blogs != 'y') {
      $smarty->assign('msg',tra("Permission denied you cannot edit this blog"));
      $smarty->display('error.tpl');
      die;  
    }
  }
  $smarty->assign('title',$data["title"]);
  $smarty->assign('description',$data["description"]);
  $smarty->assign('public',$data["public"]);
  $smarty->assign('maxPosts',$data["maxPosts"]);
}


if(isset($_REQUEST["save"])) {
  if(isset($_REQUEST["public"])&&$_REQUEST["public"]=='on') {
    $public = 'y';
  } else {
    $public = 'n';
  }
  $tikilib->replace_blog($_REQUEST["title"],$_REQUEST["description"],$user,$public,$_REQUEST["maxPosts"],$_REQUEST["blogId"]);
  header("location: tiki-list_blogs.php");
  die;
}

// Display the Index Template
$smarty->assign('mid','tiki-edit_blog.tpl');
$smarty->assign('show_page_bar','n');
$smarty->display('tiki.tpl');
?>