<?php
// Initialization
require_once('tiki-setup.php');



if($feature_quizzes != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

if(!isset($_REQUEST["quizId"])) {
  $_REQUEST["quizId"] = 0;
}
$smarty->assign('quizId',$_REQUEST["quizId"]);


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


if($tiki_p_admin_quizzes != 'y') {
    $smarty->assign('msg',tra("You dont have permission to use this feature"));
    $smarty->display("styles/$style_base/error.tpl");
    die;
}



$_REQUEST["questionsPerPage"]=999;

if($_REQUEST["quizId"]) {
  $info = $tikilib->get_quiz($_REQUEST["quizId"]);
} else {
  $info = Array();
  $info["name"]='';
  $info["description"]='';
  $info["canRepeat"]='n';
  $info["storeResults"]='n';
  $info["questionsPerPage"]=10;
  $info["timeLimited"]='n';
  $info["timeLimit"]=60*60;
}
$smarty->assign('name',$info["name"]);
$smarty->assign('description',$info["description"]);
$smarty->assign('canRepeat',$info["canRepeat"]);
$smarty->assign('storeResults',$info["storeResults"]);
$smarty->assign('questionsPerPage',$info["questionsPerPage"]);
$smarty->assign('timeLimited',$info["timeLimited"]);
$smarty->assign('timeLimit',$info["timeLimit"]);


if(isset($_REQUEST["remove"])) {
  $tikilib->remove_quiz($_REQUEST["remove"]);
}

if(isset($_REQUEST["save"])) {
  if(isset($_REQUEST["canRepeat"])&&$_REQUEST["canRepeat"]=='on') {
    $_REQUEST["canRepeat"]='y';
  } else {
    $_REQUEST["canRepeat"]='n';
  }
  if(isset($_REQUEST["storeResults"])&&$_REQUEST["storeResults"]=='on') {
    $_REQUEST["storeResults"]='y';
  } else {
    $_REQUEST["storeResults"]='n';
  }
  if(isset($_REQUEST["timeLimited"])&&$_REQUEST["timeLimited"]=='on') {
    $_REQUEST["timeLimited"]='y';
  } else {
    $_REQUEST["timeLimited"]='n';
  }
  
  $qid = $tikilib->replace_quiz($_REQUEST["quizId"], $_REQUEST["name"], $_REQUEST["description"], $_REQUEST["canRepeat"],$_REQUEST["storeResults"],$_REQUEST["questionsPerPage"],$_REQUEST["timeLimited"],$_REQUEST["timeLimit"]);
  
  $cat_type='quiz';
  $cat_objid = $qid;
  $cat_desc = substr($_REQUEST["description"],0,200);
  $cat_name = $_REQUEST["name"];
  $cat_href="tiki-take_quiz.php?quizId=".$cat_objid;
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
$channels = $tikilib->list_quizzes($offset,$maxRecords,$sort_mode,$find);

for($i=0;$i<count($channels["data"]);$i++) {
  if($userlib->object_has_one_permission($channels["data"][$i]["quizId"],'quiz')) {
    $channels["data"][$i]["individual"]='y';
    
    if($userlib->object_has_permission($user,$channels["data"][$i]["quizId"],'quiz','tiki_p_take_quiz')) {
      $channels["data"][$i]["individual_tiki_p_take_quiz"]='y';
    } else {
      $channels["data"][$i]["individual_tiki_p_take_quiz"]='n';
    }
    if($userlib->object_has_permission($user,$channels["data"][$i]["quizId"],'quiz','tiki_p_view_quiz_stats')) {
      $channels["data"][$i]["individual_tiki_p_view_quiz_stats"]='y';
    } else {
      $channels["data"][$i]["individual_tiki_p_view_quiz_stats"]='n';
    }
    if($userlib->object_has_permission($user,$channels["data"][$i]["quizId"],'quiz','tiki_p_view_user_stats')) {
      $channels["data"][$i]["individual_tiki_p_view_user_stats"]='y';
    } else {
      $channels["data"][$i]["individual_tiki_p_view_user_stats"]='n';
    }
    if($tiki_p_admin=='y' || $userlib->object_has_permission($user,$channels["data"][$i]["quizId"],'quiz','tiki_p_admin_quizzes')) {
      $channels["data"][$i]["individual_tiki_p_take_quiz"]='y';
      $channels["data"][$i]["individual_tiki_p_view_quiz_stats"]='y';
      $channels["data"][$i]["individual_tiki_p_admin_quizzes"]='y';
      $channels["data"][$i]["individual_tiki_p_view_user_stats"]='y';
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

$cat_type='quiz';
$cat_objid = $_REQUEST["quizId"];
include_once("categorize_list.php");


// Display the template
$smarty->assign('mid','tiki-edit_quiz.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>