<?php
// Initialization
require_once('tiki-setup.php');


if($feature_quizzes != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display('error.tpl');
  die;  
}

$smarty->assign('individual','n');
if($userlib->object_has_one_permission($_REQUEST["quizId"],'quiz')) {
  $smarty->assign('individual','y');
  if($tiki_p_admin != 'y') {
    $perms = $userlib->get_permissions(0,-1,'permName_desc','','quizzes');
    foreach($perms["data"] as $perm) {
      $permName=$perm["permName"];
      if($userlib->object_has_permission($user,$_REQUEST["quizId"],'quiz',$permName)) {
        $$permName = 'y';
        $smarty->assign("$permName",'y');
      } else {
        $$permName = 'n';
        $smarty->assign("$permName",'n');
      }
    }
  }
}

if($tiki_p_view_quiz_stats != 'y') {
    $smarty->assign('msg',tra("You dont have permission to use this feature"));
    $smarty->display('error.tpl');
    die;
}


if(!isset($_REQUEST["quizId"])) {
  $smarty->assign('msg',tra("No quiz indicated"));
  $smarty->display('error.tpl');
  die;
}
$smarty->assign('quizId',$_REQUEST["quizId"]);
$quiz_info=$tikilib->get_quiz($_REQUEST["quizId"]);
$smarty->assign('quiz_info',$quiz_info);

if(isset($_REQUEST["remove"]) && $tiki_p_admin_quizzes=='y') {
  $tikilib->remove_quiz_stat($_REQUEST["remove"]);
}

if(isset($_REQUEST["clear"]) && $tiki_p_admin_quizzes=='y') {
  $tikilib->clear_quiz_stats($_REQUEST["clear"]);
}


if(!isset($_REQUEST["sort_mode"])) {
  $sort_mode = 'timestamp_desc'; 
} else {
  $sort_mode = $_REQUEST["sort_mode"];
} 

if(!isset($_REQUEST["offset"])) {
  $offset = 0;
} else {
  $offset = $_REQUEST["offset"]; 
}
$smarty->assign_by_ref('offset',$offset);

if(isset($_REQUEST["find"])) {
  $find = $_REQUEST["find"];  
} else {
  $find = ''; 
}
$smarty->assign('find',$find);

$smarty->assign_by_ref('sort_mode',$sort_mode);

$channels = $tikilib->list_quiz_stats($_REQUEST["quizId"],$offset,$maxRecords,$sort_mode,$find);

$cant_pages = ceil($channels["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages',$cant_pages);
$smarty->assign('actual_page',1+($offset/$maxRecords));
if($channels["cant"] > ($offset+$maxRecords)) {
  $smarty->assign('next_offset',$offset + $maxRecords);
} else {
  $smarty->assign('next_offset',-1); 
}
// If offset is > 0 then prev_offset
if($offset>0) {
  $smarty->assign('prev_offset',$offset - $maxRecords);  
} else {
  $smarty->assign('prev_offset',-1); 
}

$smarty->assign_by_ref('channels',$channels["data"]);


//Get all the statistics for this quiz
$questions = $tikilib->list_quiz_question_stats($_REQUEST["quizId"],0,-1,'position_desc','');
$smarty->assign_by_ref('questions',$questions);

// Display the template
$smarty->assign('mid','tiki-quiz_stats_quiz.tpl');
$smarty->display('tiki.tpl');


?>