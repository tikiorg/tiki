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

$smarty->assign('quizId',$_REQUEST["quizId"]);
$quiz_info=$tikilib->get_quiz($_REQUEST["quizId"]);


if($tiki_p_take_quiz != 'y') {
    $smarty->assign('msg',tra("You dont have permission to use this feature"));
    $smarty->display('error.tpl');
    die;
}


if($user) {
  // If the quiz cannot be repeated
  if($quiz_info["canRepeat"]=='n') {
    // Check if user has taken this quiz
    if($tikilib->user_has_taken_quiz($user,$_REQUEST["quizId"])) {
      $smarty->assign('msg',tra("You cannot take this quiz twice"));
      $smarty->display('error.tpl');
      die;
    } else {
      $tikilib->user_takes_quiz($user,$_REQUEST["quizId"]);
    }
  }
}







$smarty->assign('ans','n');
if(isset($_REQUEST["timeleft"])) {
  $smarty->assign('ans','y');
  $_SESSION["finishQuiz"]=date("U");
  $elapsed = $_SESSION["finishQuiz"]-$_SESSION["startQuiz"];
  
  if($user) {
    // If the quiz cannot be repeated
    if($quiz_info["canRepeat"]=='n') {
      // Check if user has taken this quiz
      if($tikilib->user_has_taken_quiz($user,$_REQUEST["quizId"])) {
        $smarty->assign('msg',tra("You cannot take this quiz twice"));
        $smarty->display('error.tpl');
        die;
      } else {
        $tikilib->user_takes_quiz($user,$_REQUEST["quizId"]);
      }
    }
  }
  
  // Now get the quiz information
  // Verify time limit if appropiate
  
  
  if($quiz_info["timeLimited"]=='y') {
    if($elapsed > $quiz_info["timeLimit"]*60) {
      $smarty->assign('msg',tra("Quiz time limit excedeed quiz cannot be computed"));
      $smarty->display('error.tpl');
      die;
    }
  }  

      
  // Now for each quiz question verify the points the user did get
  $questions = $tikilib->list_quiz_questions($_REQUEST["quizId"],0,-1,'position_asc','');
  $points = 0;
  $max = 0;
  for($i=0;$i<count($questions["data"]);$i++) {
    $options = $tikilib->list_quiz_question_options($questions["data"][$i]["questionId"],0,-1,'optionText_desc','');
    $qid=$questions["data"][$i]["questionId"];
    $max += $questions["data"][$i]["maxPoints"];
    if(isset($_REQUEST["question_$qid"])) {
      $opt = $tikilib->get_quiz_question_option($_REQUEST["question_$qid"]);
      $points += $opt["points"];
      // Register the answer for quiz stats
      $tikilib->register_quiz_answer($_REQUEST["quizId"],$qid,$_REQUEST["question_$qid"]);
    }
  }
  $result = $tikilib->calculate_quiz_result($_REQUEST["quizId"],$points);
  // register the result for quiz stats
  $userResultId = $tikilib->register_quiz_stats($_REQUEST["quizId"],$user,$elapsed,$points,$max,$result["resultId"]);
  $smarty->assign_by_ref('result',$result);
  if($quiz_info["storeResults"]=='y') {
    for($i=0;$i<count($questions["data"]);$i++) {
      $options = $tikilib->list_quiz_question_options($questions["data"][$i]["questionId"],0,-1,'optionText_desc','');
      $qid=$questions["data"][$i]["questionId"];
      if(isset($_REQUEST["question_$qid"])) {
        $tikilib->register_user_quiz_answer($userResultId,$_REQUEST["quizId"],$qid,$_REQUEST["question_$qid"]);
      }
    }
  } 
  //print("points: $points over $max<br/>");
} else {
  $_SESSION["startQuiz"]=date("U");
}


$quiz_info["timeLimitsec"]=$quiz_info["timeLimit"]*60;
$smarty->assign('quiz_info',$quiz_info);

$questions = $tikilib->list_quiz_questions($_REQUEST["quizId"],0,-1,'position_asc','');
for($i=0;$i<count($questions["data"]);$i++) {
  $options = $tikilib->list_quiz_question_options($questions["data"][$i]["questionId"],0,-1,'optionText_desc','');
  $questions["data"][$i]["options"]=$options["data"];
}

$smarty->assign_by_ref('questions',$questions["data"]);

// Display the template
$smarty->assign('mid','tiki-take_quiz.tpl');
$smarty->display('tiki.tpl');
?>