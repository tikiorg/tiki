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

if($tiki_p_newsreader != 'y') {
  $smarty->assign('msg',tra("Permission denied to use this feature"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}


if((!isset($_REQUEST['id']))||(!isset($_REQUEST['server']))||(!isset($_REQUEST['port']))||(!isset($_REQUEST['username']))
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
$smarty->assign('id',$_REQUEST['id']);
if(isset($_REQUEST['serverId'])) {
  $smarty->assign('serverId',$_REQUEST['serverId']);
} else {
  $smarty->assign('serverId',0);
}

if(!$newslib->news_set_server($_REQUEST['server'],$_REQUEST['port'],$_REQUEST['username'],$_REQUEST['password'])) {
  $smarty->assign('msg',tra("Cannot connect to").':'.$info['server']);
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

if(!isset($_REQUEST["offset"])) {
  $offset = 0;
} else {
  $offset = $_REQUEST["offset"]; 
}
$smarty->assign_by_ref('offset',$offset);

$info = $newslib->news_select_group($_REQUEST['group']);
if(!$info) {
  $smarty->assign('msg',tra("Cannot get messages"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

$smarty->assign('prev_article',0);
$smarty->assign('next_article',0);
if($_REQUEST['id']<$info['last']) {
  $smarty->assign('next_article',$_REQUEST['id']+1);
}
if($_REQUEST['id']>$info['first']) {
  $smarty->assign('prev_article',$_REQUEST['id']-1);
}
$smarty->assign('last',$info['first']);
$smarty->assign('first',$info['last']);


$headers = $newslib->news_split_headers($_REQUEST['id']);

//print_r(array_keys($headers));
$body = nl2br(htmlentities($newslib->news_get_body($_REQUEST['id'])));

$smarty->assign('headers',$headers);
$smarty->assign('body',$body);

$section='newsreader';
include_once('tiki-section_options.php');

include_once('tiki-mytiki_shared.php');

$smarty->assign('mid','tiki-newsreader_read.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>