<?php

/*
* @author: Javier Reyes Gomez (jreyes@escire.com)
* @date: 27/01/2006
* @copyright (C) 2006 Javier Reyes Gomez (eScire.com)
* @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*/
//require_once('lib/edu/workspacelib.php');
require_once ('lib/aulawiki/categutillib.php');

//$workspacesLib = new WorkspaceLib($dbTiki);
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}
include_once ('lib/aulawiki/workspacelib.php');

global $dbTiki;
global $userlib;
$workspacesLib = new WorkspaceLib($dbTiki);

$workspace = $workspacesLib->get_current_workspace();

if (!isset ($workspace)) {
	$smarty->assign('error_msg', tra("Workspace not selected"));
}else{
	
	include_once ('lib/blogs/bloglib.php');
	$bloglib2 = new BlogLib($dbTiki);
	$categUtil = new CategUtilLib($dbTiki);
	$blogs = $categUtil->get_category_objects($workspace["categoryId"], null, "blog");
	
	if (isset ($blogs) && count($blogs) > 0) {
	
	
		$blogId = $blogs[0]["objId"];
	
		if (isset($module_params["name"]) && $module_params["name"]!=""){
			$blogName = $workspace["code"]."-".$module_params["name"];
			foreach($blogs as $blogId=>$blog){
				if($blog["name"]==$blogName){
					$blogId = $blog["objId"];
				}
			}
		}
		$smarty->assign('selectedBlogId', $blogId);
		$posts = $bloglib2->list_blog_posts($blogId, $offset = 0, $module_rows, 'created_desc', '', '');
	
		foreach ($posts["data"] as $key => $post) {
			$posts["data"][$key]["data"] = $tikilib->parse_data($post["data"]);
		}
	
		$smarty->assign('modLastBlogPosts', $posts["data"]);
		$smarty->assign('workspaceBlogs', $blogs);
		$smarty->assign('ownurl', $tikilib->httpPrefix().$_SERVER["REQUEST_URI"]);
	}
	
	$smarty->assign('nonums', isset ($module_params["nonums"]) ? $module_params["nonums"] : 'y');
	$smarty->assign('body', isset ($module_params["body"]) ? $module_params["body"] : 'y');
}
?>