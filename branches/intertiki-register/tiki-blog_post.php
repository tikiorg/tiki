<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-blog_post.php,v 1.63.2.2 2007-11-24 15:28:37 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
$section = 'blogs';
require_once ('tiki-setup.php');
include_once ('lib/categories/categlib.php');
include_once ('lib/blogs/bloglib.php');

$smarty->assign('headtitle',tra('Edit Post'));

if ($prefs['feature_freetags'] == 'y') {
	include_once('lib/freetag/freetaglib.php');
}

if ($prefs['feature_blogs'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_blogs");

	$smarty->display("error.tpl");
	die;
}

// Now check permissions to access this page
if ((empty($_REQUEST['blogId']) && $tiki_p_blog_post != 'y') || (!empty($_REQUEST["blogId"]) && !$tikilib->user_has_perm_on_object($user, $_REQUEST['blogId'], 'blog', 'tiki_p_blog_post'))) {
	$smarty->assign('msg', tra("Permission denied you cannot post"));

	$smarty->display("error.tpl");
	die;
}

if ( $prefs['feature_wysiwyg'] == 'y' &&
	( $prefs['wysiwyg_default'] == 'y' && ! isset($_REQUEST['wysiwyg']) )
	|| ( isset($_REQUEST['wysiwyg']) && $_REQUEST['wysiwyg'] == 'y' )
) $smarty->assign('wysiwyg', 'y');
else $smarty->assign('wysiwyg', 'n');

if (isset($_REQUEST["blogId"])) {
	$blogId = $_REQUEST["blogId"];

	$blog_data = $tikilib->get_blog($_REQUEST["blogId"]);
} else {
	$blogId = 0;
}

$smarty->assign('blogId', $blogId);

if (isset($_REQUEST["postId"])) {
	$postId = $_REQUEST["postId"];
} else {
	$postId = 0;
}

$smarty->assign('postId', $postId);

$smarty->assign('data', '');
$smarty->assign('created', $tikilib->now);

$blog_data = $bloglib->get_blog($blogId);
$smarty->assign_by_ref('blog_data', $blog_data);

if (isset($_REQUEST['remove_image'])) {
  $area = 'delblogpostimage';
  if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
    key_check($area);
		$bloglib->remove_post_image($_REQUEST['remove_image']);
  } else {
    key_get($area);
  }
}

// If the articleId is passed then get the article data
if (isset($_REQUEST["postId"]) && $_REQUEST["postId"] > 0) {
	// Check permission
	$data = $bloglib->get_post($_REQUEST["postId"]);

	// If the user owns the weblog then he can edit
	if ($user && $user == $blog_data["user"]) {
		$data["user"] = $user;
	}

	if ($data["user"] != $user || !$user) {
		if ($tiki_p_blog_admin != 'y' && !$tikilib->user_has_perm_on_object($user, $_REQUEST['blogId'], 'blog', 'tiki_p_blog_admin')) {
			$smarty->assign('msg', tra("Permission denied you cannot edit this post"));

			$smarty->display("error.tpl");
			die;
		}
	}

	if (empty($data["data"]))
		$data["data"] = '';

	$smarty->assign('data', TikiLib::htmldecode( $data["data"] ) );
	$smarty->assign('title', $data["title"]);
	$smarty->assign('created', $data["created"]);
	$smarty->assign('parsed_data', $tikilib->parse_data($data["data"]));
    $smarty->assign('blogpriv', $data["priv"]);
}

if ($postId) {
	check_ticket('blog');
	if (isset($_FILES['userfile1']) && is_uploaded_file($_FILES['userfile1']['tmp_name'])) {
		$fp = fopen($_FILES['userfile1']['tmp_name'], "rb");

		$data = '';

		while (!feof($fp)) {
			$data .= fread($fp, 8192 * 16);
		}

		fclose ($fp);
		$size = $_FILES['userfile1']['size'];
		$name = $_FILES['userfile1']['name'];
		$type = $_FILES['userfile1']['type'];
		$bloglib->insert_post_image($postId, $name, $size, $type, $data);
	}

	$post_images = $bloglib->get_post_images($postId);
	$smarty->assign_by_ref('post_images', $post_images);
	$cat_type = 'blog post';
	$cat_objid = $postId;
	include_once ('freetag_list.php');
}

