<?php
// Initialization
require_once('tiki-setup.php');

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

if($tiki_p_admin != 'y') {
  $user_information = $tikilib->get_user_preference($userwatch,'user_information','public');
  if($user_information == 'private') {
    $smarty->assign('msg',tra("The user has choosen to make his information private"));
    $smarty->display("styles/$style_base/error.tpl");
    die;
  }
}

$user_style = $tikilib->get_user_preference($userwatch,'theme',$style);
$language = $tikilib->get_user_preference($userwatch,'language',$language);
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

$smarty->assign('mid','tiki-user_information.tpl');
$smarty->display("styles/$style_base/tiki.tpl");

?>