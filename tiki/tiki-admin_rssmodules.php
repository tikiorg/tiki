<?php
// Initialization
require_once('tiki-setup.php');
include_once('lib/rss/rsslib.php');

if($tiki_p_admin != 'y') {
    $smarty->assign('msg',tra("You dont have permission to use this feature"));
    $smarty->display("styles/$style_base/error.tpl");
    die;
}

if(!isset($_REQUEST["rssId"])) {
  $_REQUEST["rssId"] = 0;
}
$smarty->assign('rssId',$_REQUEST["rssId"]);

$smarty->assign('preview','n');
if(isset($_REQUEST["view"])) {
  $smarty->assign('preview','y');
  $data = $rsslib->get_rss_module_content($_REQUEST["view"]);
  $items = $rsslib->parse_rss_data($data);
  
  $smarty->assign_by_ref('items',$items);
}

if($_REQUEST["rssId"]) {
  $info = $tikilib->get_rss_module($_REQUEST["rssId"]);
} else {
  $info = Array();
  $info["name"]='';
  $info["description"]='';
  $info["url"]='';
  $info["refresh"]=15;
}

$smarty->assign('name',$info["name"]);
$smarty->assign('description',$info["description"]);
$smarty->assign('url',$info["url"]);
$smarty->assign('refresh',$info["refresh"]);

if(isset($_REQUEST["remove"])) {
  $rsslib->remove_rss_module($_REQUEST["remove"]);
}

if(isset($_REQUEST["save"])) {
  $rsslib->replace_rss_module($_REQUEST["rssId"], $_REQUEST["name"], $_REQUEST["description"], $_REQUEST["url"], $_REQUEST["refresh"]);
  $smarty->assign('rssId',0);
  $smarty->assign('name','');
  $smarty->assign('description','');
  $smarty->assign('url','');
  $smarty->assign('refresh',900);
  
}

if(!isset($_REQUEST["sort_mode"])) {
  $sort_mode = 'name_desc'; 
} else {
  $sort_mode = $_REQUEST["sort_mode"];
} 

if(!isset($_REQUEST["offset"])) {
  $offset = 0;
} else {
  $offset = $_REQUEST["offset"]; 
}
$smarty->assign_by_ref('offset',$offset);

if(isset($_REQUEST["find"])) {
  $find = $_REQUEST["find"];  
} else {
  $find = ''; 
}
$smarty->assign('find',$find);

$smarty->assign_by_ref('sort_mode',$sort_mode);
$channels = $rsslib->list_rss_modules($offset,$maxRecords,$sort_mode,$find);

$cant_pages = ceil($channels["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages',$cant_pages);
$smarty->assign('actual_page',1+($offset/$maxRecords));
if($channels["cant"] > ($offset+$maxRecords)) {
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

$smarty->assign_by_ref('channels',$channels["data"]);


// Display the template
$smarty->assign('mid','tiki-admin_rssmodules.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>