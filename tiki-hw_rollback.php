<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-hw_rollback.php,v 1.1 2004-02-07 17:08:08 ggeller Exp $

// Copyright (c) 2004 George G. Geller
// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

require_once ('lib/homework/homeworklib.php');
$homeworklib = new HomeworkLib($dbTiki);

if ($feature_homework != 'y') {
  $smarty->assign('msg', tra("This feature is disabled").": feature_homework");
  $smarty->display("error.tpl");
  die;
}

// Now check permissions to access this page
if ($tiki_p_hw_teacher != 'y') {
  $smarty->assign('msg', tra("Permission denied: You cannot rollback this page."));
  $smarty->display("error.tpl");
  die;
}

// Get the page id (index into hw_pages table)
if (!isset($_REQUEST["id"])) {
  $smarty->assign('msg', tra("No hw page indicated"));
  $smarty->display("error.tpl");
  die;
}
$pageId = $_REQUEST["id"];
$smarty->assign('pageId', $pageId);

if (!isset($_REQUEST["version"])) {
  $smarty->assign('msg', tra("No version indicated"));
  $smarty->display("error.tpl");
  die;
}
$version = $_REQUEST["version"];
$smarty->assign('version', $version);

// If the page doesn't exist then display an error
if (!$homeworklib->hw_page_exists($pageId)) {
  $smarty->assign('msg', tra("Page cannot be found"));
  $smarty->display("error.tpl");
  die;
}

if (!$homeworklib->hw_page_version_exists($pageId, $version)) {
  $smarty->assign('msg', tra("Error: nonexistant version specified."));
  $smarty->display("error.tpl");
  die;
}

// Get the page data and lock it
$status = $homeworklib->hw_page_fetch_by_id(&$page_data, $pageId, true);
if (!($status == 'HW_OK')){
  $smarty->assign('msg', __FILE__.tra(" line ").__LINE__.",
    ".tra("Error: Could not fetch page")." $pageId ");
  $smarty->display("error.tpl");
  die;
}
$smarty->assign('studentName', $page_data["studentName"]);

// Get the assignment data
$assignmentId = $page_data['assignmentId'];
$smarty->assign("assignmentId",$assignmentId);
$status = $homeworklib->hw_assignment_fetch(&$assignment_data, $assignmentId);
if ($assignment_data == false){
  $smarty->assign('msg', __FILE__.tra(" line ").__LINE__.",
    ".tra("Error: Could not fetch assignment ").$assignmentId);
  $smarty->display("error.tpl");
  die;
}
$smarty->assign('page',$assignment_data["title"]);

$version = $homeworklib->hw_page_get_version($pageId, $version);
$version["data"] = $tikilib->parse_data($version["data"]);
$smarty->assign_by_ref('preview', $version);

if (isset($_REQUEST["rollback"])) {
  check_ticket('rollback');
  $homeworklib->hw_page_use_version($_REQUEST["id"], $_REQUEST["version"]);
  $assignmentId = $_REQUEST["assignmentId"];
  $studentName = $_REQUEST["student"];
  header ("location: tiki-hw_page.php?assignmentId=$assignmentId&student=$studentName");
  die;
}

ask_ticket('rollback');

$smarty->assign('mid', 'tiki-hw_rollback.tpl');
$smarty->assign('show_page_bar', 'y');
$smarty->display("tiki.tpl");

?>
