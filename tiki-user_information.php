<?php
// Initialization
require_once('tiki-setup.php');
include_once('lib/messu/messulib.php');

if(isset($_REQUEST['view_user'])) {
  $userwatch = $_REQUEST['view_user'];
} else {
  if($user) {
    $userwatch = $user;
  } else {
    $smarty->assign('msg',tra("You are not logged in and no user indicated"));
    $smarty->display("styles/$style_base/error.tpl");
    die;
  }
}
$smarty->assign('userwatch',$userwatch);

if(!$userlib->user_exists($userwatch)) {
    $smarty->assign('msg',tra("Unknown user"));
    $smarty->display("styles/$style_base/error.tpl");
    die;
}

if($tiki_p_admin != 'y') {
  $user_information = $tikilib->get_user_preference($userwatch,'user_information','public');
  if($user_information == 'private') {
    $smarty->assign('msg',tra("The user has choosen to make his information private"));
    $smarty->display("styles/$style_base/error.tpl");
    die;
  }
}

$smarty->assign('mid','tiki-user_information.tpl');

if($user) {
$smarty->assign('sent',0);
if(isset($_REQUEST['send'])) {
  $smarty->assign('sent',1);
  
  $message = '';
  
  // Validation:
  // must have a subject or body non-empty (or both)
  if(empty($_REQUEST['subject'])&&empty($_REQUEST['body'])) {
    $smarty->assign('message','ERROR: Either the subject or body must be non-empty');
	$smarty->display("styles/$style_base/tiki.tpl");
    die;
  }
  $message = tra('Message sent to').':'.$userwatch.'<br/>';
  $messulib->post_message($userwatch,$user,$_REQUEST['to'],'',$_REQUEST['subject'],$_REQUEST['body'],$_REQUEST['priority']);
  
  $smarty->assign('message',$message);
}
}

$smarty->assign('priority',3);
$allowMsgs = $tikilib->get_user_preference($userwatch,'allowMsgs','y');
$smarty->assign('allowMsgs',$allowMsgs);
$user_style = $tikilib->get_user_preference($userwatch,'theme',$style);
$user_language = $tikilib->get_user_preference($userwatch,'language',$language);
$smarty->assign_by_ref('user_language',$user_language);
$smarty->assign_by_ref('user_style',$user_style);
$realName = $tikilib->get_user_preference($userwatch,'realName','');
$country = $tikilib->get_user_preference($userwatch,'country','Other');
$smarty->assign('country',$country);
$anonpref = $tikilib->get_preference('userbreadCrumb',4);
$userbreadCrumb = $tikilib->get_user_preference($userwatch,'userbreadCrumb',$anonpref);
$smarty->assign_by_ref('realName',$realName);
$smarty->assign_by_ref('userbreadCrumb',$userbreadCrumb);
$homePage = $tikilib->get_user_preference($userwatch,'homePage','');
$smarty->assign_by_ref('homePage',$homePage);

$avatar = $tikilib->get_user_avatar($userwatch);
$smarty->assign('avatar',$avatar);

$user_information = $tikilib->get_user_preference($userwatch,'user_information','public');
$smarty->assign('user_information',$user_information);

$timezone_options = $tikilib->get_timezone_list(true);
$smarty->assign_by_ref('timezone_options',$timezone_options);
$server_time = new Date();
$display_timezone = $tikilib->get_user_preference($userwatch,'display_timezone', $server_time->tz->getID());
$smarty->assign_by_ref('display_timezone',$display_timezone);

$userinfo = $userlib->get_user_info($userwatch);
$smarty->assign_by_ref('userinfo',$userinfo);


$smarty->display("styles/$style_base/tiki.tpl");

?>