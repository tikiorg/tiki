<?php
// Initialization
require_once('tiki-setup.php');
include_once('lib/surveys/surveylib.php');

if($feature_surveys != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

if(!isset($_REQUEST["surveyId"])) {
  $smarty->assign('msg',tra("No survey indicated"));
  $smarty->display("styles/$style_base/error.tpl");
  die;
}

$smarty->assign('individual','n');
if($userlib->object_has_one_permission($_REQUEST["surveyId"],'survey')) {
  $smarty->assign('individual','y');
  if($tiki_p_admin != 'y') {
    $perms = $userlib->get_permissions(0,-1,'permName_desc','','surveys');
    foreach($perms["data"] as $perm) {
      $permName=$perm["permName"];
      if($userlib->object_has_permission($user,$_REQUEST["surveyId"],'survey',$permName)) {
        $$permName = 'y';
        $smarty->assign("$permName",'y');
      } else {
        $$permName = 'n';
        $smarty->assign("$permName",'n');
      }
    }
  }
}

$smarty->assign('surveyId',$_REQUEST["surveyId"]);
$survey_info=$srvlib->get_survey($_REQUEST["surveyId"]);


if($tiki_p_take_survey != 'y') {
    $smarty->assign('msg',tra("You dont have permission to use this feature"));
    $smarty->display("styles/$style_base/error.tpl");
    die;
}



// Check if user has taken this survey
if($tiki_p_admin != 'y') {
if($tikilib->user_has_voted($user,'survey'.$_REQUEST["surveyId"])) {
  $smarty->assign('msg',tra("You cannot take this survey twice"));
  $smarty->display("styles/$style_base/error.tpl");
  die;
} 
}

$srvlib->add_survey_hit($_REQUEST["surveyId"]);

$smarty->assign('survey_info',$survey_info);

$questions = $srvlib->list_survey_questions($_REQUEST["surveyId"],0,-1,'position_asc','');
$smarty->assign_by_ref('questions',$questions["data"]);

if(isset($_REQUEST["ans"])) {
  foreach($questions["data"] as $question) {
    $questionId=$question["questionId"];
    //print("question: $questionId<br/>");
    if(isset($_REQUEST["question_".$questionId])) {
      if($question["type"]=='m') {
        // If we have a multiple question
        $ids = array_keys($_REQUEST["question_".$questionId]);	
        //print_r($ids);
        // Now for each of the options we increase the number of votes
        foreach($ids as $optionId) {
          $srvlib->register_survey_option_vote($questionId,$optionId);
        }
      } else {
      	$value = $_REQUEST["question_".$questionId];
      	//print("value: $value<br/>");
      	if($question["type"]=='r' || $question["type"]=='s') {
      	  $srvlib->register_survey_rate_vote($questionId,$value);	
      	} elseif ($question["type"]=='t') {
      	  $srvlib->register_survey_text_option_vote($questionId,$value);	
      	} else {
      	  $srvlib->register_survey_option_vote($questionId,$value);	
      	}
      }
    }	
  }
  $tikilib->register_user_vote($user,'survey'.$_REQUEST["surveyId"]);
  header("location: tiki-list_surveys.php");
}


//print_r($questions);
$section='surveys';
include_once('tiki-section_options.php');


// Display the template
$smarty->assign('mid','tiki-take_survey.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>