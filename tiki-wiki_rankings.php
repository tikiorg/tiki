<?php
// Initialization
require_once('tiki-setup.php');

if($feature_wiki != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display('error.tpl');
  die;  
}

if($feature_wiki_rankings != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display('error.tpl');
  die;  
}

if($tiki_p_view != 'y') {
  $smarty->assign('msg',tra("Permission denied you cannot view this section"));
  $smarty->display('error.tpl');
  die;  
}


// Get the page from the request var or default it to HomePage
if(!isset($_REQUEST["limit"])) {
  $limit = 10;
} else {
  $limit = $_REQUEST["limit"];
}

$allrankings = Array(
  Array( 'name'=> 'Top pages', 'value'=>tra('wiki_ranking_top_pages')),
  Array( 'name'=> 'Last pages', 'value'=>tra('wiki_ranking_last_pages')),
  Array( 'name'=> 'Most relevant pages', 'value'=>tra('wiki_ranking_top_pagerank')),
  Array( 'name'=> 'Top authors', 'value'=>tra('wiki_ranking_top_authors'))
);
$smarty->assign('allrankings',$allrankings);

if(!isset($_REQUEST["which"])) {
  $which = 'wiki_ranking_top_pages';
} else {
  $which = $_REQUEST["which"];
}
$smarty->assign('which',$which);

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
$smarty->assign('rpage','tiki-wiki_rankings.php');
// Display the template
$smarty->assign('mid','tiki-ranking.tpl');
$smarty->display('tiki.tpl');
?>
