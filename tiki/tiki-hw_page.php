<?php
// Initialization
//       1         2         3         4         5         6         7      
// 4567890123456789012345678901234567890123456789012345678901234567890123456789
// George G. Geller
// January 23, 2003
// tiki-hw_student.php
//
// Adapted from tiki-index.php
//
// This is where the student views his work on a wiki-like page.
// Access is by the student's login and by assignmentId.
//
// New features needed: Show/hide assignment, submit page for grading, lock 
//   while being graded
//
// Wiki features needed: history, editing, comments
//
// Wiki features not needed: structures, backlinks, rollbacks, wiki-style lock,
//   notepad, undos, slides, theme control, custom layout, attachments,
//   footnotes, watches, hawhaw (mobility), catagories.

error_reporting (E_ALL);
require_once('/www/tikiwiki/doc/devtools/ggg-trace.php');
require_once('tiki-setup.php');
// GGG This has to be redone include_once('lib/wiki/histlib.php');
require_once('lib/homework/homeworklib.php');

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

// Get the assignmentId from the request, no assignmentId? then send error
//  message and die
if(isset($_REQUEST["assignmentId"]) && $_REQUEST["assignmentId"] != "") {
  $assignmentId = $_REQUEST["assignmentId"];
}
else{
  $smarty->assign('msg', __FILE__.tra(" line ").__LINE__.", \ 
    ".tra("Error: No")." assignmentId ".tra("specified."));
  $smarty->display("error.tpl");
  die;  
}

$homeworklib = new HomeworkLib($dbTiki);

$assignment_data = $homeworklib->get_assignment($assignmentId);
// $ggg_tracer->out(__FILE__." line: ".__LINE__.' $assignment_data = ');
// $ggg_tracer->outvar($assignment_data);

// check for valid assignment id.
if(!$assignment_data /* || $article_data['deleted'] */) {
  $smarty->assign('msg', __FILE__.tra(" line ").__LINE__.", \
    ".tra("Error: Invalid")." assignmentId");
  $smarty->display("error.tpl");
  die;  
}

/* The assignment_data might look like this:
 articleId=>2
 title=>flowers of the world
 state=>s
 authorName=>bnuz
 topicId=>0
 topicName=>
 size=>174
 useImage=>n
 image_name=>
 image_type=>
 image_size=>0
 image_x=>0
 image_y=>0
 image_data=>
 publishDate=>-1
 expireDate=>1077620400
 created=>1074747306
 heading=>Flowers have always been a symbol of life and happiness, but always feminity and fragility. Flowers as symbols appear, on flags, logos,  I want you to pick such a symbolical flower and analyse its blalbalblablallb
 body=>!!!References
*See book x y for references on symbolical content of natural elements
*See
*
!!!Worksteps
*open a gallery and collect in it logos and pics from the web
 
 hash=>d7c19163a18b553e9808d3a2fcd43705
 author=>admin
 reads=>0
 votes=>0
 points=>0
 type=>
 rating=>0.00
 isfloat=>n
 avatarLibName=>
 use_ratings=>
 show_pre_publ=>
 show_post_expire=>y
 heading_only=>
 allow_comments=>y
 comment_can_rate_article=>
 show_image=>y
 show_avatar=>
 show_author=>y
 show_pubdate=>y
 show_expdate=>
 show_reads=>y
 show_size=>y
 creator_edit=>
 entrating=>0
*/

/*
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
// Have to do the history stuff and the comment stuff too.
if (!$homeworklib->hw_page_fetch(&$info, $user, $assignmentId))
{
  // $ggg_tracer->outln(__FILE__." line: ".__LINE__.' Create a homework page.');  
  $homeworklib->hw_page_create($user, $assignmentId);
  $homeworklib->hw_page_fetch(&$info, $user,$assignmentId);
  // $ggg_tracer->out(__FILE__." line: ".__LINE__.' $info = ');
  // $ggg_tracer->outvar($info);
  $id = $info['id'];
  header ("location: tiki-hw_editpage.php?id=$id");
  die;
}

/* 
$ggg_tracer->out(__FILE__." line: ".__LINE__.' $info = ');
$ggg_tracer->outvar($info);

Gives the contents of $info:

/www/tikiwiki/tiki-hw_student.php line: 147 $info = Array
 id=>17
 assignmentId=>2
 studentName=>Bobby
 data=>
 description=>
 lastModif=>1075325142
 user=>Bobby
 comment=>
 version=>0
 ip=>192.168.2.7
 flag=>
 points=>
 votes=>
 cache=>
 wiki_cache=>0
 cache_timestamp=>
 page_size=>0
*/

$smarty->assign('assignmentTitle',$assignment_data['title']);
$smarty->assign('assignmentHeading',$assignment_data['heading']);
$smarty->assign('dueDate',$assignment_data['expireDate']);

// Verify lock status
// GGG this is the wiki-style lock.  The hw-style lock on edit may need a time-out.
if($info["flag"] == 'L') {
  $smarty->assign('lock',true);  
} else {
  $smarty->assign('lock',false);
}

// GGG Don't use cache
// $smarty->assign('cached_page','n');

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

$ggg_tracer->out(__FILE__." line: ".__LINE__.' $info["id"] = ');
$ggg_tracer->outvar($info["id"]);
$id = $info["id"];
$smarty->assign("id",$info["id"]);

$ggg_tracer->out(__FILE__." line: ".__LINE__.' $info["comment"] = ');
$ggg_tracer->outvar($info["comment"]);
$smarty->assign("comment",$info["comment"]);

// 0 means it is not in the queue
// 1 means it is the next paper to be graded.
$nGradingQueue = $homeworklib->hw_grading_queue($id);
$smarty->assign("nGradingQueue",$nGradingQueue);

$smarty->assign("studentName",$info['studentName']);


// Display the template
// GGG have to figure out how to use dblclickedit $smarty->assign('dblclickedit','y');
$smarty->assign('mid','tiki-hw_page.tpl');
$smarty->display("tiki.tpl");

?>
