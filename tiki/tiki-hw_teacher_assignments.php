<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-hw_teacher_assignments.php,v 1.6 2004-03-12 20:58:26 ggeller Exp $

// Copyright (c) 2004, George G. Geller
// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Todo: 
//   Show how many items are ready to grade
//     if due date is passed.
//     if items are available for grading.
//     link to tiki-hw_teacher_grading_que.php?assignmentId=n
//   Replace $listpages with $listassignments
//   Put in the ticket-checking stuff.
//   Test with a large number of assignments and with zero assignments

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

if ($tiki_p_hw_grader != 'y') {
  $smarty->assign('msg', tra("Permission denied: you must be a teacher to access this page."));
  $smarty->display("error.tpl");
  die;
}

if (isset($_REQUEST["remove"])) {
  $homeworklib->hw_assignment_remove($_REQUEST["remove"]);
}

// Get a list of last changes to the Wiki database
$listassignments = $homeworklib->hw_assignments_list(0);

for ($i = 0; $i < $listassignments["cant"]; $i++) {
  $listassignments["data"][$i]["parsed_heading"] = $tikilib->parse_data($listassignments["data"][$i]["heading"]);
}

$smarty->assign_by_ref('listassignments', $listassignments["data"]);

// Display the template
$smarty->assign('mid', 'tiki-hw_teacher_assignments.tpl');
$smarty->display("tiki.tpl");

?>
