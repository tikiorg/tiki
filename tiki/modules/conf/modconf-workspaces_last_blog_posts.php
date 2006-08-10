<?php
/*
* @author: Javier Reyes Gomez (jreyes@escire.com)
* @date: 27/01/2006
* @copyright (C) 2006 Javier Reyes Gomez (eScire.com)
* @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*/
function module_workspaces_last_blog_posts_help() {
	return "List the active workspace blogs, and the posts of the selected blog.";
}
function module_workspaces_last_blog_posts_title() {
	return "Blogs";
}
function module_workspaces_last_blog_posts_params() {
	$param = array();
	$param["name"]=array();
	$param["name"]["name"]="Blog name";
	$param["name"]["help"]="Blog name, you can use %WSCODE% to paste the active workspace code, Eg. %WSCODE%-Blog1";
	$param["name"]["defaultValue"]="Blog";
	$param["maxPosts"]=array();
	$param["maxPosts"]["name"]="Max. posts";
	$param["maxPosts"]["help"]="Maximum number of posts to show";
	$param["maxPosts"]["defaultValue"]="5";
	$param["showBar"]=array();
	$param["showBar"]["name"]="Show list";
	$param["showBar"]["help"]="Show or hide the workspace blog list";
	$param["showBar"]["defaultValue"]="y";
	$param["offset"]=array();
	$param["offset"]["name"]="Page offset";
	$param["offset"]["help"]="Page offset";
	$param["offset"]["defaultValue"]="0";
	$param["offset"]["hide"]="y";
	
	return $param;
}

?>
