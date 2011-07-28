<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

include_once('tiki-setup.php');
require_once("lib/pear/XML/Server.php");
include_once('lib/blogs/bloglib.php');
if($prefs['feature_xmlrpc'] != 'y') {
  die;  
}

// Build map using webservices
$map = array(
	"blogger.newPost" => array("function" => "newPost"),
	"blogger.getUserInfo" => array("function" => "getUserInfo"),
	"blogger.getPost" => array("function" => "getPost"),
	"blogger.editPost" => array("function" => "editPost"),
	"blogger.deletePost" => array("function" => "deletePost"),
	"blogger.getRecentPosts" => array("function" => "getRecentPosts"),
	"blogger.getUserInfo" => array("function" => "getUserInfo"),
	"blogger.getUsersBlogs" => array("function" => "getUserBlogs")
);

$s = new XML_RPC_Server($map);

function check_individual($user, $blogid, $permName) {
	global $userlib;

	// If the user is admin he can do everything
	if ($userlib->user_has_permission($user, 'tiki_p_blog_admin'))
		return true;

	// If no individual permissions for the object then ok
	if (!$userlib->object_has_one_permission($blogid, 'blog'))
		return true;

	// If the object has individual permissions then check
	// Now get all the permissions that are set for this type of permissions 'image gallery'
	if ($userlib->object_has_permission($user, $blogId, 'blog', $permName)) {
		return true;
	} else {
		return false;
	}
}

/* Validates the user and returns user information */
function getUserInfo($params) {
	global $tikilib, $userlib;

	$appkeyp = $params->getParam(0);
	$appkey = $appkeyp->scalarval();
	$usernamep = $params->getParam(1);
	$username = $usernamep->scalarval();
	$passwordp = $params->getParam(2);
	$password = $passwordp->scalarval();
	list($ok, $username, $e) = $userlib->validate_user($username,$password,'','');

	if ($ok) {
		$myStruct = new XML_RPC_Value(array(
			"nickname" => new XML_RPC_Value($username),
			"firstname" => new XML_RPC_Value("none"),
			"lastname" => new XML_RPC_Value("none"),
			"email" => new XML_RPC_Value("none"),
			"userid" => new XML_RPC_Value("$username"),
			"url" => new XML_RPC_Value("none")
		), "struct");

		return new XML_RPC_Response($myStruct);
	} else {
		return new XML_RPC_Response(0, 101, "Invalid username or password");
	}
}

/* Posts a new submission to the CMS */
function newPost($params) {
	global $tikilib, $userlib, $bloglib;

	$appkeyp = $params->getParam(0);
	$appkey = $appkeyp->scalarval();
	$blogidp = $params->getParam(1);
	$blogid = $blogidp->scalarval();
	$usernamep = $params->getParam(2);
	$username = $usernamep->scalarval();
	$passwordp = $params->getParam(3);
	$password = $passwordp->scalarval();
	$passp = $params->getParam(4);
	$content = $passp->scalarval();
	$passp = $params->getParam(5);
	$publish = $passp->scalarval();

	// Now check if the user is valid and if the user can post a submission
	list($ok, $username, $e) = $userlib->validate_user($username,$password,'','');
	if (!$ok) {
		return new XML_RPC_Response(0, 101, "Invalid username or password");
	}

	// Get individual permissions for this weblog if they exist
	if (!check_individual($username, $blogid, 'tiki_p_blog_post')) {
		return new XML_RPC_Response(0, 101, "User is not allowed to post to this weblog due to individual restrictions for this weblog");
	}

	// If the blog is not public then check if the user is the owner
	if (!$userlib->user_has_permission($username, 'tiki_p_blog_admin')) {
		if (!$userlib->user_has_permission($username, 'tiki_p_blog_post')) {
			return new XML_RPC_Response(0, 101, "User is not allowed to post");
		}

		require_once('lib/blogs/bloglib.php');
		$blog_info = $bloglib->get_blog($blogid);

		if ($blog_info["public"] != 'y') {
			if ($username != $blog_info["user"]) {
				return new XML_RPC_Response(0, 101, "User is not allowed to post");
			}
		}
	}

	// User ok and can submit then submit the post
	$id = $bloglib->blog_post($blogid, $content, $username);

	return new XML_RPC_Response(new XML_RPC_Value("$id"));
}

