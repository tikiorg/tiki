<?php
// Initialization
require_once('tiki-setup.php');

if($feature_faqs != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

if($tiki_p_admin_faqs != 'y') {
    $smarty->assign('msg',tra("You dont have permission to use this feature"));
    $smarty->display("styles/$style_base/error.tpl");
    die;
}

if(!isset($_REQUEST["faqId"])) {
    $smarty->assign('msg',tra("No menu indicated"));
    $smarty->display("styles/$style_base/error.tpl");
    die;
}


$smarty->assign('faqId',$_REQUEST["faqId"]);
$faq_info = $tikilib->get_faq($_REQUEST["faqId"]);
$smarty->assign('faq_info',$faq_info);

if(!isset($_REQUEST["questionId"])) {
    $_REQUEST["questionId"]=0;
}
$smarty->assign('questionId',$_REQUEST["questionId"]);


if($_REQUEST["questionId"]) {
  $info = $tikilib->get_faq_question($_REQUEST["questionId"]);
} else {
  $info = Array();
  $info["question"]='';
  $info["answer"]='';
}
$smarty->assign('question',$info["question"]);
$smarty->assign('answer',$info["answer"]);

if(isset($_REQUEST["remove"])) {
  $tikilib->remove_faq_question($_REQUEST["remove"]);
}

if(!isset($_REQUEST["filter"])) {$_REQUEST["filter"]='';}
$smarty->assign('filter',$_REQUEST["filter"]);

if(isset($_REQUEST["useq"])) {
  $quse = $tikilib->get_faq_question($_REQUEST["usequestionId"]);
  $tikilib->replace_faq_question($_REQUEST["faqId"], 0, $quse["question"], $quse["answer"]);
}

if(isset($_REQUEST["save"])) {
   $tikilib->replace_faq_question($_REQUEST["faqId"], $_REQUEST["questionId"], $_REQUEST["question"], $_REQUEST["answer"]);
}

if(!isset($_REQUEST["sort_mode"])) {
  $sort_mode = 'question_asc'; 
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

if(isset($_REQUEST["remove_suggested"])) {
  $tikilib->remove_suggested_question($_REQUEST["remove_suggested"]);
}
if(isset($_REQUEST["approve_suggested"])) {
  $tikilib->approve_suggested_question($_REQUEST["approve_suggested"]);
}


$smarty->assign_by_ref('sort_mode',$sort_mode);
$channels = $tikilib->list_faq_questions($_REQUEST["faqId"],0,-1,$sort_mode,$find);
$allq = $tikilib->list_all_faq_questions(0,-1,'question_asc',$_REQUEST["filter"]);
$smarty->assign_by_ref('allq',$allq["data"]);

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


$suggested = $tikilib->list_suggested_questions(0,-1,'created_desc','');
$smarty->assign_by_ref('suggested',$suggested["data"]);



// Display the template
$smarty->assign('mid','tiki-faq_questions.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>