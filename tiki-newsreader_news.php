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

//Now calculate all the offsets using maxRecords and offset
//then load headers for messages between the first and last message to be displayed
//Assign the headers information to the articles array to be displayed in the template
//calculate next and prev offsets, page number and so...
if(!isset($_REQUEST["offset"])) {
  $offset = 0;
} else {
  $offset = $_REQUEST["offset"]; 
}
$smarty->assign_by_ref('offset',$offset);
$cant = $info['last']-$info['first']+1;
$cant_pages = ceil($cant / $maxRecords);
$smarty->assign_by_ref('cant_pages',$cant_pages);
$smarty->assign('actual_page',1+($offset/$maxRecords));
if($cant > ($offset+$maxRecords)) {
  $smarty->assign('next_offset',$offset + $maxRecords);
} else {
  $smarty->assign('next_offset',-1); 
}
// If offset is > 0 then prev_offset
if($offset>0) {
  $smarty->assign('prev_offset',$offset - $maxRecords);  
} else {
  $smarty->assign('prev_offset',-1); 
}
// Since the first message is the last one...
$count=0;
$articles=Array();
for($i=$info['last']-$offset;$count<$maxRecords&&$i>=$info['first'];$i--) {
  $count++;
  $art=$newslib->news_split_headers($i);
  $art['loopid']=$i;
  $articles[]=$art;
  
}
$smarty->assign('articles',$articles);
print_r(array_keys($articles[0]));


$smarty->assign('mid','tiki-newsreader_news.tpl');
$smarty->display('tiki.tpl');
?>