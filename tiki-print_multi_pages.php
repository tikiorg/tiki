<?php
// Initialization
require_once('tiki-setup.php');

if($feature_wiki_multiprint != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display('error.tpl');
  die;  
}

if(!isset($_REQUEST["printpages"])) {
  $smarty->assign('msg',tra("No pages indicated"));
  $smarty->display('error.tpl');
  die;
} else {
  $printpages = unserialize(urldecode($printpages));
}


if(isset($_REQUEST["print"])) {
  // Create XMLRPC object
  $pages = Array();  
  foreach($printpages as $page) {
    $page_info = $tikilib->get_page_info($page);
    $page_info["parsed"]=$tikilib->parse_data($page_info["data"]);
    $pages[] = $page_info;
  }
}  
$smarty->assign_by_ref('pages',$pages);  

// Display the template
$smarty->display('tiki-print_multi_pages.tpl');
?>