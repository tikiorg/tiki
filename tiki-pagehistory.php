<?php
// Initialization
require_once('tiki-setup.php');

if($feature_wiki != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}


if($feature_history != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}



// Get the page from the request var or default it to HomePage
if(!isset($_REQUEST["page"])) {
  $smarty->assign('msg',tra("No page indicated"));
  $smarty->display("styles/$style_base/error.tpl");
  die;
  header("location: tiki-index.php");
} else {
  $page = $_REQUEST["page"];
  $smarty->assign_by_ref('page',$_REQUEST["page"]); 
}



include_once("tiki-pagesetup.php");
// Now check permissions to access this page
if($tiki_p_view != 'y') {
  $smarty->assign('msg',tra("Permission denied you cannot browse this page history"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}


// If the page doesn't exist then display an error
if(!$tikilib->page_exists($page)) {
  $smarty->assign('msg',tra("Page cannot be found"));
  $smarty->display("styles/$style_base/error.tpl");
  die;
}

if(isset($_REQUEST["delete"])) {
  foreach(array_keys($_REQUEST["hist"]) as $version) {      	
    $tikilib->remove_version($_REQUEST["page"],$version);
  }
}


$smarty->assign('source',0);
if(isset($_REQUEST['source'])) {
  $smarty->assign('source',$_REQUEST['source']);
  $version = $tikilib->get_version($page,$_REQUEST["source"]);
  $smarty->assign('sourcev',nl2br(htmlentities($version["data"])));  
}

// If we have to include a preview please show it
$smarty->assign('preview',false);
if(isset($_REQUEST["preview"])) {
  $version = $tikilib->get_version($page,$_REQUEST["preview"]);
  $version["data"] = $tikilib->parse_data($version["data"]);
  if($version) {
    $smarty->assign_by_ref('preview',$version);
    $smarty->assign_by_ref('version',$_REQUEST["preview"]); 
  } 
}

$smarty->assign('diff2','n');
if(isset($_REQUEST["diff2"])) {
  $diff = $tikilib->get_version($page,$_REQUEST["diff2"]);
  $info = $tikilib->get_page_info($page);
  $html = $tikilib->diff2($diff["data"],$info["data"]);
  $smarty->assign('diffdata',$html);
  $smarty->assign('diff2','y');
  $smarty->assign_by_ref('version',$_REQUEST["diff2"]);
}

// We are going to change this to "compare" instead of diff
$smarty->assign('diff',false);
if(isset($_REQUEST["diff"])) {
  $diff = $tikilib->get_version($page,$_REQUEST["diff"]);
  $diff["data"] = $tikilib->parse_data($diff["data"]);
  $smarty->assign_by_ref('diff',$diff["data"]);
  $info = $tikilib->get_page_info($page);
  $pdata = $tikilib->parse_data($info["data"]);
  $smarty->assign_by_ref('parsed',$pdata); 
  $smarty->assign_by_ref('version',$_REQUEST["diff"]);
}

$info = $tikilib->get_page_info($page);
$smarty->assign_by_ref('info',$info);

$history = $tikilib->get_page_history($page);
$smarty->assign_by_ref('history',$history);

// Display the template
$smarty->assign('mid','tiki-pagehistory.tpl');
$smarty->assign('show_page_bar','y');
$smarty->display("styles/$style_base/tiki.tpl");
?>
