<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-hw_pagehistory.php,v 1.2 2004-02-06 20:47:53 ggeller Exp $

// Copyright (c) 2004 George G. Geller
// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Need tiki-hw_rollback.php

error_reporting(E_ALL);

// Initialization
require_once ('tiki-setup.php');

require_once("lib/homework/homeworklib.php");
$homeworklib = new HomeworkLib($dbTiki);


if ($feature_homework != 'y') {
  $smarty->assign('msg', tra("This feature is disabled").": feature_homework");
  $smarty->display("error.tpl");
  die;
}

if (!isset($_REQUEST["id"])) {
  $smarty->assign('msg', tra("No homework page indicated!"));
  $smarty->display("error.tpl");
  die;
}

$pageId = $_REQUEST["id"];
$smarty->assign('pageId',$pageId);

// Get the page data
$status = $homeworklib->hw_page_fetch_by_id(&$page_data, $pageId, false);
if (!($status == 'HW_OK' || $status == 'HW_PAGE_LOCKED')){
  $smarty->assign('msg', __FILE__.tra(" line ").__LINE__.", \ 
    ".tra("Error: Could not fetch page")." pageId ");
  $smarty->display("error.tpl");
  die;
}
$smarty->assign_by_ref('info', $page_data);

// Get the assignment data
$assignmentId = $page_data['assignmentId'];
$smarty->assign("assignmentId",$assignmentId);
$assignment_data = $homeworklib->get_assignment($assignmentId);
if ($assignment_data == false){
  $smarty->assign('msg', __FILE__.tra(" line ").__LINE__.", \ 
    ".tra("Error: Could not fetch assignment ").$assignmentId);
  $smarty->display("error.tpl");
  die;
}

// Get the assignment title from the hw_assignment table.
$_REQUEST["page"] = $assignment_data["title"];
$page = $_REQUEST["page"];
$smarty->assign_by_ref('page', $_REQUEST["page"]);

// Now check permissions to access this page
if ($tiki_p_hw_student != 'y') {
  $smarty->assign('msg', tra("You must be a student to view this page!"));
  $smarty->display("error.tpl");
  die;
}

// $ggg_tracer->out(__FILE__." line ".__LINE__.' $page_data = ');
// $ggg_tracer->outvar($page_data);
$studentName = $page_data["studentName"];
$smarty->assign("studentName",$studentName);
if ($tiki_p_hw_grader != 'y') {
  if ($user != $page_data["studentName"]){
	$smarty->assign('msg', tra("Students may only view their own work!"));
	$smarty->display("error.tpl");
	die;
  }
}

if (isset($_REQUEST["delete"]) && isset($_REQUEST["hist"])) {
  check_ticket('page-history');
  foreach (array_keys($_REQUEST["hist"])as $version) {
	$homeworklib->hw_page_remove_version($pageId, $version);
  }
}

$smarty->assign('source', 0);

if (isset($_REQUEST['source'])) {
  $ggg_tracer->outln(__FILE__." line: ".__LINE__.' $_REQUEST["source"] = '.$_REQUEST["source"]);
  $smarty->assign('source', $_REQUEST['source']);
  $version = $histlib->get_version($page, $_REQUEST["source"]);
  $smarty->assign('sourcev', nl2br(htmlentities($version["data"])));
}

// If we have to include a preview please show it
$smarty->assign('preview', false);

/*
if (isset($_REQUEST["preview"])) {
  $version = $histlib->get_version($page, $_REQUEST["preview"]);
  $version["data"] = $tikilib->parse_data($version["data"]);
  
  if ($version) {
    $smarty->assign_by_ref('preview', $version);
    
    $smarty->assign_by_ref('version', $_REQUEST["preview"]);
  }
}
*/

$smarty->assign('diff2', 'n');

/*
if (isset($_REQUEST["diff2"])) {
  $diff = $histlib->get_version($page, $_REQUEST["diff2"]);

  $info = $tikilib->get_page_info($page);
  $html = $tikilib->diff2($diff["data"], $info["data"]);
  $smarty->assign('diffdata', $html);
  $smarty->assign('diff2', 'y');
  $smarty->assign_by_ref('version', $_REQUEST["diff2"]);
}
*/

// We are going to change this to "compare" instead of diff
$smarty->assign('diff', false);

/*
if (isset($_REQUEST["diff"])) {
  $diff = $histlib->get_version($page, $_REQUEST["diff"]);

  $diff["data"] = $tikilib->parse_data($diff["data"]);
  $smarty->assign_by_ref('diff', $diff["data"]);
  $info = $tikilib->get_page_info($page);
  $pdata = $tikilib->parse_data($info["data"]);
  $smarty->assign_by_ref('parsed', $pdata);
  $smarty->assign_by_ref('version', $_REQUEST["diff"]);
}
*/

// $history = $histlib->get_page_history($page);
$history = $homeworklib->hw_page_get_history($pageId);
$smarty->assign_by_ref('history', $history);

ask_ticket('page-history');

// Display the template
$smarty->assign('mid', 'tiki-hw_pagehistory.tpl');
$smarty->assign('show_page_bar', 'y');
$smarty->display("tiki.tpl");

?>
