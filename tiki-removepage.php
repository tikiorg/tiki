<?php
// Initialization
require_once('tiki-setup.php');

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
if($tiki_p_remove != 'y') {
  $smarty->assign('msg',tra("Permission denied you cannot remove versions from this page"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}


if($_REQUEST["version"]<>"last") {
  $smarty->assign_by_ref('version',$_REQUEST["version"]);  
  $version = $_REQUEST["version"];
} else {
  $smarty->assign('version','last'); 
  $version = "last";
}


// If the page doesn't exist then display an error
if(!$tikilib->page_exists($page)) {
  $smarty->assign('msg',tra("Page cannot be found"));
  $smarty->display("styles/$style_base/error.tpl");
  die;
}

if(isset($_REQUEST["remove"])) {
  if(isset($_REQUEST["all"]) && $_REQUEST["all"]=='on') {
    $tikilib->remove_all_versions($_REQUEST["page"]);
    header("location: tiki-index.php");
    die; 
  } else {
    if($version=="last") {
      $tikilib->remove_last_version($_REQUEST["page"]);
    } else {
      $tikilib->remove_version($_REQUEST["page"],$_REQUEST["version"]);
    }
    header("location: tiki-index.php");
    die; 
  }  
}

$smarty->assign('mid','tiki-removepage.tpl');
$smarty->assign('show_page_bar','y');
$smarty->display("styles/$style_base/tiki.tpl");
?>