<?php

// Copyright (c) 2004 George G. Geller
// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Data:
//   $assignmentId(ro) - passed in in _REQUEST, index for hw_assignment table
//   $assignmentData(ro) - row from the hw_assignment table example:
// $assignment_data = Array
//  articleId=>2
//  title=>flowers of the world
//  state=>s
//  authorName=>bnuz
//  topicId=>0
//  topicName=>
//  size=>174
//  useImage=>n
//  image_name=>
//  image_type=>
//  image_size=>0
//  image_x=>0
//  image_y=>0
//  image_data=>
//  publishDate=>-1
//  expireDate=>1077620400
//  created=>1074747306
//  heading=>Flowers have always been a symbol of life and happiness, but always feminity...
//  body=>!!!References...
//                 1         2         3
//        12345678901234567890123456789012
//  hash=>d7c19163a18b553e9808d3a2fcd43705
//  author=>admin
//  reads=>0
//  votes=>0
//  points=>0
//  type=>
//  rating=>0.00
//  isfloat=>n
//  avatarLibName=>
//  use_ratings=>
//  show_pre_publ=>
//  show_post_expire=>y
//  heading_only=>
//  allow_comments=>y
//  comment_can_rate_article=>
//  show_image=>y
//  show_avatar=>
//  show_author=>y
//  show_pubdate=>y
//  show_expdate=>
//  show_reads=>y
//  show_size=>y
//  creator_edit=>
//  entrating=>0


// Adapted from tiki-read_article.php
error_reporting (E_ALL);
require_once('doc/devtools/ggg-trace.php');

// Initialization
require_once('tiki-setup.php');

include_once('lib/homework/homeworklib.php');
include_once ('lib/articles/artlib.php'); // GGG remove later
$ggg_tracer->outln(__FILE__." line: ".__LINE__);

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

$article_data = $tikilib->get_article($assignmentId);

$homeworklib = new HomeworkLib($dbTiki);
$assignment_data = $homeworklib->get_assignment($assignmentId);
// $ggg_tracer->out(__FILE__." line ".__LINE__.' $assignment_data = ');
// $ggg_tracer->outvar($assignment_data);

if (!$assignment_data) {
  $smarty->assign('msg', tra("Assignment not found"));
  $smarty->display("error.tpl");
  die;
}

/* $article_data["expireDate"] > date("U") the assignment is overdue */

$smarty->assign('title', $article_data["title"]);
$smarty->assign('title', $assignment_data["title"]);

$smarty->assign('authorName', $article_data["authorName"]);
$smarty->assign('authorName', $assignment_data["authorName"]);

$smarty->assign('topicId', $article_data["topicId"]);
$smarty->assign('topicId', $assignment_data["topicId"]);

$smarty->assign('type', $article_data["type"]);
$smarty->assign('type', $assignment_data["type"]);

$smarty->assign('rating', $article_data["rating"]);
$smarty->assign('rating', $assignment_data["rating"]);

$smarty->assign('entrating', $article_data["entrating"]);
$smarty->assign('entrating', $assignment_data["entrating"]);

$smarty->assign('useImage', $article_data["useImage"]);
$smarty->assign('useImage', $assignment_data["useImage"]);

$smarty->assign('isfloat', $assignment_data["isfloat"]);

$smarty->assign('image_name', $article_data["image_name"]);
$smarty->assign('image_name', $assignment_data["image_name"]);

$smarty->assign('image_type', $article_data["image_type"]);
$smarty->assign('image_type', $assignment_data["image_type"]);

$smarty->assign('image_size', $article_data["image_size"]);
$smarty->assign('image_size', $assignment_data["image_size"]);

$smarty->assign('image_x', $article_data["image_x"]);
$smarty->assign('image_x', $assignment_data["image_x"]);

$smarty->assign('image_y', $article_data["image_y"]);
$smarty->assign('image_y', $assignment_data["image_y"]);

$smarty->assign('image_data', urlencode($article_data["image_data"]));
$smarty->assign('image_data', urlencode($assignment_data["image_data"]));

$smarty->assign('reads', $article_data["reads"]);
$smarty->assign('reads', $assignment_data["reads"]);

$smarty->assign('size', $article_data["size"]);
$smarty->assign('size', $assignment_data["size"]);

if (strlen($article_data["image_data"]) > 0) {
  $smarty->assign('hasImage', 'y');
  $hasImage = 'y';
}

if (strlen($assignment_data["image_data"]) > 0) {
  $smarty->assign('hasImage', 'y');
  $hasImage = 'y';
}

$smarty->assign('heading', $article_data["heading"]);
$smarty->assign('heading', $assignment_data["heading"]);

$_REQUEST['page'] = 1; // GGG remove later
$pages = 1;            // GGG remove later

/*
if (!isset($_REQUEST['page'])){
  $_REQUEST['page'] = 1;
}
$pages = $artlib->get_number_of_pages($article_data["body"]);
$ggg_tracer->outln(__FILE__." line: ".__LINE__.' $pages = '."$pages");
$article_data["body"] = $artlib->get_page($article_data["body"], $_REQUEST['page']);
$smarty->assign('pages', $pages);

if ($pages > $_REQUEST['page']) {
  $smarty->assign('next_page', $_REQUEST['page'] + 1);
} else {
  $smarty->assign('next_page', $_REQUEST['page']);
}

if ($_REQUEST['page'] > 1) {
  $smarty->assign('prev_page', $_REQUEST['page'] - 1);
} else {
  $smarty->assign('prev_page', 1);
}
*/
$smarty->assign('first_page', 1);
$smarty->assign('last_page', $pages);
$smarty->assign('page', $_REQUEST['page']);

// I wonder why it needs both the body and the parsed_body?
$smarty->assign('body', $assignment_data["body"]);

$smarty->assign('dueDate', $assignment_data["expireDate"]);
$smarty->assign('edit_data', 'y');

$body = $assignment_data["body"];
$heading = $assignment_data["heading"];
$smarty->assign('parsed_body', $tikilib->parse_data($body));
$smarty->assign('parsed_heading', $tikilib->parse_data($heading));

$topics = $artlib->list_topics();
$smarty->assign_by_ref('topics', $topics);

// Display the Index Template
$smarty->assign('mid', 'tiki-hw_student_assignment.tpl');
$smarty->assign('show_page_bar', 'n');
$smarty->assign('page', $title);    // Display the assignment title in the browser titlebar
$smarty->display("tiki.tpl");

?>
