<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-hw_teacher_assignment.php,v 1.2 2004-03-01 02:15:37 ggeller Exp $

// Copyright (c) 2004 George G. Geller
// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Adapted from tiki-read_article.php

error_reporting (E_ALL);

// Initialization
require_once('tiki-setup.php');

include_once('lib/homework/homeworklib.php');
$homeworklib = new HomeworkLib($dbTiki);

if ($feature_homework != 'y') {
  $smarty->assign('msg', tra("This feature is disabled").": feature_homework");
  $smarty->display("error.tpl");
  die;
}

if ($tiki_p_hw_grader != 'y') {
  $smarty->assign('msg', tra("You must be a teacher or grader to access this page."));
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

$assignment_data = $homeworklib->get_assignment($assignmentId); // GGG change to hw_assignment_fetch or something

if (!$assignment_data) {
  $smarty->assign('msg', tra("Assignment not found"));
  $smarty->display("error.tpl");
  die;
}

$smarty->assign('title', $assignment_data["title"]);

$smarty->assign('dueDate', $assignment_data["expireDate"]); // Change expireDate to dueDate

$heading = $assignment_data["heading"];
$smarty->assign('parsed_heading', $tikilib->parse_data($heading));

$body = $assignment_data["body"];
$smarty->assign('parsed_body', $tikilib->parse_data($body));

// Display the Index Template
$smarty->assign('mid', 'tiki-hw_teacher_assignment.tpl');
$smarty->assign('show_page_bar', 'n');
$smarty->assign('page', $title);    // Display the assignment title in the browser titlebar
$smarty->display("tiki.tpl");

?>
