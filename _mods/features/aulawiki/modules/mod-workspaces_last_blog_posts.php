<?php

/*
* @author: Javier Reyes Gomez (jreyes@escire.com)
* @date: 27/01/2006
* @copyright (C) 2006 Javier Reyes Gomez (eScire.com)
* @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*/
//require_once('lib/edu/workspacelib.php');
require_once ('lib/workspaces/resourceslib.php');

//$workspacesLib = new WorkspaceLib($dbTiki);
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}
include_once ('lib/workspaces/workspacelib.php');

global $dbTiki;
global $userlib;
$workspacesLib = new WorkspaceLib($dbTiki);

$workspace = $workspacesLib->get_current_workspace();

if (!isset ($workspace)) {
	$smarty->assign('error_msg', tra("Workspace not selected"));
}else{
	
	include_once ('lib/blogs/bloglib.php');
	$bloglib2 = new BlogLib($dbTiki);
	$resourcesLib = new WorkspaceResourcesLib($dbTiki);
	$blogs = $resourcesLib->get_category_objects($workspace["categoryId"], null, "blog");
	
	if (isset ($blogs) && count($blogs) > 0) {
	
		$blogId = $blogs[0]["objId"];
	
		if (isset($module_params["name"]) && $module_params["name"]!=""){
			foreach($blogs as $key=>$blog){
				if($blog["name"]==$module_params["name"]){
					$blogId = $blog["objId"];
					$smarty->assign('selectedBlog', $blog);
				}
			}
		}

		$maxPosts = 5;
		if (isset($module_params["maxPosts"]) && $module_params["maxPosts"]!=""){
			$maxPosts = $module_params["maxPosts"];
		}
		$offset = 0;
		if (isset($module_params["offset"])){
			$offset = $module_params["offset"];
		}

		$posts = $bloglib2->list_blog_posts($blogId, $offset, $maxPosts, 'created_desc', '', '');

		//Configure Offset
		$cant_pages = ceil($posts["cant"] / $maxPosts);
		$smarty->assign_by_ref('cant_pages', $cant_pages);
		$smarty->assign('actual_page', 1 + ($offset / $maxPosts));
		
		if ($posts["cant"] > ($offset + $maxPosts)) {
			$smarty->assign('next_offset', $offset + $maxPosts);
		} else {
			$smarty->assign('next_offset', -1);
		}
		
		// If offset is > 0 then prev_offset
		if ($offset > 0) {
			$smarty->assign('prev_offset', $offset - $maxPosts);
		} else {
			$smarty->assign('prev_offset', -1);
		}
		
		foreach ($posts["data"] as $key => $post) {
			$posts["data"][$key]["data"] = $tikilib->parse_data($post["data"]);
		}
	
		$smarty->assign_by_ref('modLastBlogPosts', $posts["data"]);
		$smarty->assign_by_ref('workspaceBlogs', $blogs);
		if (count($blogs) == 1) {
			$smarty->assign_by_ref('selectedBlog', $blogs[0]);
		}
	}

	$smarty->assign('showBar', isset ($module_params["showBar"]) ? $module_params["showBar"] : 'y');
	$smarty->assign('nonums', isset ($module_params["nonums"]) ? $module_params["nonums"] : 'y');
	$smarty->assign('body', isset ($module_params["body"]) ? $module_params["body"] : 'y');
}
?>