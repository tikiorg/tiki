<?php
// Initialization
require_once('tiki-setup.php');
include_once('lib/polls/polllib.php');

if(!isset($polllib)) {
  $polllib = new PollLib($dbTiki);
}


if($feature_polls != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

if(!isset($_REQUEST["pollId"])) {
    $smarty->assign('msg',tra("No poll indicated"));
    $smarty->display("styles/$style_base/error.tpl");
    die;
}

$poll_info = $tikilib->get_poll($_REQUEST["pollId"]);
$polls = $polllib->list_active_polls(0,-1,'publishDate_desc','');
$options = $polllib->list_poll_options($_REQUEST["pollId"],0,-1,'title_desc','');
for($i=0;$i<count($options["data"]);$i++)
{
  if($poll_info["votes"]==0) {
    $percent = 0;
  } else {
    $percent = number_format($options["data"][$i]["votes"]*100 / $poll_info["votes"],2);
    $options["data"][$i]["percent"]=$percent;
  }
  $width = $percent * 200 / 100;
  $options["data"][$i]["width"]=$percent;
}

// Poll comments

if($feature_poll_comments == 'y') {
  $comments_per_page = $poll_comments_per_page;
  $comments_default_ordering = $poll_comments_default_ordering;
  $comments_vars=Array('pollId');
  $comments_prefix_var='poll';
  $comments_object_var='pollId';
  include_once("comments.php");
}



$smarty->assign_by_ref('poll_info',$poll_info);
$smarty->assign_by_ref('polls',$polls["data"]);
$smarty->assign_by_ref('options',$options["data"]);

// Display the template
$smarty->assign('mid','tiki-poll_results.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>