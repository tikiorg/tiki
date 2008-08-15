<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-user_bookmarks.php,v 1.20 2007-10-12 07:55:32 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
$section = 'mytiki';
require_once ('tiki-setup.php');
if ($prefs['feature_ajax'] == "y") {
require_once ('lib/ajax/ajaxlib.php');
}
include_once ('lib/bookmarks/bookmarklib.php');

if ($tiki_p_create_bookmarks != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("You do not have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}

if (!$user) {
	$smarty->assign('msg', tra("You must log in to use this feature"));

	$smarty->display("error.tpl");
	die;
}

if ($prefs['feature_user_bookmarks'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_user_bookmarks");

	$smarty->display("error.tpl");
	die;
}
if (!isset($_REQUEST["parentId"])) {
	$_REQUEST["parentId"] = 0;
}

if ($_REQUEST["parentId"]) {
	$path = $bookmarklib->get_folder_path($_REQUEST["parentId"], $user);

	$p_info = $bookmarklib->get_folder($_REQUEST["parentId"], $user);
	$father = $p_info["parentId"];
} else {
	$path = tra("TOP");

	$father = 0;
}

$smarty->assign('parentId', $_REQUEST["parentId"]);
$smarty->assign('path', $path);

//chekck for edit folder
if (isset($_REQUEST["editfolder"])) {
	$folder_info = $bookmarklib->get_folder($_REQUEST["editfolder"], $user);
} else {
	$folder_info["name"] = '';

	$_REQUEST["editfolder"] = 0;
}

$smarty->assign('foldername', $folder_info["name"]);
$smarty->assign('editfolder', $_REQUEST["editfolder"]);

if (isset($_REQUEST["editurl"])) {
	$url_info = $bookmarklib->get_url($_REQUEST["editurl"]);
} else {
	$url_info["name"] = '';

	$url_info["url"] = '';
	$_REQUEST["editurl"] = 0;
}

$smarty->assign('urlname', $url_info["name"]);
$smarty->assign('urlurl', $url_info["url"]);
$smarty->assign('editurl', $_REQUEST["editurl"]);

// Create a folder inside the parentFolder here
if (isset($_REQUEST["addfolder"])) {
	check_ticket('user-bookmarks');
	if ($_REQUEST["editfolder"]) {
		$bookmarklib->update_folder($_REQUEST["editfolder"], $_REQUEST["foldername"], $user);

		$smarty->assign('editfolder', 0);
		$smarty->assign('foldername', '');
	} else {
		$bookmarklib->add_folder($_REQUEST["parentId"], $_REQUEST["foldername"], $user);
	}
}

if (isset($_REQUEST["removefolder"])) {
	check_ticket('user-bookmarks');
	$bookmarklib->remove_folder($_REQUEST["removefolder"], $user);
}

if (isset($_REQUEST["refreshurl"])) {
	check_ticket('user-bookmarks');
	$bookmarklib->refresh_url($_REQUEST["refreshurl"]);
}

if (isset($_REQUEST["addurl"])) {
	check_ticket('user-bookmarks');
	$urlid
		= $bookmarklib->replace_url($_REQUEST["editurl"], $_REQUEST["parentId"], $_REQUEST["urlname"], $_REQUEST["urlurl"], $user);

	if ($_REQUEST["editurl"] == 0 && $tiki_p_cache_bookmarks == 'y') {
		$bookmarklib->refresh_url($urlid);
	}

	$smarty->assign('editurl', 0);
	$smarty->assign('urlname', '');
	$smarty->assign('urlurl', '');
}

if (isset($_REQUEST["removeurl"])) {
	check_ticket('user-bookmarks');
	$bookmarklib->remove_url($_REQUEST["removeurl"], $user);
}

$urls = $bookmarklib->list_folder($_REQUEST["parentId"], 0, -1, 'name_asc', '', $user);
$smarty->assign('urls', $urls["data"]);
$folders = $bookmarklib->get_child_folders($_REQUEST["parentId"], $user);
$pf = array(
	"name" => "..",
	"folderId" => $father,
	"parentId" => 0,
	"user" => $user
);

$pfs = array($pf);

if ($_REQUEST["parentId"]) {
	$folders = array_merge($pfs, $folders);
}

$smarty->assign('folders', $folders);

include_once ('tiki-mytiki_shared.php');

ask_ticket('user-bookmarks');

include_once('tiki-section_options.php');
if ($prefs['feature_ajax'] == "y") {
function user_bookmarks_ajax() {
    global $ajaxlib, $xajax;
    $ajaxlib->registerTemplate("tiki-user_bookmarks.tpl");
    $ajaxlib->registerTemplate("tiki-my_tiki.tpl");
    $ajaxlib->registerFunction("loadComponent");
    $ajaxlib->processRequests();
}
user_bookmarks_ajax();
$smarty->assign("mootab",'y');
}
// Display the template
$smarty->assign('mid', 'tiki-user_bookmarks.tpl');
$smarty->display("tiki.tpl");

?>
