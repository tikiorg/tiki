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

if($tiki_p_messages != 'y' ) {
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

$smarty->assign('mid','messu-compose.tpl');

$smarty->assign('sent',0);

if(isset($_REQUEST['reply'])||isset($_REQUEST['replyall'])) {
  $messulib->flag_message($_SESSION['user'], $_REQUEST['msgId'], 'isReplied', 'y');
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
    
  // Parse the to, cc and bcc fields into an array
  $arr_to = explode(',',$_REQUEST['to']);
  $arr_cc = explode(',',$_REQUEST['cc']);
  $arr_bcc = explode(',',$_REQUEST['bcc']);
    
  // Remove invalid users from the to, cc and bcc fields
  $users = Array();
  foreach($arr_to as $a_user) {
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
  foreach($arr_cc as $a_user) {
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
  foreach($arr_bcc as $a_user) {
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
    $messulib->post_message($a_user,$_SESSION['user'],$_REQUEST['to'],$_REQUEST['cc'],$_REQUEST['subject'],$_REQUEST['body'],$_REQUEST['priority']);
  }
  
  $smarty->assign('message',$message);
}


if($feature_messages=='y' && $tiki_p_messages=='y') {
  $unread = $tikilib->user_unread_messages($user);
  $smarty->assign('unread',$unread);
}

$smarty->display('tiki.tpl');

?>