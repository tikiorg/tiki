<?php
require_once('tiki-setup.php');
include_once('lib/charts/chartlib.php');


if($feature_charts != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

if(!isset($_REQUEST['itemId'])) {
  $smarty->assign('msg',tra("No itemindicated"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  

}


$info = $chartlib->get_chart_item($_REQUEST["itemId"]);
$chart_info = $chartlib->get_chart($info['chartId']);
$smarty->assign_by_ref('info',$info);
$smarty->assign_by_ref('chart_info',$chart_info);
$smarty->assign('chartId',$info['chartId']);
$smarty->assign('itemId',$_REQUEST['itemId']);

$smarty->assign('user_voted_chart',$chartlib->user_has_voted_chart($user,$info['chartId'])?'y':'n');
$smarty->assign('user_voted_item',$chartlib->user_has_voted_item($user,$info['itemId'])?'y':'n');

$sameurl_elements = Array('offset','sort_mode','where','find','chartId','itemId');

$smarty->assign('mid','tiki-view_chart_item.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>