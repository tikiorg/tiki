<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-hw_editpage.php,v 1.2 2004-02-05 19:09:59 ggeller Exp $

// Copyright (c) 2004 George G. Geller
// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Adapted from tiki-editpage.php

// Students cannot edit/view another student's page.  This may change in VERSION2 when we do peer reviewers.
// Students cannot edit their own pages after the due date.
// Admin, teachers and graders can edit anyone's page after the due date, no one's before the due date.

error_reporting (E_ALL);

// Initialization
require_once ('tiki-setup.php');

include_once("lib/homework/homeworklib.php");
$homeworklib = new HomeworkLib($dbTiki);

if ($feature_homework != 'y') {
  $smarty->assign('msg', tra("This feature is disabled").": feature_homework");
  $smarty->display("error.tpl");
  die;
}

if ($tiki_p_hw_student != 'y') {
  $smarty->assign('msg', tra("You must be a student to edit homework pages."));
  $smarty->display("error.tpl");
  die;
}

if (!isset($_REQUEST["id"])) {
  $smarty->assign('msg', tra("No assignment indicated"));
  $smarty->display("error.tpl");
  die;
}
$pageId = $_REQUEST["id"];

// Fetch the page try to lock it.
$status = $homeworklib->hw_page_fetch_by_id(&$page_data, $pageId, true);
if ($status == "HW_INVALID_ID"){
  $smarty->assign('msg', tra("Error: Invalid id for hw_pages table."));
  $smarty->display("error.tpl");
  die;
} elseif ($status == "HW_PAGE_LOCKED"){
  $smarty->assign('msg', tra("Error: This page is being edited by another user."));
  $smarty->display("error.tpl");
  die;
}
$ggg_tracer->out(__FILE__." line ".__LINE__.' $page_data = ');
$ggg_tracer->outvar($page_data);

// TODO for VERSION2 - change this for peer review
$studentName = $page_data['studentName'];
if (!$tiki_p_hw_grader && $usr != $studentName /* && !homeworklib->hw_peer_review_permit($user, $pageID) */){
  $smarty->assign('msg', tra("Permission denied: Students may only view or edit their own work."));
  $smarty->display("error.tpl");
  die;
}

$assignmentId = $page_data['assignmentId'];
$smarty->assign('assignmentId',$assignmentId);

$assignment_data = $homeworklib->get_assignment($assignmentId);
$ggg_tracer->out(__FILE__." line ".__LINE__.' $assignment_data = ');
$ggg_tracer->outvar($assignment_data);

// Students cannot edit their own pages after the due date. (unlock the page and exit)
// $ggg_tracer->outln(__FILE__." line ".__LINE__.' date("U") = '.date("U"));
// $ggg_tracer->outln(__FILE__." line ".__LINE__.' $assignment_data["expireDate"] = '.$assignment_data['expireDate']);
// $sdate = date("Ymd G:i:s", $assignment_data["expireDate"]);
// $ggg_tracer->outln(__FILE__." line ".__LINE__.' $sdate = '.$sdate);
if (($tiki_p_hw_grader == 'n') && (date("U") > $assignment_data['expireDate']) ) {
  $homeworklib->hw_page_unlock($pageId);
  $smarty->assign('msg', tra("Permission denied: Students may edit their work after the due date."));
  $smarty->display("error.tpl");
  die;
}

//       1         2         3         4         5         6         7
//34567890123456789012345678901234567890123456789012345678901234567890123456789
// Admin, teachers and graders can edit anyone's page after the due date, no
//   one's before the due date. (unlock this page and exit)
// $ggg_tracer->outln(__FILE__." line ".__LINE__.' $tiki_p_hw_grader = '.$tiki_p_hw_grader);
if (($tiki_p_hw_grader == 'y') && (date("U") < $assignment_data['expireDate'])) {
  $homeworklib->hw_page_unlock($pageId);
  $smarty->assign('msg', tra("Permission denied: The teacher must wait until the due date to edit student\'s work."));
  $smarty->display("error.tpl");
  die;
}

$smarty->assign('homeworkTitle', $assignment_data["title"]);

// $ggg_tracer->out(__FILE__." line ".__LINE__.' $_REQUEST = ');
// $ggg_tracer->outvar($_REQUEST);

$edit_data = $page_data["data"];
$smarty->assign('pagedata',$edit_data);

// No preview for the prototype
$smarty->assign('preview',0);
// If we are in preview mode then preview it!
// if(isset($_REQUEST["preview"])) {
//   $smarty->assign('preview',1); 
// }

if (isset($_REQUEST["save"])) {
  check_ticket('edit-page');

  $edit = $_REQUEST["edit"];
  if ((md5($page_data["data"]) != md5($_REQUEST["edit"]))) {
	$comment = $_REQUEST["comment"];
    $homeworklib->hw_page_update($pageId, $edit, $comment);
  }
  header("location: tiki-hw_page.php?assignmentId=".$page_data['assignmentId']);
  die;
}

// We are creating a new version; it needs a new comment.
$_REQUEST["comment"] = '';

include_once("textareasize.php");

include_once ('lib/quicktags/quicktagslib.php');
$quicktags = $quicktagslib->list_quicktags(0,-1,'taglabel_desc','');
$smarty->assign_by_ref('quicktags', $quicktags["data"]);
ask_ticket('edit-page');

$smarty->assign("pageId",$pageId);

// Display the Index Template
$smarty->assign('mid', 'tiki-hw_editpage.tpl');
$smarty->assign('show_page_bar', 'y');
$smarty->display("tiki.tpl");

?>
