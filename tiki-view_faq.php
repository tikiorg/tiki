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
    $smarty->assign('msg',tra("No faq indicated"));
    $smarty->display('error.tpl');
    die;
}

$tikilib->add_faq_hit($_REQUEST["faqId"]);

$smarty->assign('faqId',$_REQUEST["faqId"]);
$faq_info = $tikilib->get_faq($_REQUEST["faqId"]);
$smarty->assign('faq_info',$faq_info);


if(isset($_REQUEST["find"])) {
  $find = $_REQUEST["find"];  
} else {
  $find = ''; 
}
$smarty->assign('find',$find);

$channels = $tikilib->list_faq_questions($_REQUEST["faqId"],0,-1,'question_asc',$find);


$smarty->assign_by_ref('channels',$channels["data"]);


if($feature_faq_comments == 'y') {
  $comments_per_page = $faq_comments_per_page;
  $comments_default_ordering = $faq_comments_default_ordering;
  $comments_vars=Array('faqId');
  $comments_prefix_var='faq';
  $comments_object_var='faqId';
  include_once("comments.php");
}



// Display the template
$smarty->assign('mid','tiki-view_faq.tpl');
$smarty->display('tiki.tpl');
?>