<?php
require_once('tiki-setup.php');
include_once('lib/charts/chartlib.php');


if($feature_charts != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}



$where = ''; 
$wheres=Array();
if(isset($_REQUEST['where'])) {
  $where = $_REQUEST['where'];
}
if(!isset($_REQUEST["sort_mode"])) {  $sort_mode = 'created_desc'; } else {  $sort_mode = $_REQUEST["sort_mode"];} 
if(!isset($_REQUEST["offset"])) {  $offset = 0;} else {  $offset = $_REQUEST["offset"]; }$smarty->assign_by_ref('offset',$offset);
if(isset($_REQUEST["find"])) { $find = $_REQUEST["find"];  } else {  $find = ''; } $smarty->assign('find',$find);
$smarty->assign('where',$where); $smarty->assign_by_ref('sort_mode',$sort_mode);
$items = $chartlib->list_charts($offset,$maxRecords,$sort_mode,$find,$where);
$smarty->assign('cant',$items['cant']);
$cant_pages = ceil($items["cant"] / $maxRecords);$smarty->assign_by_ref('cant_pages',$cant_pages);$smarty->assign('actual_page',1+($offset/$maxRecords));
if($items["cant"] > ($offset+$maxRecords)) {  $smarty->assign('next_offset',$offset + $maxRecords);} else {  $smarty->assign('next_offset',-1); }
if($offset>0) {  $smarty->assign('prev_offset',$offset - $maxRecords);  } else {  $smarty->assign('prev_offset',-1); }
$smarty->assign_by_ref('items',$items["data"]);

$sameurl_elements = Array('offset','sort_mode','where','find','chartId');

$smarty->assign('mid','tiki-charts.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?> 
