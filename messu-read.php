<?php
require_once('tiki-setup.php');
include_once('lib/messu/messulib.php');

if($feature_messages != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

if($tiki_p_messages != 'y' ) {
  $smarty->assign('msg',tra("Permission denied"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}


$smarty->assign('legend','');
if(!isset($_REQUEST['msgId'])) {
  $smarty->assign('legend','No more messages');
  $smarty->assign('mid','messu-read.tpl');
  $smarty->display('tiki.tpl');
  die;
}

if(isset($_REQUEST["delete"])) {
  $messulib->delete_message($_SESSION['user'],$_REQUEST['msgdel']);
}

if(isset($_REQUEST['action'])) {
  $messulib->flag_message($_SESSION['user'], $_REQUEST['msgId'], $_REQUEST['action'], $_REQUEST['actionval']);
}

// Using the sort_mode, flag, flagval and find get the next and prev messages
$smarty->assign('sort_mode',$_REQUEST['sort_mode']);
$smarty->assign('find',$_REQUEST['find']);
$smarty->assign('flag',$_REQUEST['flag']);
$smarty->assign('offset',$_REQUEST['offset']);
$smarty->assign('flagval',$_REQUEST['flagval']);
$smarty->assign('priority',$_REQUEST['priority']);
$smarty->assign('msgId',$_REQUEST['msgId']);
$next = $messulib->get_next_message($_SESSION['user'],$_REQUEST['msgId'], $_REQUEST['sort_mode'], $_REQUEST['find'], $_REQUEST['flag'], $_REQUEST['flagval'],$_REQUEST['priority']);
$prev = $messulib->get_prev_message($_SESSION['user'],$_REQUEST['msgId'], $_REQUEST['sort_mode'], $_REQUEST['find'], $_REQUEST['flag'], $_REQUEST['flagval'],$_REQUEST['priority']);
$smarty->assign('next',$next);
$smarty->assign('prev',$prev);

// Mark the message as read
$messulib->flag_message($_SESSION['user'], $_REQUEST['msgId'], 'isRead', 'y');

// Get the message and assign its data to template vars
$msg = $messulib->get_message($_SESSION['user'],$_REQUEST['msgId']);
$smarty->assign_by_ref('msg',$msg);

$smarty->assign('mid','messu-read.tpl');
$smarty->display('tiki.tpl');
?>