$smarty->assign('preview', 'n');

if ($tiki_p_admin != 'y') {
    if ($tiki_p_use_HTML != 'y') {
		$_REQUEST["allowhtml"] = 'off';
    }
}

$blogpriv='n';
$smarty->assign('blogpriv', 'n');
if(isset($_REQUEST["data"])) {
    if (($prefs['feature_wiki_allowhtml'] == 'y' and $tiki_p_use_HTML == 'y' 
		and isset($_REQUEST["allowhtml"]) && $_REQUEST["allowhtml"]=="on")) {
		$edit_data = $_REQUEST["data"];  
    } else {
		$edit_data = $_REQUEST["data"];
    }
} else {
    if (isset($data["data"])) {
		$edit_data = $data["data"];
    } else {
		$edit_data = '';
    }
    if (isset($data["priv"])) {
	    $smarty->assign('blogpriv', $data["priv"]);
	    $blogpriv=$data["priv"];
	}
}
if (isset($_REQUEST["blogpriv"]) && $_REQUEST["blogpriv"] == 'on') {
    $smarty->assign('blogpriv', 'y');
    $blogpriv='y';
}


if (isset($_REQUEST["preview"])) {
	$parsed_data = $tikilib->apply_postedit_handlers($edit_data);
	$parsed_data = $tikilib->parse_data($parsed_data);

	if ($prefs['blog_spellcheck'] == 'y') {
		if (isset($_REQUEST["spellcheck"]) && $_REQUEST["spellcheck"] == 'on') {
			$parsed_data = $tikilib->spellcheckreplace($edit_data, $parsed_data, $prefs['language'], 'blogedit');

			$smarty->assign('spellcheck', 'y');
		} else {
			$smarty->assign('spellcheck', 'n');
		}
	}

	$smarty->assign('data', TikiLib::htmldecode( $edit_data ) );

	if ($prefs['feature_freetags'] == 'y') {
	$smarty->assign('taglist',$_REQUEST["freetag_string"]);
	}
	$smarty->assign('title', isset($_REQUEST["title"]) ? $_REQUEST['title'] : '');
	$smarty->assign('parsed_data', $parsed_data);
	$smarty->assign('preview', 'y');
}

// remove images (permissions!)
if ((isset($_REQUEST['save']) || isset($_REQUEST['save_exit']))&& $prefs['feature_contribution'] == 'y' && $prefs['feature_contribution_mandatory_blog'] == 'y' && (empty($_REQUEST['contributions']) || count($_REQUEST['contributions']) <= 0)) {
	$contribution_needed = true;
	$smarty->assign('contribution_needed', 'y');
} else {
	$contribution_needed = false;
}

