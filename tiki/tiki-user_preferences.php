<?php
// Initialization
require_once('tiki-setup.php');

// User preferences screen

if($feature_userPreferences != 'y') {
   $smarty->assign('msg',tra("This feature is disabled"));
   $smarty->display('error.tpl');
   die;
}

if(empty($user)) {
   $smarty->assign('msg',tra("You are not logged in"));
   $smarty->display('error.tpl');
   die;
}


if(isset($_REQUEST["view_user"])) {
if($tiki_p_admin == 'y') {
  $userwatch = $_REQUEST["view_user"];
} else {
  $smarty->assign('msg',tra("You dont have permission to view other users data"));
   $smarty->display('error.tpl');
   die;
}
}
$userwatch = $user;


$foo = parse_url($_SERVER["REQUEST_URI"]);
$foo1=str_replace("tiki-user_preferences","tiki-editpage",$foo["path"]);
$foo2=str_replace("tiki-user_preferences","tiki-index",$foo["path"]);
$smarty->assign('url_edit',$_SERVER["SERVER_NAME"].$foo1);
$smarty->assign('url_visit',$_SERVER["SERVER_NAME"].$foo2);


if(isset($_REQUEST["prefs"])) {
  // setting preferences
  $userlib->change_user_email($userwatch,$_REQUEST["email"]);
  $tikilib->set_user_preference($userwatch,'theme',$_REQUEST["style"]);
  $tikilib->set_user_preference($userwatch,'realName',$_REQUEST["realName"]);
  $tikilib->set_user_preference($userwatch,'homePage',$_REQUEST["homePage"]);
}

if(isset($_REQUEST["chgpswd"])) {
  if($_REQUEST["pass1"]!=$_REQUEST["pass2"]) {
    $smarty->assign('msg',tra("The passwords didn't match"));
    $smarty->display('error.tpl');
    die;
  }
  $old = $userlib->get_user_password($userwatch);
  if($old != $_REQUEST["old"]) {
    $smarty->assign('msg',tra("Invalid old password"));
    $smarty->display('error.tpl');
    die;
  }
  $userlib->change_user_password($userwatch,$_REQUEST["pass1"]);
}

$userinfo = $userlib->get_user_info($userwatch);
$smarty->assign_by_ref('userinfo',$userinfo);

$styles=Array();
$h=opendir("styles/");
while($file=readdir($h)) {
  if(strstr($file,"css")) {
    $styles[]=$file;
  }
}
closedir($h);
$smarty->assign_by_ref('styles',$styles);

// Get user pages
$user_pages = $tikilib->get_user_pages($userwatch,-1);
$user_blogs = $tikilib->list_user_blogs($userwatch,false);
$user_galleries = $tikilib->get_user_galleries($userwatch, -1);
$smarty->assign_by_ref('user_pages',$user_pages);
$smarty->assign_by_ref('user_blogs',$user_blogs);
$smarty->assign_by_ref('user_galleries',$user_galleries);

// Get user blogs


// Get user galleries

// Get preferences
$style = $tikilib->get_user_preference($userwatch,'theme','default.css');
$smarty->assign_by_ref('style',$style);
$realName = $tikilib->get_user_preference($userwatch,'realName','');
$smarty->assign_by_ref('realName',$realName);
$homePage = $tikilib->get_user_preference($userwatch,'homePage','');
$smarty->assign_by_ref('homePage',$homePage);


$smarty->assign('mid','tiki-user_preferences.tpl');
$smarty->display('tiki.tpl');
?>