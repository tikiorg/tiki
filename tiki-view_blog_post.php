<?php
// Initialization
require_once('tiki-setup.php');
include_once('lib/blogs/bloglib.php');

if(!isset($_REQUEST['blogId'])&&!isset($_REQUEST['postId'])) {
  $parts = parse_url($_SERVER['REQUEST_URI']);
  $paths = explode('/',$parts['path']);
  $blogId = $paths[count($paths)-2];
  $postId = $paths[count($paths)-1];
  // So this is to process a trackback ping
  
  if(isset($_REQUEST['__mode'])) {
    // Build RSS listing trackback_from
    $pings = $bloglist->get_trackbacks_from($postId);
  }
  
  if(isset($_REQUEST['url'])) {
    // Add a trackback ping to the list of trackback_from
    $title = isset($_REQUEST['title'])?$_REQUEST['title']:'';
    $excerpt = isset($_REQUEST['excerpt'])?$_REQUEST['excerpt']:'';
    $blog_name = isset($_REQUEST['blog_name'])?$_REQUEST['blog_name']:'';
    if($bloglib->add_trackback_from($postId,$_REQUEST['url'],$title,$excerpt,$blog_name))
    {
	    print('<?xml version="1.0" encoding="iso-8859-1"?>');
	    print('<response>');
	    print('<error>0</error>');
	    print('</response>');

    } else {
        print('<?xml version="1.0" encoding="iso-8859-1"?>');
	    print('<response>');
	    print('<error>1</error>');
	    print('<message>Error trying to add ping for post</message>');
	    print('</response>');
    }
    die;
  }
}




if($feature_blogs != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

if(!isset($_REQUEST["postId"])) {
  $smarty->assign('msg',tra("No post indicated"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

//Build absolute URI for this
$parts = parse_url($_SERVER['REQUEST_URI']);
$uri = httpPrefix().$parts['path'].'?blogId='.$_REQUEST['blogId'].'&postId='.$_REQUEST['postId'];
$uri2 = httpPrefix().$parts['path'].'/'.$_REQUEST['blogId'].'/'.$_REQUEST['postId'];
$smarty->assign('uri',$uri);
$smarty->assign('uri2',$uri2);

$postId=$_REQUEST["postId"];
$post_info = $bloglib->get_post($_REQUEST["postId"]);
$smarty->assign('post_info',$post_info);
$smarty->assign('postId',$_REQUEST["postId"]);
$_REQUEST["blogId"]=$post_info["blogId"];
$blog_data = $bloglib->get_blog($_REQUEST['blogId']);
$smarty->assign('blog_data',$blog_data);
$smarty->assign('blogId',$_REQUEST["blogId"]);

if(!isset($_REQUEST['offset'])) $_REQUEST['offset']=0;
if(!isset($_REQUEST['sort_mode'])) $_REQUEST['sort_mode']='created_desc';
if(!isset($_REQUEST['find'])) $_REQUEST['find']='';

$smarty->assign('offset',$_REQUEST["offset"]);
$smarty->assign('sort_mode',$_REQUEST["sort_mode"]);
$smarty->assign('find',$_REQUEST["find"]);
$offset=$_REQUEST["offset"];
$sort_mode=$_REQUEST["sort_mode"];
$find=$_REQUEST["find"];

$parsed_data = $tikilib->parse_data($post_info["data"]);
$smarty->assign('parsed_data',$parsed_data);

$smarty->assign('individual','n');
if($userlib->object_has_one_permission($_REQUEST["blogId"],'blog')) {
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

if($tiki_p_blog_admin == 'y') {
  $tiki_p_create_blogs = 'y';
  $smarty->assign('tiki_p_create_blogs','y');
  $tiki_p_blog_post = 'y';
  $smarty->assign('tiki_p_blog_post','y');
  $tiki_p_read_blog = 'y';
  $smarty->assign('tiki_p_read_blog','y');
}

if($tiki_p_read_blog != 'y') {
  $smarty->assign('msg',tra("Permission denied you can not view this section"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

$blog_data = $tikilib->get_blog($_REQUEST["blogId"]);
$ownsblog = 'n';
if($user && $user == $blog_data["user"]) {
    $ownsblog = 'y';
}
$smarty->assign('ownsblog',$ownsblog);
if(!$blog_data) {
  $smarty->assign('msg',tra("Blog not found"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}



if($feature_blogposts_comments == 'y') {
  $comments_per_page = $blog_comments_per_page;
  $comments_default_ordering = $blog_comments_default_ordering;
  $comments_vars=Array('postId','offset','find','sort_mode');
  $comments_prefix_var='post';
  $comments_object_var='postId';
  include_once("comments.php");
}

$section='blogs';
include_once('tiki-section_options.php');
if($feature_theme_control == 'y') {
	$cat_type='blog';
	$cat_objid = $_REQUEST['blogId'];
	include('tiki-tc.php');
}

if($user 
	&& $tiki_p_notepad == 'y' 
	&& $feature_notepad == 'y' 
	&& isset($_REQUEST['savenotepad'])) {
  $tikilib->replace_note($user,0,$post_info['title']?$post_info['title']:date("d/m/Y [h:i]",$post_info['created']),$post_info['data']);
}


// Display the template
$smarty->assign('mid','tiki-view_blog_post.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>