if ((isset($_REQUEST["save"]) || isset($_REQUEST['save_exit'])) && !$contribution_needed) {
	include_once ("lib/imagegals/imagegallib.php");

	$smarty->assign('individual', 'n');

	if ($userlib->object_has_one_permission($_REQUEST["blogId"], 'blog')) {
		$smarty->assign('individual', 'y');

		if ($tiki_p_admin != 'y') {
			// Now get all the permissions that are set for this type of permissions 'image gallery'
			$perms = $userlib->get_permissions(0, -1, 'permName_desc', '', 'blogs');

			foreach ($perms["data"] as $perm) {
				$permName = $perm["permName"];

				if ($userlib->object_has_permission($user, $_REQUEST["blogId"], 'blog', $permName)) {
					$$permName = 'y';
					$smarty->assign("$permName", 'y');
				} else {
					$$permName = 'n';
					$smarty->assign("$permName", 'n');
				}
			}
		}
	}

	if ($tiki_p_blog_admin == 'y') {
		$tiki_p_create_blogs = 'y';

		$smarty->assign('tiki_p_create_blogs', 'y');
		$tiki_p_blog_post = 'y';
		$smarty->assign('tiki_p_blog_post', 'y');
		$tiki_p_read_blog = 'y';
		$smarty->assign('tiki_p_read_blog', 'y');
	}

	if ($tiki_p_blog_post != 'y') {
		$smarty->assign('msg', tra("Permission denied you cannot post"));
		$smarty->display("error.tpl");
		die;
	}

	if ($_REQUEST["postId"] > 0) {
		$data = $bloglib->get_post($_REQUEST["postId"]);

		$blog_data = $tikilib->get_blog($data["blogId"]);

		if ($user && $user == $blog_data["user"]) {
			$data["user"] = $user;
		}

		if ($data["user"] != $user || !$user) {
			if ($tiki_p_blog_admin != 'y') {
				$smarty->assign('msg', tra("Permission denied you cannot edit this post"));
				$smarty->display("error.tpl");
				die;
			}
		}
	}

	$edit_data = $imagegallib->capture_images($edit_data);
	$title = isset($_REQUEST['title']) ? $_REQUEST['title'] : '';
	if ($_REQUEST["postId"] > 0) {
	  $bloglib->update_post($_REQUEST["postId"], $_REQUEST["blogId"], $edit_data, $user, $title, isset($_REQUEST['contributions'])? $_REQUEST['contributions']:'', $data['data'], $blogpriv);
		$postid = $_REQUEST["postId"];
	} else {
	  $postid = $bloglib->blog_post($_REQUEST["blogId"], $edit_data, $user, $title, isset($_REQUEST['contributions'])? $_REQUEST['contributions']:'', $blogpriv);
		$smarty->assign('postId', $postid);
	}

	// TAG Stuff
	$cat_type = 'blog post';
	$cat_objid = $postid;
	$cat_desc = substr($edit_data,0,200);
	$cat_name = $title;
	$cat_href="tiki-view_blog_post.php?postId=".urlencode($postid);
	include_once ("freetag_apply.php");

	if (isset($_REQUEST['save_exit'])) {
		header ("location: tiki-view_blog.php?blogId=$blogId");

		die;
	}

	$parsed_data = $tikilib->apply_postedit_handlers($edit_data);
	$parsed_data = $tikilib->parse_data($parsed_data);

	$smarty->assign('data', TikiLib::htmldecode( $edit_data ) );

	if ($prefs['feature_freetags'] == 'y') {
	$smarty->assign('taglist',$_REQUEST["freetag_string"]);	
	}
	$smarty->assign('title', isset($_REQUEST["title"]) ? $_REQUEST['title'] : '');
	$smarty->assign('parsed_data', $parsed_data);
}

if ($contribution_needed) {
	$smarty->assign('title', $_REQUEST["title"]);
	$smarty->assign('parsed_data', $tikilib->parse_data($_REQUEST['data']));
	$smarty->assign('data', TikiLib::htmldecode( $_REQUEST['data'] ) );
	if ($prefs['feature_freetags'] == 'y') {
		$smarty->assign('taglist',$_REQUEST["freetag_string"]);
	}
}
if ($tiki_p_blog_admin == 'y') {
	$blogsd = $bloglib->list_blogs(0, -1, 'created_desc', '');

	$blogs = $blogsd['data'];
} else {
	$blogs = $bloglib->list_blogs_user_can_post($user);
}

if (count($blogs) == 0) {
	$smarty->assign('msg', tra("You can't post in any blog maybe you have to create a blog first"));

	$smarty->display("error.tpl");
	die;
}

$sameurl_elements = array(
	'offset',
	'sort_mode',
	'where',
	'find',
	'blogId',
	'postId'
);

$smarty->assign_by_ref('blogs', $blogs);

include_once ('tiki-section_options.php');

include_once("textareasize.php");

global $wikilib; include_once('lib/wiki/wikilib.php');
$plugins = $wikilib->list_plugins(true);
$smarty->assign_by_ref('plugins', $plugins);

include_once ('lib/quicktags/quicktagslib.php');
$quicktags = $quicktagslib->list_quicktags(0,-1,'taglabel_desc','','blogs');
$smarty->assign_by_ref('quicktags', $quicktags["data"]);
if ($prefs['feature_contribution'] == 'y') {
	include_once('contribution.php');
}

ask_ticket('blog');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the Index Template
$smarty->assign('mid', 'tiki-blog_post.tpl');
$smarty->display("tiki.tpl");

?>
