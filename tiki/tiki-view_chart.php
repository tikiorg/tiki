<?php
require_once('tiki-setup.php');
include_once('lib/charts/chartlib.php');

if($feature_charts != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

if(!isset($_REQUEST['chartId'])) {
  $smarty->assign('msg',tra("No chart indicated"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  

}
$chart_info = $chartlib->get_chart($_REQUEST["chartId"]);
$smarty->assign_by_ref('chart_info',$chart_info);

// Regenerate the ranking if no ranking is found or if
// the last ranking is too old for the frequency
if(!$chartlib->ranking_exists($chart_info['chartId']) ||
    ($chart_info['lastChart']+($chart_info['frequency']*24*60*60)) < $now) {
  if($chart_info['frequency']==0) $chartlib->drop_rankings($chart_info['chartId']);  
  $chartlib->generate_new_ranking($chart_info['chartId']);    
}

// If no period indicated then period is last
// Note that there's always at least one period because the ranking is
// generated if not existed
if(!isset($_REQUEST['period'])) {
  $_REQUEST['period']=$chartlib->get_last_period($_REQUEST['chartId']);
}

// If the chart is not realtime then build links to the
// next and previous periods if they exist
if($chart_info['frequency']) {
  $lastPeriod = $chartlib->get_last_period($chart_info['chartId']);
  $firstPeriod = $chartlib->get_first_period($chart_info['chartId']);
  if($firstPeriod && $firstPeriod < $_REQUEST['period']) {
    $smarty->assign('prevPeriod',$_REQUEST['period']-1);
  } else {
    $smarty->assign('prevPeriod',0);
  }
  if($lastPeriod && $lastPeriod > $_REQUEST['period']) {
    $smarty->assign('nextPeriod',$_REQUEST['period']+1);
  } else {
    $smarty->assign('nextPeriod',0);
  }
}

// Purge user votes that are too old using voteagainafter
$chartlib->purge_user_votes($chart_info['chartId'],$chart_info['voteAgainAfter']);

// determine if the user has voted this chart or not
$user_voted_chart = $chartlib->user_has_voted_chart($user,$chart_info['chartId']);
$smarty->assign('user_voted_chart',$user_voted_chart?'y':'n');

// now get the ranking items
$items = $chartlib->get_ranking($chart_info['chartId'],$_REQUEST['period']);
$smarty->assign_by_ref('items',$items);

$smarty->assign('mid','tiki-view_chart.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?> 
