<?php
// Initialization
require_once('tiki-setup.php');

if($feature_wiki_multiprint != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

if(!isset($_REQUEST["printpages"])) {
  $smarty->assign('msg',tra("No pages indicated"));
  $smarty->display("styles/$style_base/error.tpl");
  die;
} else {
  $printpages = unserialize(urldecode($_REQUEST["printpages"]));
}


if(isset($_REQUEST["print"])) {
  // Create XMLRPC object
  $pages = Array();  
  foreach($printpages as $page) {
    
    // If the page doesn't exist then display an error
    if(!$tikilib->page_exists($page)) {
      $smarty->assign('msg',tra("Page cannot be found"));
      $smarty->display("styles/$style_base/error.tpl");
      die;
    }
    // Now check permissions to access this page
    if($tiki_p_view != 'y') {
      $smarty->assign('msg',tra("Permission denied you cannot view this page"));
      $smarty->display("styles/$style_base/error.tpl");
      die;  
    }

    $page_info = $tikilib->get_page_info($page);
    $page_info["parsed"]=$tikilib->parse_data($page_info["data"]);
    $pages[] = $page_info;
  }
}  
$smarty->assign_by_ref('pages',$pages);  

// Display the template
$smarty->display("styles/$style_base/tiki-print_multi_pages.tpl");
?>
