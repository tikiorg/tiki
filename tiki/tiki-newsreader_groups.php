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

if(!isset($_REQUEST["serverId"])) {
  $smarty->assign('msg',tra("No server indicated"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}


if($_REQUEST["serverId"]) {
  $info = $newslib->get_server($user,$_REQUEST["serverId"]);
} 

$smarty->assign('serverId',$_REQUEST["serverId"]);
$smarty->assign('info',$info);

if(!$newslib->news_set_server($info['server'],$info['port'],$info['username'],$info['password'])) {
  $smarty->assign('msg',tra("Cannot connect to").':'.$info['server']);
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}
$groups = $newslib->news_get_groups();
$smarty->assign_by_ref('groups',$groups);
//print_r($groups);

include_once('tiki-mytiki_shared.php');

$section='newsreader';
include_once('tiki-section_options.php');

$smarty->assign('mid','tiki-newsreader_groups.tpl');
$smarty->display('tiki.tpl');
?>