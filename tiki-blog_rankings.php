<?php
// Initialization
require_once('tiki-setup.php');
include_once('lib/rankings/ranklib.php');

if($feature_blogs != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

if($feature_blog_rankings != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

if($tiki_p_read_blog != 'y') {
  $smarty->assign('msg',tra("Permission denied you cannot view this section"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

$allrankings = Array(
  Array( 'name'=> tra('Top visited blogs'), 'value'=> 'blog_ranking_top_blogs'),
  Array( 'name'=> tra('Last posts'),        'value'=> 'blog_ranking_last_posts'),
  Array( 'name'=> tra('Top active blogs'),  'value'=> 'blog_ranking_top_active_blogs')
);
$smarty->assign('allrankings',$allrankings);

if(!isset($_REQUEST["which"])) {
  $which = 'blog_ranking_top_blogs';
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
$smarty->assign('rpage','tiki-blog_rankings.php');
// Display the template
$smarty->assign('mid','tiki-ranking.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>
