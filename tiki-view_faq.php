<?php
// Initialization
require_once('tiki-setup.php');
include_once('lib/faqs/faqlib.php');

if($feature_faqs != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

if($tiki_p_view_faqs != 'y') {
    $smarty->assign('msg',tra("You dont have permission to use this feature"));
    $smarty->display("styles/$style_base/error.tpl");
    die;
}

if(!isset($_REQUEST["faqId"])) {
    $smarty->assign('msg',tra("No faq indicated"));
    $smarty->display("styles/$style_base/error.tpl");
    die;
}

$faqlib->add_faq_hit($_REQUEST["faqId"]);

$smarty->assign('faqId',$_REQUEST["faqId"]);
$faq_info = $tikilib->get_faq($_REQUEST["faqId"]);
$smarty->assign('faq_info',$faq_info);


if(isset($_REQUEST["find"])) {
  $find = $_REQUEST["find"];  
} else {
  $find = ''; 
}
$smarty->assign('find',$find);

$channels = $faqlib->list_faq_questions($_REQUEST["faqId"],0,-1,'question_asc',$find);


$smarty->assign_by_ref('channels',$channels["data"]);

if(isset($_REQUEST["sugg"])) {
  if($tiki_p_suggest_faq == 'y')  {
    $faqlib->add_suggested_faq_question($_REQUEST["faqId"],$_REQUEST["suggested_question"],$_REQUEST["suggested_answer"],$user);
  }
}

$suggested = $faqlib->list_suggested_questions(0,-1,'created_desc','');
$smarty->assign_by_ref('suggested',$suggested["data"]);

if($feature_faq_comments == 'y') {
  $comments_per_page = $faq_comments_per_page;
  $comments_default_ordering = $faq_comments_default_ordering;
  $comments_vars=Array('faqId');
  $comments_prefix_var='faq';
  $comments_object_var='faqId';
  include_once("comments.php");
}

$section='faqs';
include_once('tiki-section_options.php');


if($feature_theme_control == 'y') {
	$cat_type='faq';
	$cat_objid = $_REQUEST["faqId"];
	include('tiki-tc.php');
}

// Display the template
$smarty->assign('mid','tiki-view_faq.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>