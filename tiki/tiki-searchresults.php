<?php
// Initialization
require_once('tiki-setup.php');

if($feature_search != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display('error.tpl');
  die;  
}

// Now check permissions to view pages
if($tiki_p_view != 'y') {
  $smarty->assign('msg',tra("Permission denied you cannot view pages"));
  $smarty->display('error.tpl');
  die;  
}

// Build the query using words
if( (!isset($_REQUEST["words"])) || (empty($_REQUEST["words"])) ) {
  $results = $tikilib->find_pages();
} else {
  $results = $tikilib->find_pages($_REQUEST["words"]);
}

// Find search results (build array)
$smarty->assign_by_ref('results',$results);

// Display the template
$smarty->assign('mid','tiki-searchresults.tpl');
$smarty->display('tiki.tpl');
?>
