<?php
// Initialization
require_once('tiki-setup.php');

if($feature_quizzes != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display('error.tpl');
  die;  
}

if(!isset($_REQUEST["quizId"])) {
  $smarty->assign('msg',tra("No quiz indicated"));
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


if($tiki_p_view_user_results != 'y') {
    $smarty->assign('msg',tra("You dont have permission to use this feature"));
    $smarty->display('error.tpl');
    die;
}


$smarty->assign('quizId',$_REQUEST["quizId"]);
$quiz_info=$tikilib->get_quiz($_REQUEST["quizId"]);
$smarty->assign('quiz_info',$quiz_info);

if(!isset($_REQUEST["resultId"])) {
  $smarty->assign('msg',tra("No result indicated"));
  $smarty->display('error.tpl');
  die;
}
$smarty->assign('resultId',$_REQUEST["resultId"]);

if(!isset($_REQUEST["userResultId"])) {
  $smarty->assign('msg',tra("No result indicated"));
  $smarty->display('error.tpl');
  die;
}
$smarty->assign('userResultId',$_REQUEST["userResultId"]);
$ur_info = $tikilib->get_user_quiz_result($_REQUEST["userResultId"]);
$smarty->assign('ur_info',$ur_info);

$result = $tikilib->get_quiz_result($resultId);
$smarty->assign_by_ref('result',$result);

$questions = $tikilib->get_user_quiz_questions($_REQUEST["userResultId"]);
$smarty->assign('questions',$questions);

$section='quizzes';
include_once('tiki-section_options.php');


$smarty->assign('mid','tiki-quiz_result_stats.tpl');
$smarty->display('tiki.tpl');
?>
