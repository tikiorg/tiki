<?php
require_once('tiki-setup.php');
include_once('lib/messu/messulib.php');

if(!$user) {
   $smarty->assign('msg',tra("You are not logged in"));
   $smarty->display("styles/$style_base/error.tpl");
   die;
}


if($feature_messages != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

if($tiki_p_broadcast != 'y' ) {
  $smarty->assign('msg',tra("Permission denied"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}


if(!isset($_REQUEST['to'])) $_REQUEST['to']='';
if(!isset($_REQUEST['cc'])) $_REQUEST['cc']='';
if(!isset($_REQUEST['bcc'])) $_REQUEST['bcc']='';
if(!isset($_REQUEST['subject'])) $_REQUEST['subject']='';
if(!isset($_REQUEST['body'])) $_REQUEST['body']='';
if(!isset($_REQUEST['priority'])) $_REQUEST['priority']=3;

$smarty->assign('to',$_REQUEST['to']);
$smarty->assign('cc',$_REQUEST['cc']);
$smarty->assign('bcc',$_REQUEST['bcc']);
$smarty->assign('subject',$_REQUEST['subject']);
$smarty->assign('body',$_REQUEST['body']);
$smarty->assign('priority',$_REQUEST['priority']);

$smarty->assign('mid','messu-broadcast.tpl');

$smarty->assign('sent',0);

if(isset($_REQUEST['reply'])||isset($_REQUEST['replyall'])) {
  $messulib->flag_message($_SESSION['user'], $_REQUEST['msgId'], 'isReplied', 'y');
}

if(isset($_REQUEST['group'])) {
  if($_REQUEST['group']=='all') {
    $all_users = $userlib->get_users(0,-1,'login_desc','');
  } else {
    $all_users = $userlib->get_group_users($_REQUEST['group']);
  }
}


if(isset($_REQUEST['send'])) {
  $smarty->assign('sent',1);
  $message = '';
  // Validation:
  // must have a subject or body non-empty (or both)
  if(empty($_REQUEST['subject'])&&empty($_REQUEST['body'])) {
    $smarty->assign('message','ERROR: Either the subject or body must be non-empty');
    $smarty->display('tiki.tpl');
    die;
  }
    
  
  // Remove invalid users from the to, cc and bcc fields
  $users = Array();
  foreach($all_users['data'] as $a_user) {
    $a_user = $a_user['user'];
    if(!empty($a_user)) {
      if($messulib->user_exists($a_user)) {
        if($messulib->get_user_preference($a_user,'allowMsgs','y')) {
          $users[] = $a_user;
        } else {
          $message.="User $a_user can not receive messages<br/>";
        }
      } else {
        $message.="Invalid user: $a_user<br/>";
      }
    }
  }
    
  $users = array_unique($users);
  
  // Validation: either to, cc or bcc must have a valid user
  if(count($users)>0) {
    $message.="Message will be sent to: ".implode(',',$users)."<br/>";
  } else {
    $message = 'ERROR: No valid users to send the message';
    $smarty->assign('message',$message);
    $smarty->display('tiki.tpl');
    die;
  }
 
  // Insert the message in the inboxes of each user
  foreach($users as $a_user) {
    $messulib->post_message($a_user,$_SESSION['user'],$a_user,'',$_REQUEST['subject'],$_REQUEST['body'],$_REQUEST['priority']);
  }
  
  $smarty->assign('message',$message);
}

$groups = $userlib->get_groups(0,-1,'groupName_asc','');
$smarty->assign_by_ref('groups',$groups["data"]);

include_once('tiki-mytiki_shared.php');
$smarty->display('tiki.tpl');

?>