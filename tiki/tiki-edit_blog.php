<?php
// Initialization
require_once('tiki-setup.php');
include_once('lib/blogs/bloglib.php');

if($feature_blogs != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

// Now check permissions to access this page
if($tiki_p_create_blogs != 'y') {
  $smarty->assign('msg',tra("Permission denied you cannot create or edit blogs"));
  $smarty->display("styles/$style_base/error.tpl");
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
$smarty->assign('use_find','y');
$smarty->assign('use_title','y');
$smarty->assign('allow_comments', 'y');
$smarty->assign('maxPosts',10);
$heading='<div class="blogtitle">Blog: {$title}</div>'."\n";
$heading.='<div class="bloginfo">'."\n";
$heading.='{tr}Created by{/tr} {$creator}{tr} on {/tr}{$created|tiki_short_datetime}<br/>'."\n";
$heading.='{tr}Last modified{/tr} {$lastModif|tiki_short_datetime}<br/><br/>'."\n";
$heading.='({$posts} {tr}posts{/tr} | {$hits} {tr}visits{/tr} | {tr}Activity={/tr}{$activity|string_format:"%.2f"})'."\n";
$heading.='{if $tiki_p_blog_post eq "y"}'."\n";
$heading.='{if ($user and $creator eq $user) or $tiki_p_blog_admin eq "y" or $public eq "y"}'."\n";
$heading.='[<a class="bloglink" href="tiki-blog_post.php?blogId={$blogId}">{tr}Post{/tr}</a>]'."\n";
$heading.='{/if}'."\n";
$heading.='{if $rss_blog eq "y"}'."\n";
$heading.='[<a class="bloglink" href="tiki-blog_rss.php?blogId={$blogId}">RSS</a>]'."\n";
$heading.='{/if}'."\n";
$heading.='{/if}'."\n";
$heading.='{if ($user and $creator eq $user) or $tiki_p_blog_admin eq "y"}'."\n";
$heading.='[<a class="bloglink" href="tiki-edit_blog.php?blogId={$blogId}">{tr}Edit{/tr}</a>]'."\n";
$heading.='{/if}'."\n";
$heading.='</div>'."\n";
$heading.='<div class="blogdesc">{tr}Description:{/tr}{$description}</div>';
$smarty->assign_by_ref('heading',$heading);


if(isset($_REQUEST["blogId"]) && $_REQUEST["blogId"]>0) {
  // Check permission
  $data = $tikilib->get_blog($_REQUEST["blogId"]);
  if($data["user"]!=$user || !$user) {
    if($tiki_p_blog_admin != 'y') {
      $smarty->assign('msg',tra("Permission denied you cannot edit this blog"));
      $smarty->display("styles/$style_base/error.tpl");
      die;  
    }
  }
  $smarty->assign('title',$data["title"]);
  $smarty->assign('description',$data["description"]);
  $smarty->assign('public',$data["public"]);
  $smarty->assign('use_title',$data["use_title"]);
  $smarty->assign('allow_comments',$data["allow_comments"]);
  $smarty->assign('use_find',$data["use_find"]);
  $smarty->assign('maxPosts',$data["maxPosts"]);
  $smarty->assign('heading',$data["heading"]);
}

if(isset($_REQUEST['preview'])) {
  $smarty->assign('title',$_REQUEST["title"]);
  $smarty->assign('description',$_REQUEST["description"]);
  $smarty->assign('public',isset($_REQUEST["public"])?'y':'n');
  $smarty->assign('use_find',isset($_REQUEST["use_find"])?'y':'n');
  $smarty->assign('use_title',isset($_REQUEST["use_title"])?'y':'n');
  $smarty->assign('allow_comments',isset($_REQUEST["allow_comments"])?'y':'n');
  $smarty->assign('maxPosts',$_REQUEST["maxPosts"]);
  $smarty->assign('heading',$_REQUEST["heading"]);
}


if(isset($_REQUEST["save"])) {
  if(isset($_REQUEST["public"])&&$_REQUEST["public"]=='on') {
    $public = 'y';
  } else {
    $public = 'n';
  }
  $use_title = isset($_REQUEST['use_title'])?'y':'n';
  $allow_comments = isset($_REQUEST['allow_comments'])?'y':'n';
  $use_find = isset($_REQUEST['use_find'])?'y':'n';
  $bid=$bloglib->replace_blog($_REQUEST["title"],$_REQUEST["description"],$user,$public,$_REQUEST["maxPosts"],$_REQUEST["blogId"],$_REQUEST['heading'],$use_title,$use_find,$allow_comments);
  
  $cat_type='blog';
  $cat_objid = $bid;
  $cat_desc = substr($_REQUEST["description"],0,200);
  $cat_name = $_REQUEST["title"];
  $cat_href="tiki-view_blog.php?blogId=".$cat_objid;
  include_once("categorize.php");
  
  header("location: tiki-list_blogs.php");
  die;
}

$cat_type='blog';
$cat_objid = $blogId;
include_once("categorize_list.php");


// Display the Index Template
$smarty->assign('mid','tiki-edit_blog.tpl');
$smarty->assign('show_page_bar','n');
$smarty->display("styles/$style_base/tiki.tpl");
?>