<?php
// Initialization
require_once('tiki-setup.php');

if($feature_faqs != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display('error.tpl');
  die;  
}


if($tiki_p_view_faqs != 'y') {
    $smarty->assign('msg',tra("You dont have permission to use this feature"));
    $smarty->display('error.tpl');
    die;
}

if(!isset($_REQUEST["faqId"])) {
  $_REQUEST["faqId"] = 0;
}
$smarty->assign('faqId',$_REQUEST["faqId"]);

if($_REQUEST["faqId"]) {
  $info = $tikilib->get_faq($_REQUEST["faqId"]);
} else {
  $info = Array();
  $info["title"]='';
  $info["description"]='';
}
$smarty->assign('title',$info["title"]);
$smarty->assign('description',$info["description"]);


if(isset($_REQUEST["remove"])) {
  if($tiki_p_admin_faqs != 'y') {
    $smarty->assign('msg',tra("You dont have permission to use this feature"));
    $smarty->display('error.tpl');
    die;
  }
  $tikilib->remove_faq($_REQUEST["remove"]);
}

if(isset($_REQUEST["save"])) {
  if($tiki_p_admin_faqs != 'y') {
    $smarty->assign('msg',tra("You dont have permission to use this feature"));
    $smarty->display('error.tpl');
    die;
  }
  $tikilib->replace_faq($_REQUEST["faqId"], $_REQUEST["title"], $_REQUEST["description"]);
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
$channels = $tikilib->list_faqs($offset,$maxRecords,$sort_mode,$find);

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


// Display the template
$smarty->assign('mid','tiki-list_faqs.tpl');
$smarty->display('tiki.tpl');
?>