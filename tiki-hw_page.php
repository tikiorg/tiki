<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-hw_page.php,v 1.3 2004-02-22 14:56:50 ggeller Exp $

// 20040207 - Fix typo in error message

// Initialization

// Copyright (c) 2004 George G. Geller
// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Adapted from tiki-index.php
//
// This is where the student views his work on a wiki-like page.
// Access is by the student's login and by assignmentId.
//
// RFEs for version 2:
//   submit page for grading
//   Show/hide assignment -- need to adapt the preview mode from
//     tiki-hw_teacher_assignment_edit.php
//
// Wiki features needed: history, editing, comments
//
// Wiki features not needed: structures, backlinks, rollbacks, wiki-style lock,
//   notepad, undos, slides, theme control, custom layout, attachments,
//   footnotes, watches, hawhaw (mobility), catagories.

error_reporting (E_ALL);

require_once('tiki-setup.php');

require_once('lib/homework/homeworklib.php');
$homeworklib = new HomeworkLib($dbTiki);

if($feature_homework != 'y') {
  $smarty->assign('msg', tra("This feature is disabled").": feature_homework");
  $smarty->display("error.tpl");
  die;
}

if($tiki_p_hw_student != 'y') {
  $smarty->assign('msg', tra("You must be a student to view this page."));
  $smarty->display("error.tpl");
  die;  
}

// For teacher, graders, and admin the student must be specified
if($tiki_p_hw_grader == 'y') {
  if(isset($_REQUEST["student"]) && $_REQUEST["student"] != "") {
	$studentName = $_REQUEST["student"];
	// make sure $studentName is really a student
	if (!$homeworklib->hw_is_student($studentName)){
	  $smarty->assign('msg', __FILE__.tra(" line ").__LINE__.",
        ".tra("Error: ").$studentName.tra(" is not a student."));
	  $smarty->display("error.tpl");
	  die;
	}
  }
  else{
	$smarty->assign('msg', __FILE__.tra(" line ").__LINE__.",
    ".tra("Error: No")." student ".tra("specified."));
	$smarty->display("error.tpl");
	die;  
  }
}
else {
  $studentName = $user;
}

// Get the assignmentId from the request, no assignmentId? then send error
//  message and die
if(isset($_REQUEST["assignmentId"]) && $_REQUEST["assignmentId"] != "") {
  $assignmentId = $_REQUEST["assignmentId"];
}
else{
  $smarty->assign('msg', __FILE__.tra(" line ").__LINE__.",
    ".tra("Error: No")." assignmentId ".tra("specified."));
  $smarty->display("error.tpl");
  die;  
}

$assignment_data = $homeworklib->get_assignment($assignmentId);
// $ggg_tracer->out(__FILE__." line: ".__LINE__.' $assignment_data = ');
// $ggg_tracer->outvar($assignment_data);

// check for valid assignment id.
if(!$assignment_data /* || $article_data['deleted'] */) {
  $smarty->assign('msg', __FILE__.tra(" line ").__LINE__.",
    ".tra("Error: Invalid")." assignmentId");
  $smarty->display("error.tpl");
  die;  
}

/*

Regarding fields in the hw_assignment table:
Most of the fields aren't used.  They are just there because I stole the code
and the fields for the table from articles.
I'm using the following here:
 articleId translates to assignmentId
 title
 expireDate translates to due date
 heading is used for the short summary
 body is only visible in the preview.

I need to add a field to designate assignments that have been deleted.
*/

$thedate = date("U");

// Get page info, or create it if it doesn't exist
if (!$homeworklib->hw_page_fetch(&$info, $studentName, $assignmentId))
{
  $homeworklib->hw_page_create($studentName, $assignmentId);
  $homeworklib->hw_page_fetch(&$info, $studentName, $assignmentId);
  $pageId = $info['id'];
  if ($user == $studentName){
	header ("location: tiki-hw_editpage.php?id=$pageId");
	die;
  }
}

$smarty->assign('assignmentTitle',$assignment_data['title']);
$smarty->assign('assignmentHeading',$assignment_data['heading']);
$smarty->assign('dueDate',$assignment_data['expireDate']);

// Verify lock status
// GGG The hw-style lock on edit may need a time-out as well as auto-lock on edit.
// TODO: This functionality has been moved into homeworklib
// Have to modify template to accomadate locking scheme.
if($info["flag"] == 'L') {
  $smarty->assign('lock',true);  
} else {
  $smarty->assign('lock',false);
}

$pdata = $tikilib->parse_data($info["data"]);

$smarty->assign_by_ref('parsed',$pdata);
$smarty->assign_by_ref('lastModif',$info["lastModif"]);

// The names of anonymous peer reviewer and graders are hidden from the student
$lastUser = $info["user"];
$lastUserType = $homeworklib->hw_user_type($lastUser);
$userType = $homeworklib->hw_user_type($user);
switch ($userType)
{
 case 'HW_ADMIN':
 case 'HW_TEACHER':
 case 'HW_GRADER':
   break;
 case 'HW_STUDENT':
   switch ($lastUserType)
	 {
	 case 'HW_ADMIN':
	 case 'HW_TEACHER':
	   break;
	 case 'HW_GRADER':
	   $lastUser = tra("Anonymous Grader");
	   break;
	 case 'HW_STUDENT':
	   if ($lastUser != $user)
		 $lastUser = tra("Anonymous Peer Reviewer");
	   break;
	 }
   break;
}
$smarty->assign('lastUser',$lastUser);

$smarty->assign('assignmentId',$assignmentId);

$pageId = $info["id"];
$smarty->assign("id",$pageId);

$smarty->assign("comment",$info["comment"]);

// GGG TODO - this code is for VERSION 2
// The "submit for grading" button was pressed
//   add this to the grading queue.
if (isset($_REQUEST["submit"])) {
  // $ggg_tracer->outln(__FILE__." line: ".__LINE__.' submit detected! ');
  $homeworklib->hw_grading_queue_submit($pageId, $info['lastModif'],
										$info['version'], $assignmentId);
}

// 0 means it is not in the queue
// 1 means it is the next paper to be graded.
$nGradingQueue = $homeworklib->hw_grading_queue($pageId);
$smarty->assign("nGradingQueue",$nGradingQueue);

$smarty->assign("studentName",$info['studentName']);


// Display the template
// GGG have to figure out how to use dblclickedit $smarty->assign('dblclickedit','y');
$smarty->assign('mid','tiki-hw_page.tpl');
$smarty->display("tiki.tpl");

?>
