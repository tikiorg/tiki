<?php
// Initialization
require_once('tiki-setup.php');

if($feature_galleries != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display('error.tpl');
  die;  
}

if($feature_gal_rankings != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display('error.tpl');
  die;  
}

$allrankings = Array(
  Array( 'name'=> 'Top galleries', 'value'=> tra('gal_ranking_top_galleries')),
  Array( 'name'=> 'Top images', 'value'=>tra('gal_ranking_top_images')),
  Array( 'name'=> 'Last images', 'value'=>tra('gal_ranking_last_images')),
);
$smarty->assign('allrankings',$allrankings);

if(!isset($_REQUEST["which"])) {
  $which = 'gal_ranking_top_galleries';
} else {
  $which = $_REQUEST["which"];
}
$smarty->assign('which',$which);


// Get the page from the request var or default it to HomePage
if(!isset($_REQUEST["limit"])) {
  $limit = 10;
} else {
  $limit = $_REQUEST["limit"];
}

$smarty->assign_by_ref('limit',$limit);

// Rankings:
// Top Pages
// Last pages
// Top Authors
$rankings=Array();

$rk = $tikilib->$which($limit);
$rank["data"] = $rk["data"];
$rank["title"] = $rk["title"];
$rank["y"]=$rk["y"];
$rankings[] = $rank;



$smarty->assign_by_ref('rankings',$rankings);
$smarty->assign('rpage','tiki-galleries_rankings.php');
// Display the template
$smarty->assign('mid','tiki-ranking.tpl');
$smarty->display('tiki.tpl');
?>
