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
if($tiki_p_blog_post != 'y') {
  $smarty->assign('msg',tra("Permission denied you cannot post"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

$smarty->assign('wysiwyg','n');
if(isset($_REQUEST['wysiwyg'])&&$_REQUEST['wysiwyg']=='y') {
  $smarty->assign('wysiwyg','y');
}

if(isset($_REQUEST["blogId"])) {
  $blogId = $_REQUEST["blogId"];
  $blog_data = $tikilib->get_blog($_REQUEST["blogId"]);
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

$smarty->assign('data',' ');
$smarty->assign('created',date("U"));

$blog_data = $bloglib->get_blog($blogId);
$smarty->assign_by_ref('blog_data',$blog_data);

if(isset($_REQUEST['remove_image'])) {
  $bloglib->remove_post_image($_REQUEST['remove_image']);
}

// If the articleId is passed then get the article data
if(isset($_REQUEST["postId"]) && $_REQUEST["postId"]>0) {
  // Check permission
  $data = $bloglib->get_post($_REQUEST["postId"]);
  // If the user owns the weblog then he can edit
  if($user && $user==$blog_data["user"]) {
    $data["user"] = $user;
  } 
  if($data["user"]!=$user || !$user) {
    if($tiki_p_blog_admin != 'y') {
      $smarty->assign('msg',tra("Permission denied you cannot edit this post"));
      $smarty->display("styles/$style_base/error.tpl");
      die;  
    }
  }
  if(empty($data["data"])) $data["data"]=' ';
  $smarty->assign('data',$data["data"]);
  $smarty->assign('title',$data["title"]);
  $smarty->assign('trackbacks_to',$data["trackbacks_to"]);
  $smarty->assign('created',$data["created"]);
  $smarty->assign('parsed_data',$tikilib->parse_data($data["data"]));
}


if($postId) {
	if(isset($_FILES['userfile1'])&&is_uploaded_file($_FILES['userfile1']['tmp_name'])) {
	 $fp = fopen($_FILES['userfile1']['tmp_name'],"rb");
	 $data = '';
	 while(!feof($fp)) {
	   $data .= fread($fp,8192*16);
	 }
	 fclose($fp);
	 $size = $_FILES['userfile1']['size'];
	 $name = $_FILES['userfile1']['name'];
	 $type = $_FILES['userfile1']['type'];
	 $bloglib->insert_post_image($postId,$name,$size,$type,$data);
	}
	
	$post_images = $bloglib->get_post_images($postId);
	$smarty->assign_by_ref('post_images',$post_images);
}



$smarty->assign('preview','n');
if(isset($_REQUEST["preview"])) {
  $data = $_REQUEST["data"];
  $parsed_data = $tikilib->parse_data($_REQUEST["data"]);

  if($blog_spellcheck == 'y') {
  if(isset($_REQUEST["spellcheck"])&&$_REQUEST["spellcheck"]=='on') {
  $parsed_data = $tikilib->spellcheckreplace($data,$parsed_data,$language,'blogedit');
  $smarty->assign('spellcheck','y');
  } else {
  $smarty->assign('spellcheck','n');
  }
  }
  if(empty($data)) $data=' ';
  $smarty->assign('data',$data);
  $smarty->assign('parsed_data',$parsed_data);
  $smarty->assign('preview','y');
}

// remove images (permissions!)


if(isset($_REQUEST["save"])||isset($_REQUEST['save_exit'])) {
  include_once("lib/imagegals/imagegallib.php");
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

  if($tiki_p_blog_post != 'y') {
    $smarty->assign('msg',tra("Permission denied you cannot post"));
    $smarty->display("styles/$style_base/error.tpl");
    die;  
  }
  
  if($_REQUEST["postId"]>0) {
    $data = $bloglib->get_post($_REQUEST["postId"]);
    $blog_data = $tikilib->get_blog($data["blogId"]);
    if($user && $user==$blog_data["user"]) {
      $data["user"] = $user;
    } 
    if($data["user"]!=$user || !$user) {
      if($tiki_p_blog_admin != 'y') {
        $smarty->assign('msg',tra("Permission denied you cannot edit this post"));
        $smarty->display("styles/$style_base/error.tpl");
        die;  
      }
    }
  }

  $_REQUEST["data"] = $imagegallib->capture_images($_REQUEST["data"]);
  $title = isset($_REQUEST['title'])?$_REQUEST['title'] : '';
  if($_REQUEST["postId"]>0) {
    $bloglib->update_post($_REQUEST["postId"],$_REQUEST["data"],$user,$title,$_REQUEST['trackback']);
  } else {
    $postid = $bloglib->blog_post($_REQUEST["blogId"],$_REQUEST["data"],$user,$title,$_REQUEST['trackback']);
    $smarty->assign('postId',$postid);
  }
  if(isset($_REQUEST['save_exit'])) {
    header("location: tiki-view_blog.php?blogId=$blogId");
    die;
  }
  
  $data = $_REQUEST["data"];
  $parsed_data = $tikilib->parse_data($_REQUEST["data"]);

  if(empty($data)) $data=' ';
  $smarty->assign('data',$data);
  $smarty->assign('title',isset($_REQUEST["title"])?$_REQUEST['title']:'');
  $smarty->assign('trackbacks_to',explode(',',$_REQUEST['trackback']));
  $smarty->assign('parsed_data',$parsed_data);
}
if($tiki_p_blog_admin == 'y') {
  $blogsd = $bloglib->list_blogs( 0, -1, 'created_desc', '');
  $blogs=$blogsd['data'];
} else {
  $blogs = $bloglib->list_user_blogs($user,1);
}
if(count($blogs)==0) {
  $smarty->assign('msg',tra("You can't post in any blog maybe you have to create a blog first"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}
$smarty->assign_by_ref('blogs',$blogs);
$section='blogs';
include_once('tiki-section_options.php');
// Display the Index Template
$smarty->assign('mid','tiki-blog_post.tpl');
$smarty->assign('show_page_bar','n');
$smarty->display("styles/$style_base/tiki.tpl");
?>
