<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-hw_teacher_assignment_edit.php,v 1.4 2004-03-11 17:12:27 ggeller Exp $

// Copyright (c) 2004 George G. Geller
// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Todo:
//   Check for due dates in the past.
//   Set the page title (for mozilla window.)
//   Preview has to have the assignmentId stored internally somewhere.
//   When editing an existing assignment, overwrite it instead of creating a new one.

error_reporting (E_ALL);

// Requires (among other things):
// templates/tiki-hw_teacher_assignment_edit.tpl
// templates/tiki-hw_teacher_assignment_edit_preview.tpl

// Bugs:
//   This feature should be known as Writers' Workshop rather than homework.

// Initialization
require_once ('tiki-setup.php');

require_once ('lib/homework/homeworklib.php');
$homeworklib = new HomeworkLib($dbTiki);

$dc = &$tikilib->get_date_converter($user);

if ($feature_homework != 'y') {
  $smarty->assign('msg', tra("This feature is disabled").": feature_homework");
  $smarty->display("error.tpl");
  die;
}

// Now check permissions to access this page
if ($tiki_p_hw_teacher != 'y') {
  $smarty->assign('msg', tra("Permission denied: You must be a teacher to edit assignments."));
  $smarty->display("error.tpl");
  die;
}

// Initialize the data
$assignment_data = array();
$fields = array(
		"assignmentId",
		"title",
		"teacherName",
		"created",
		"dueDate",
		"modified",
		"heading",
		"body");
foreach ($fields as $f) {
  $assignment_data[$f] = "";
}

if (isset($_REQUEST["assignmentId"])) {
  $assignment_data["assignmentId"] = $_REQUEST["assignmentId"];
} else {
  $assignment_data["assignmentId"] = 0;
}

//
// Save
//
if (isset($_REQUEST["save"])) {
  $assignment_data["title"] = strip_tags($_REQUEST["title"]);
  $assignment_data["teacherName"] = $user;
  if ($assignment_data["assignmentId"] == 0){
	$assignment_data["created"] = date("U");
  }
  $assignment_data["dueDate"] = $dc->getServerDateFromDisplayDate(mktime($_REQUEST["Time_Hour"],
    $_REQUEST["Time_Minute"], 0, $_REQUEST["Date_Month"], $_REQUEST["Date_Day"], 
    $_REQUEST["Date_Year"]));
  $assignment_data["modified"] = date("U");
  $assignment_data["heading"] = $_REQUEST["heading"];
  $assignment_data["body"] = $_REQUEST["body"];
  $homeworklib->hw_assignment_store(&$assignment_data);
  // TODO: check for error here?
  header ("location: tiki-hw_teacher_assignments.php");
  die;
}

//
// Preview, create, and edit
//
if (isset($_REQUEST["preview"])) {
  $smarty->assign('preview', 1);
  $assignment_data["dueDate"] = $dc->getServerDateFromDisplayDate(mktime($_REQUEST["Time_Hour"],
    $_REQUEST["Time_Minute"], 0, $_REQUEST["Date_Month"], $_REQUEST["Date_Day"], 
    $_REQUEST["Date_Year"]));
  $assignment_data["title"] = $_REQUEST["title"];
  $assignment_data["parsed_title"] = $tikilib->parse_data($assignment_data["title"]);
  $assignment_data["heading"] = $_REQUEST["heading"];;
  $assignment_data["parsed_heading"] = $tikilib->parse_data($assignment_data["heading"]);
  $assignment_data["body"] = $_REQUEST["body"];
  $assignment_data["parsed_body"] = $tikilib->parse_data($assignment_data["body"]);
}
else { // Create new, or edit old assignment
  $smarty->assign('preview', 0);
  if ($assignment_data["assignmentId"] == 0){ // create a new assignment
	// Default due date is 2 AM seven days from now.
	$cur_time = getdate();
	$assignment_data["dueDate"] = mktime (2, 0, 0, $cur_time["mon"], $cur_time["mday"]+7,
                                           $cur_time["year"]);
  }
  else { // edit an existing assignment
	// $assignment_data = array();
	// If hw_assignment_fetch might fail is a bogus id is passed in.
	if (!$homeworklib->hw_assignment_fetch(&$assignment_data, $assignment_data["assignmentId"])){
	  $msg = __FILE__." line: ".__LINE__.tra(" error: Can not find assignment ").$assignment_data["assignmentId"].".";
	  $smarty->assign('msg', tra($msg));
	  $smarty->display("error.tpl");
	  die;
	}

//  	print('$assignment_data = ');
//  	print_r($assignment_data);
//  	die;
  }
}

$smarty->assign_by_ref("assignment_data",$assignment_data);

include_once("textareasize.php");
include_once ('lib/quicktags/quicktagslib.php');
$quicktags = $quicktagslib->list_quicktags(0,100,'taglabel_desc','');
$smarty->assign_by_ref('quicktags', $quicktags["data"]);

$smarty->assign('mid', 'tiki-hw_teacher_assignment_edit.tpl');
$smarty->assign('show_page_bar', 'n'); // Do not show the wiki-specific tiki-page_bar.tpl
$smarty->display("tiki.tpl");
?>
