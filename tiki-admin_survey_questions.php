<?php
// Initialization
require_once('tiki-setup.php');


if($feature_surveys != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display('error.tpl');
  die;  
}

if(!isset($_REQUEST["surveyId"])) {
  $smarty->assign('msg',tra("No survey indicated"));
  $smarty->display('error.tpl');
  die;
}
$smarty->assign('surveyId',$_REQUEST["surveyId"]);


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

if($tiki_p_admin_surveys != 'y') {
    $smarty->assign('msg',tra("You dont have permission to use this feature"));
    $smarty->display('error.tpl');
    die;
}


$survey_info=$tikilib->get_survey($_REQUEST["surveyId"]);
$smarty->assign('survey_info',$survey_info);

if(!isset($_REQUEST["questionId"])) {
  $_REQUEST["questionId"] = 0;
}
$smarty->assign('questionId',$_REQUEST["questionId"]);

if($_REQUEST["questionId"]) {
  $info = $tikilib->get_survey_question($_REQUEST["questionId"]);
} else {
  $info = Array();
  $info["question"]='';
  $info["type"]='';
  $info["position"]='';
  $info["options"]='';
}
$smarty->assign('info',$info);


if(isset($_REQUEST["remove"])) {
  $tikilib->remove_survey_question($_REQUEST["remove"]);
}

if(isset($_REQUEST["save"])) {
  $tikilib->replace_survey_question($_REQUEST["questionId"], $_REQUEST["question"], $_REQUEST["type"], $_REQUEST["surveyId"],$_REQUEST["position"],$_REQUEST["options"]);
  $info["question"]='';
  $info["type"]='';
  $info["position"]='';
  $info["options"]='';
  $smarty->assign('questionId',0);
  $smarty->assign('info',$info);
}


if(!isset($_REQUEST["sort_mode"])) {
  $sort_mode = 'position_asc'; 
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
$channels = $tikilib->list_survey_questions($_REQUEST["surveyId"],$offset,$maxRecords,$sort_mode,$find);
//$smarty->assign('questions',$channels["data"]);

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

// Fill array with possible number of questions per page
$positions=Array();
for($i=1;$i<100;$i++) $positions[]=$i;
$smarty->assign('positions',$positions);



// Display the template
$smarty->assign('mid','tiki-admin_survey_questions.tpl');
$smarty->display('tiki.tpl');
?>