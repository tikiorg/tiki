<?php
// Initialization
require_once('tiki-setup.php');

if($feature_ranking != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display('error.tpl');
  die;  
}


// Now check permissions to access this page
if($tiki_p_view != 'y') {
  $smarty->assign('msg',tra("Permission denied you cannot view pages"));
  $smarty->display('error.tpl');
  die;  
}



// Get the page from the request var or default it to HomePage
if(!isset($_REQUEST["limit"])) {
  $limit = 10;
} else {
  $limit = $_REQUEST["limit"];
}

$smarty->assign_by_ref('limit',$limit);


$ranking = $tikilib->get_top_pages($limit);
$smarty->assign_by_ref('ranking',$ranking);

// Display the template
$smarty->assign('mid','tiki-ranking.tpl');
$smarty->display('tiki.tpl');
?>