// :TODO: editPost
function editPost($params) {
	global $tikilib, $userlib, $bloglib;

	$appkeyp = $params->getParam(0);
	$appkey = $appkeyp->scalarval();
	$blogidp = $params->getParam(1);
	$postid = $blogidp->scalarval();
	$usernamep = $params->getParam(2);
	$username = $usernamep->scalarval();
	$passwordp = $params->getParam(3);
	$password = $passwordp->scalarval();
	$passp = $params->getParam(4);
	$content = $passp->scalarval();
	$passp = $params->getParam(5);
	$publish = $passp->scalarval();

	// Now check if the user is valid and if the user can post a submission
	list($ok, $username, $e) = $userlib->validate_user($username,$password,'','');
	if (!$ok) {
		return new XML_RPC_Response(0, 101, "Invalid username or password");
	}

	if (!check_individual($username, $blogid, 'tiki_p_blog_post')) {
		return new XML_RPC_Response(0,
			101, "User is not allowed to post to this weblog due to individual restrictions for this weblog therefor the user cannot edit a post");
	}

	if (!$userlib->user_has_permission($username, 'tiki_p_blog_post')) {
		return new XML_RPC_Response(0, 101, "User is not allowed to post");
	}

	// Now get the post information
	$post_data = $bloglib->get_post($postid);

	if (!$post_data) {
		return new XML_RPC_Response(0, 101, "Post not found");
	}

	if ($post_data["user"] != $username) {
		if (!$userlib->user_has_permission($username, 'tiki_p_blog_admin')) {
			return new XML_RPC_Response(0, 101, "Permission denied to edit that post since the post does not belong to the user");
		}
	}

	$id = $bloglib->update_post($postid, $blogid, $content, $username);
	return new XML_RPC_Response(new XML_RPC_Value(1, "boolean"));
}

// :TODO: deletePost
function deletePost($params) {
	global $tikilib, $userlib, $bloglib;

	$appkeyp = $params->getParam(0);
	$appkey = $appkeyp->scalarval();
	$blogidp = $params->getParam(1);
	$postid = $blogidp->scalarval();
	$usernamep = $params->getParam(2);
	$username = $usernamep->scalarval();
	$passwordp = $params->getParam(3);
	$password = $passwordp->scalarval();
	$passp = $params->getParam(4);
	$publish = $passp->scalarval();

	// Now check if the user is valid and if the user can post a submission
	list($ok, $username, $e) = $userlib->validate_user($username,$password,'','');
	if (!$ok) {
		return new XML_RPC_Response(0, 101, "Invalid username or password");
	}

	// Now get the post information
	$post_data = $bloglib->get_post($postid);

	if (!$post_data) {
		return new XML_RPC_Response(0, 101, "Post not found");
	}

	if ($post_data["user"] != $username) {
		if (!$userlib->user_has_permission($username, 'tiki_p_blog_admin')) {
			return new XML_RPC_Response(0, 101, "Permission denied to edit that post");
		}
	}

	$id = $bloglib->remove_post($postid);
	return new XML_RPC_Response(new XML_RPC_Value(1, "boolean"));
}

// :TODO: getTemplate

// :TODO: setTemplate

// :TODO: getPost
function getPost($params) {
	global $tikilib, $userlib, $bloglib;

	$appkeyp = $params->getParam(0);
	$appkey = $appkeyp->scalarval();
	$blogidp = $params->getParam(1);
	$postid = $blogidp->scalarval();
	$usernamep = $params->getParam(2);
	$username = $usernamep->scalarval();
	$passwordp = $params->getParam(3);
	$password = $passwordp->scalarval();

	// Now check if the user is valid and if the user can post a submission
	list($ok, $username, $e) = $userlib->validate_user($username,$password,'','');
	if (!$ok) {
		return new XML_RPC_Response(0, 101, "Invalid username or password");
	}

	if (!check_individual($username, $blogid, 'tiki_p_blog_post')) {
		return new XML_RPC_Response(0, 101, "User is not allowed to post to this weblog due to individual restrictions for this weblog");
	}

	if (!$userlib->user_has_permission($username, 'tiki_p_blog_post')) {
		return new XML_RPC_Response(0, 101, "User is not allowed to post");
	}

	if (!$userlib->user_has_permission($username, 'tiki_p_read_blog')) {
		return new XML_RPC_Response(0, 101, "Permission denied to read this blog");
	}

	// Now get the post information
	$post_data = $bloglib->get_post($postid);

	if (!$post_data) {
		return new XML_RPC_Response(0, 101, "Post not found");
	}

	$dateCreated = $tikilib->get_iso8601_datetime($post_data["created"]);
	// added dateTime type for blogger compliant xml tag Joerg Knobloch <joerg@happypenguins.net>
	$myStruct = new XML_RPC_Value(array(
		"userid" => new XML_RPC_Value($username),
		"dateCreated" => new XML_RPC_Value($dateCreated, "dateTime.iso8601"),
		"content" => new XML_RPC_Value($post_data["data"]),
		"postid" => new XML_RPC_Value($post_data["postId"])
	), "struct");

	// User ok and can submit then submit an article
	return new XML_RPC_Response($myStruct);
}

