<?php
// Initialization
require_once('tiki-setup.php');
include_once('lib/messu/messulib.php');

if(!$user) {
   $smarty->assign('msg',tra("You are not logged in"));
   $smarty->display("styles/$style_base/error.tpl");
   die;
}

if($feature_contact != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}


$smarty->assign('mid','tiki-contact.tpl');

$email = $userlib->get_user_email($contact_user);
$smarty->assign('email',$email);

if($user and $feature_messages == 'y' and $tiki_p_messages == 'y') {
$smarty->assign('sent',0);
if(isset($_REQUEST['send'])) {
  $smarty->assign('sent',1);
  
  $message = '';
  
  // Validation:
  // must have a subject or body non-empty (or both)
  if(empty($_REQUEST['subject'])&&empty($_REQUEST['body'])) {
    $smarty->assign('message',tra('ERROR: Either the subject or body must be non-empty'));
    $smarty->display("styles/$style_base/tiki.tpl");
    die;
  }
  $message = tra('Message sent to').':'.$contact_user.'<br/>';
  $messulib->post_message($contact_user,$user,$_REQUEST['to'],'',$_REQUEST['subject'],$_REQUEST['body'],$_REQUEST['priority']);
  
  $smarty->assign('message',$message);
}
}

$smarty->assign('priority',3);

$smarty->display("styles/$style_base/tiki.tpl");
?>
