<?php
require_once('tiki-setup.php');
include_once('lib/newsreader/newslib.php');

if(!$user) {
   $smarty->assign('msg',tra("You are not logged in"));
   $smarty->display("styles/$style_base/error.tpl");
   die;
}


if($feature_newsreader != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

if((!isset($_REQUEST['server']))||(!isset($_REQUEST['port']))||(!isset($_REQUEST['username']))
   ||(!isset($_REQUEST['password']))||(!isset($_REQUEST['group']))) {
  $smarty->assign('msg',tra("Missing information to read news (server,port,username,password,group) required"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

$smarty->assign('server',$_REQUEST['server']);
$smarty->assign('port',$_REQUEST['port']);
$smarty->assign('username',$_REQUEST['username']);
$smarty->assign('password',$_REQUEST['password']);
$smarty->assign('group',$_REQUEST['group']);

if(!$newslib->news_set_server($_REQUEST['server'],$_REQUEST['port'],$_REQUEST['username'],$_REQUEST['password'])) {
  $smarty->assign('msg',tra("Cannot connect to").':'.$info['server']);
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}
$info = $newslib->news_select_group($_REQUEST['group']);
if(!$info) {
  $smarty->assign('msg',tra("Cannot get messages"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}
print_r($info);

$smarty->assign('mid','tiki-newsreader_news.tpl');
$smarty->display('tiki.tpl');
?>