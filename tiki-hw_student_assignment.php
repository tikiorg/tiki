<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-hw_student_assignment.php,v 1.3 2004-04-26 18:57:08 ggeller Exp $

// Copyright (c) 2004 George G. Geller
// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Data:
//   $assignmentId(ro)   - passed in in _REQUEST, index for hw_assignment table
//   $assignmentData(ro) - row from the hw_assignment table example:

// Adapted from tiki-read_article.php
error_reporting (E_ALL);

// Initialization
require_once('tiki-setup.php');

include_once('lib/homework/homeworklib.php');
include_once ('lib/articles/artlib.php'); // GGG remove later

if ($feature_homework != 'y') {
  $smarty->assign('msg', tra("This feature is disabled").": feature_homework");
  $smarty->display("error.tpl");
  die;
}

if ($tiki_p_hw_student != 'y') {
  $smarty->assign('msg', tra("You must be a student to view homework assignments."));
  $smarty->display("error.tpl");
  die;
}

if (!isset($_REQUEST["assignmentId"])) {
  $smarty->assign('msg', tra("No assignment indicated"));
  $smarty->display("error.tpl");
  die;
}

$assignmentId = $_REQUEST["assignmentId"];

$smarty->assign('assignmentId', $assignmentId);

$homeworklib = new HomeworkLib($dbTiki);
if (!$homeworklib->hw_assignment_fetch(&$assignment_data, $assignmentId)){
  $smarty->assign('msg', tra("Assignment not found"));
  $smarty->display("error.tpl");
  die;
}

$smarty->assign('title', $assignment_data["title"]);

$body = $assignment_data["body"];
$smarty->assign('parsed_body', $tikilib->parse_data($body));
$heading = $assignment_data["heading"];
$smarty->assign('parsed_heading', $tikilib->parse_data($heading));

// Display the Index Template
$smarty->assign('mid', 'tiki-hw_student_assignment.tpl');
$smarty->assign('page', $title);    // Display the assignment title in the browser titlebar
$smarty->display("tiki.tpl");

?>
