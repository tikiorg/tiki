<?php
// Initialization
require_once('tiki-setup.php');

if($feature_surveys != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

/*
if($tiki_p_take_quiz != 'y') {
    $smarty->assign('msg',tra("You dont have permission to use this feature"));
    $smarty->display("styles/$style_base/error.tpl");
    die;
}
*/

if(!isset($_REQUEST["sort_mode"])) {
  $sort_mode = 'created_desc'; 
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
$channels = $tikilib->list_surveys($offset,$maxRecords,$sort_mode,$find);

for($i=0;$i<count($channels["data"]);$i++) {
  if($userlib->object_has_one_permission($channels["data"][$i]["surveyId"],'survey')) {
    $channels["data"][$i]["individual"]='y';
    
    if($userlib->object_has_permission($user,$channels["data"][$i]["surveyId"],'survey','tiki_p_take_survey')) {
      $channels["data"][$i]["individual_tiki_p_take_survey"]='y';
    } else {
      $channels["data"][$i]["individual_tiki_p_take_survey"]='n';
    }
    if($userlib->object_has_permission($user,$channels["data"][$i]["surveyId"],'survey','tiki_p_view_survey_stats')) {
      $channels["data"][$i]["individual_tiki_p_view_survey_stats"]='y';
    } else {
      $channels["data"][$i]["individual_tiki_p_view_survey_stats"]='n';
    }
    
    if($tiki_p_admin=='y' || $userlib->object_has_permission($user,$channels["data"][$i]["surveyId"],'survey','tiki_p_admin_surveys')) {
      $channels["data"][$i]["individual_tiki_p_take_survey"]='y';
      $channels["data"][$i]["individual_tiki_p_view_survey_stats"]='y';
      $channels["data"][$i]["individual_tiki_p_admin_surveys"]='y';
    } 
    
  } else {
    $channels["data"][$i]["individual"]='n';
  }
}


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

$section='surveys';
include_once('tiki-section_options.php');


// Display the template
$smarty->assign('mid','tiki-list_surveys.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>