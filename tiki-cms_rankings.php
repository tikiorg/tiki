<?php
// Initialization
require_once('tiki-setup.php');
include_once('lib/rankings/ranklib.php');

if($feature_articles != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

if($feature_cms_rankings != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

if($tiki_p_read_article != 'y') {
  $smarty->assign('msg',tra("Permission denied you cannot view this section"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

$allrankings = Array(
  Array( 'name'=> tra('Top articles'), 'value'=> 'cms_ranking_top_articles'),
  Array( 'name'=> tra('Top authors'), 'value'=> 'cms_ranking_top_authors')
  
);
$smarty->assign('allrankings',$allrankings);

if(!isset($_REQUEST["which"])) {
  $which = 'cms_ranking_top_articles';
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

$rk = $ranklib->$which($limit);
$rank["data"] = $rk["data"];
$rank["title"] = $rk["title"];
$rank["y"]=$rk["y"];
$rankings[] = $rank;



$smarty->assign_by_ref('rankings',$rankings);
$smarty->assign('rpage','tiki-cms_rankings.php');
// Display the template
$smarty->assign('mid','tiki-ranking.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>
