<?php
// Initialization
require_once('tiki-setup.php');

if($feature_blogs != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display('error.tpl');
  die;  
}


// Now check permissions to access this page
if($tiki_p_blog_post != 'y') {
  $smarty->assign('msg',tra("Permission denied you cannot post"));
  $smarty->display('error.tpl');
  die;  
}

if(isset($_REQUEST["blogId"])) {
  $blogId = $_REQUEST["blogId"];
} else {
  $blogId = 0;
}
$smarty->assign('blogId',$blogId);

if(isset($_REQUEST["postId"])) {
  $postId = $_REQUEST["postId"];
} else {
  $postId = 0;
}
$smarty->assign('postId',$postId);

$smarty->assign('data','');
$smarty->assign('created',date("U"));
// If the articleId is passed then get the article data
if(isset($_REQUEST["postId"]) && $_REQUEST["postId"]>0) {
  // Check permission
  $data = $tikilib->get_post($_REQUEST["postId"]);
  if($data["user"]!=$user) {
    if($tiki_p_admin_blogs != 'y') {
      $smarty->assign('msg',tra("Permission denied you cannot edit this post"));
      $smarty->display('error.tpl');
      die;  
    }
  }
  $smarty->assign('data',$data["data"]);
  $smarty->assign('created',$data["created"]);
  $smarty->assign('parsed_data',$tikilib->parse_data($data["data"]));
}

$smarty->assign('preview','n');
if(isset($_REQUEST["preview"])) {
  $smarty->assign('data',$_REQUEST["data"]);
  $smarty->assign('parsed_data',$tikilib->parse_data($_REQUEST["data"]));
  $smarty->assign('preview','y');
}

if(isset($_REQUEST["save"])) {
  $_REQUEST["data"] = $tikilib->capture_images($_REQUEST["data"]);
  if($_REQUEST["postId"]>0) {
    $tikilib->update_post($_REQUEST["postId"],$_REQUEST["data"],$user);
  } else {
    $tikilib->blog_post($_REQUEST["blogId"],$_REQUEST["data"],$user);
  }
  $links = $tikilib->get_links($_REQUEST["data"]);
  $tikilib->cache_links($links);
  header("location: tiki-view_blog.php?blogId=".$_REQUEST["blogId"]);
  die;
}
$blogs = $tikilib->list_user_blogs($user,1);
if(count($blogs)==0) {
  $smarty->assign('msg',tra("You can't post in any blog maybe you have to create a blog first"));
  $smarty->display('error.tpl');
  die;  
}
$smarty->assign_by_ref('blogs',$blogs);


// Display the Index Template
$smarty->assign('mid','tiki-blog_post.tpl');
$smarty->assign('show_page_bar','n');
$smarty->display('tiki.tpl');
?>