<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-hw_student_assignments.php,v 1.3 2004-02-22 14:35:49 ggeller Exp $

// Copyright (c) 2004, George Geller
// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

error_reporting(E_ALL);

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/articles/artlib.php');
include_once("lib/commentslib.php");

require_once("lib/homework/homeworklib.php");

$homeworklib = new HomeworkLib($dbTiki);

$commentslib = new Comments($dbTiki);

if ($feature_homework != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_homework");
	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_hw_student != 'y') {
	$smarty->assign('msg', tra("Permission denied you must be a student to view assignments."));
	$smarty->display("error.tpl");
	die;
}

// If offset is set use it if not then use offset =0
// use the maxRecords php variable to set the limit
// if sortMode is not set then use lastModif_desc
if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}

$smarty->assign_by_ref('offset', $offset);

$now = date("U");
$pdate = $now;

$sort_mode = 'publishDate_desc'; // stupid - they are sorted by due date!
$find = '';                      // stupid - this variable is not used.
$type = '';                      // stupid - this variable is not used.
$topic = '';                     // stupid - this variable is not used.

$listpages = $homeworklib->hw_assignments_list(0, $maxArticles, $sort_mode, $find, $pdate, $user, $type, $topic);

for ($i = 0; $i < count($listpages["data"]); $i++) {
  $listpages["data"][$i]["parsed_heading"] = $tikilib->parse_data($listpages["data"][$i]["heading"]);
  $listpages["data"][$i]["show_author"] = 'n';
  $listpages["data"][$i]["show_expdate"] = 'y';
  $comments_prefix_var='article:';
  $comments_object_var=$listpages["data"][$i]["articleId"];
  $comments_objectId = $comments_prefix_var.$comments_object_var;
  $listpages["data"][$i]["comments_cant"] = $commentslib->count_comments($comments_objectId);
}

$topics = $artlib->list_topics();
$smarty->assign_by_ref('topics', $topics);

// If there're more records then assign next_offset
$smarty->assign_by_ref('listpages', $listpages["data"]);
$section = 'cms';
include_once ('tiki-section_options.php');

// Display the template
$smarty->assign('mid', 'tiki-hw_student_assignments.tpl');
$smarty->display("tiki.tpl");

?>
