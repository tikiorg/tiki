<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-hw_teacher_assignments.php,v 1.4 2004-02-22 14:32:23 ggeller Exp $

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
//   Change the table structure from the article stuff to assignment stuff
//     expireDate => dueDate
//   Put in the ticket-checking stuff.
//   Test with a large number of assignments.

error_reporting(E_ALL);

// require_once("doc/devtools/ggg-trace.php");
// $ggg_tracer->outln(__FILE__." line: ".__LINE__);

// Initialization
require_once ('tiki-setup.php');

require_once("lib/homework/homeworklib.php");

$homeworklib = new HomeworkLib($dbTiki);

if ($feature_homework != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_homework");
	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_hw_teacher != 'y') {
	$smarty->assign('msg', tra("Permission denied you must be a teacher to edit assignments."));
	$smarty->display("error.tpl");
	die;
}

if (isset($_REQUEST["remove"])) {
  // TODO: Add the check ticket stuff
  $homeworklib->hw_assignment_remove($_REQUEST["remove"]);
}

// If offset is set use it if not then use offset =0
if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}

$smarty->assign_by_ref('offset', $offset);

/*
$now = date("U");

if (isset($_SESSION["thedate"])) {
	if ($_SESSION["thedate"] < $now) {
		$pdate = $_SESSION["thedate"];
	} else {
		if ($tiki_p_admin == 'y') {
			$pdate = $_SESSION["thedate"];
		} else {
			$pdate = $now;
		}
	}
} else {
	$pdate = $now;
}
*/

if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}

if (isset($_REQUEST["type"])) {
	$type = $_REQUEST["type"];
} else {
	$type = '';
}

// Get a list of last changes to the Wiki database
$listassignments = $homeworklib->hw_assignments_list(0, $maxArticles);

for ($i = 0; $i < count($listassignments["data"]); $i++) {
	$listassignments["data"][$i]["parsed_heading"] = $tikilib->parse_data($listassignments["data"][$i]["heading"]);
	$listassignments["data"][$i]["show_author"] = 'n';
	$listassignments["data"][$i]["show_expdate"] = 'y';
	$comments_prefix_var='article:';
	$comments_object_var=$listassignments["data"][$i]["articleId"];
	$comments_objectId = $comments_prefix_var.$comments_object_var;
}

$smarty->assign_by_ref('listpages', $listassignments["data"]);

// Display the template
$smarty->assign('mid', 'tiki-hw_teacher_assignments.tpl');
$smarty->display("tiki.tpl");

?>
