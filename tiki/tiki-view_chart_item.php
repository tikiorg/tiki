<?php
require_once('tiki-setup.php');
include_once('lib/charts/chartlib.php');

//xdebug_start_profiling();

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
$user_voted_chart = $chartlib->user_has_voted_chart($user,$info['chartId'])?'y':'n';
$smarty->assign_by_ref('user_voted_chart',$user_voted_chart);
$user_voted_item = $chartlib->user_has_voted_item($user,$info['itemId'])?'y':'n';
$smarty->assign_by_ref('user_voted_item',$user_voted_item);


if(isset($_REQUEST['vote'])) {
  if( ($tiki_p_admin_charts == 'y') ||
  	 (($chart_info['singleChartVotes'] == 'n' || $user_voted_chart == 'n')
     &&
      ($chart_info['singleItemVotes'] == 'n' || $user_voted_item == 'n'))
    ) 
  {
    if(!isset($_REQUEST['points'])) $_REQUEST['points']=0;
    $chartlib->user_vote($user,$_REQUEST['itemId'],$_REQUEST['points']); 
  }

}

$info = $chartlib->get_chart_item($_REQUEST["itemId"]);
$user_voted_chart = $chartlib->user_has_voted_chart($user,$info['chartId'])?'y':'n';
$user_voted_item = $chartlib->user_has_voted_item($user,$info['itemId'])?'y':'n';

$sameurl_elements = Array('offset','sort_mode','where','find','chartId','itemId');

$smarty->assign('mid','tiki-view_chart_item.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>