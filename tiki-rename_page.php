<?php
// Initialization
require_once('tiki-setup.php');
include_once('lib/wiki/wikilib.php');

if($feature_wiki != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}


// Get the page from the request var or default it to HomePage
if(!isset($_REQUEST["page"])) {
  $smarty->assign('msg',tra("No page indicated"));
  $smarty->display("styles/$style_base/error.tpl");
  die;
} else {
  $page = $_REQUEST["page"];
  $smarty->assign_by_ref('page',$_REQUEST["page"]); 
}

include_once("tiki-pagesetup.php");
// Now check permissions to access this page
if($tiki_p_rename != 'y') {
  $smarty->assign('msg',tra("Permission denied you cannot remove versions from this page"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

// If the page doesn't exist then display an error
if(!$tikilib->page_exists($page)) {
  $smarty->assign('msg',tra("Page cannot be found"));
  $smarty->display("styles/$style_base/error.tpl");
  die;
}

if(isset($_REQUEST["rename"])) {
print("ren");
$wikilib->wiki_rename_page($_REQUEST['oldpage'],$_REQUEST['newpage']);
}

$smarty->assign('mid','tiki-rename_page.tpl');
$smarty->assign('show_page_bar','y');
$smarty->display("styles/$style_base/tiki.tpl");
?>