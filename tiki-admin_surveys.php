<?php
// Initialization
require_once('tiki-setup.php');

if($feature_surveys != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

if(!isset($_REQUEST["surveyId"])) {
  $_REQUEST["surveyId"] = 0;
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
    $smarty->display("styles/$style_base/error.tpl");
    die;
}

if($_REQUEST["surveyId"]) {
  $info = $tikilib->get_survey($_REQUEST["surveyId"]);
} else {
  $info = Array();
  $info["name"]='';
  $info["description"]='';
  $info["status"]='o';
}
$smarty->assign('info',$info);

if(isset($_REQUEST["remove"])) {
  $tikilib->remove_survey($_REQUEST["remove"]);
}

if(isset($_REQUEST["save"])) {
  
  $sid = $tikilib->replace_survey($_REQUEST["surveyId"], $_REQUEST["name"], $_REQUEST["description"], $_REQUEST["status"]);
  
  $cat_type='survey';
  $cat_objid = $sid;
  $cat_desc = substr($_REQUEST["description"],0,200);
  $cat_name = $_REQUEST["name"];
  $cat_href="tiki-take_survey.php?surveyId=".$cat_objid;
  include_once("categorize.php");
}

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

// Fill array with possible number of questions per page
$qpp=Array(1,2,3,4);
for($i=5;$i<50;$i+=5) $qpp[]=$i;
$hrs=Array();
for($i=0;$i<10;$i++) $hrs[]=$i;
$mins=Array();
for($i=1;$i<120;$i++) $mins[]=$i;
$smarty->assign('qpp',$qpp);
$smarty->assign('hrs',$hrs);
$smarty->assign('mins',$mins);

$cat_type='survey';
$cat_objid = $_REQUEST["surveyId"];
include_once("categorize_list.php");


// Display the template
$smarty->assign('mid','tiki-admin_surveys.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>