// :TODO: getRecentPosts
function getRecentPosts($params) {
	global $tikilib, $userlib, $bloglib;

	$appkeyp = $params->getParam(0);
	$appkey = $appkeyp->scalarval();
	$blogidp = $params->getParam(1);
	$blogid = $blogidp->scalarval();
	$usernamep = $params->getParam(2);
	$username = $usernamep->scalarval();
	$passwordp = $params->getParam(3);
	$password = $passwordp->scalarval();
	$passp = $params->getParam(4);
	$number = $passp->scalarval();

	// Now check if the user is valid and if the user can post a submission
	list($ok, $username, $e) = $userlib->validate_user($username,$password,'','');
	if (!$ok) {
		return new XML_RPC_Response(0, 101, "Invalid username or password");
	}

	if (!check_individual($username, $blogid, 'tiki_p_blog_post')) {
		return new XML_RPC_Response(0,
			101, "User is not allowed to post to this weblog due to individual restrictions for this weblog therefore the user cannot edit a post");
	}

	if (!$userlib->user_has_permission($username, 'tiki_p_blog_post')) {
		return new XML_RPC_Response(0, 101, "User is not allowed to post");
	}

	// Now get the post information
	$posts = $bloglib->list_blog_posts($blogid, false, 0, $number, 'created_desc', '', '');

	if (count($posts) == 0) {
		return new XML_RPC_Response(0, 101, "No posts");
	}

	$arrayval = array();

	foreach ($posts["data"] as $post) {
		$dateCreated = $tikilib->get_iso8601_datetime($post["created"]);

		$myStruct = new XML_RPC_Value(array(
			"userid" => new XML_RPC_Value($username),
			"dateCreated" => new XML_RPC_Value($dateCreated, "dateTime.iso8601"),
			"content" => new XML_RPC_Value($post["data"]),
			"postid" => new XML_RPC_Value($post["postId"])
		), "struct");

		$arrayval[] = $myStruct;
	}

	// User ok and can submit then submit an article
	$myVal = new XML_RPC_Value($arrayval, "array");
	return new XML_RPC_Response($myVal);
}

// :TODO: tiki.tikiPost

/* Get the topics where the user can post a new */
function getUserBlogs($params) {
	global $tikilib, $userlib, $bloglib;

	$appkeyp = $params->getParam(0);
	$appkey = $appkeyp->scalarval();
	$usernamep = $params->getParam(1);
	$username = $usernamep->scalarval();
	$passwordp = $params->getParam(2);
	$password = $passwordp->scalarval();

	$arrayVal = array();

	global $bloglib; require_once('lib/blogs/bloglib.php');
	$blogs = $bloglib->list_user_blogs($username, true);
	$foo = parse_url($_SERVER["REQUEST_URI"]);
	$foo1 = $tikilib->httpPrefix(). str_replace("xmlrpc", "tiki-view_blog", $foo["path"]);

	foreach ($blogs as $blog) {
		$myStruct = new XML_RPC_Value(array(
			"blogName" => new XML_RPC_Value($blog["title"]),
			"url" => new XML_RPC_Value($foo1 . "?blogId=" . $blog["blogId"]),
			"blogid" => new XML_RPC_Value($blog["blogId"])
		), "struct");

		$arrayVal[] = $myStruct;
	}

	$myVal = new XML_RPC_Value($arrayVal, "array");
	return new XML_RPC_Response($myVal);
}
