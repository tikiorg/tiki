<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-hw_grading_queue.php,v 1.2 2004-06-19 15:25:17 ohertel Exp $

// Copyright (c) 2004 George G. Geller
// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Data:
//   $assignmentId - ro - index into hw_assignments table
//   $assignment_data - rw - a row from hw_assignments
//   $listPages - rw - the rows in hw_pages with assignmentId

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

if (!isset($_REQUEST["assignmentId"])) {
  $smarty->assign('msg', tra("Error: No assignment indicated."));
  $smarty->display("error.tpl");
  die;
}
$assignmentId = $_REQUEST["assignmentId"];

if (!$homeworklib->hw_assignment_fetch(&$assignment_data, $assignmentId)){
  $smarty->assign('msg', tra("Error: assignment not found."));
  $smarty->display("error.tpl");
  die;
}

if (!$homeworklib->hw_pages_list_for_assignment(&$listPages, $assignmentId)){
  $smarty->assign('msg', tra('No entries available yet!'));
  $smarty->display("error.tpl");
  die;
}

// This foreach doesn't work. Maybe because $page is by value rather than by reference?
// foreach($listPages as $page){
//   $page["data"] = $tikilib->parse_data($page["data"]);
// }

$nPages = count($listPages);
for($i = 0; $i < $nPages ; $i++){
   $listPages[$i]["data"] = $tikilib->parse_data($listPages[$i]["data"]);
}

$smarty->assign_by_ref('assignment_data', $assignment_data);
$smarty->assign_by_ref('listPages', $listPages);

// Display the template
$smarty->assign('mid', 'tiki-hw_grading_queue.tpl');
$smarty->assign('page',$assignment_data["title"]);
$smarty->display("tiki.tpl");

?>
