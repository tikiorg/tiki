<?php
// Initialization
require_once('tiki-setup.php');
include_once('lib/wiki/wikilib.php');

if($feature_wiki != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}


// Create the HomePage if it doesn't exist
if(!$tikilib->page_exists($wikiHomePage)) {
  $tikilib->create_page($wikiHomePage,0,'',date("U"),'Tiki initialization'); 
}


// Get the page from the request var or default it to HomePage
if(!isset($_REQUEST["page"])) {
  $page = $wikiHomePage;
  $smarty->assign('page',$wikiHomePage); 
} else {
  $page = $_REQUEST["page"];
  $smarty->assign_by_ref('page',$_REQUEST["page"]); 
}

require_once('tiki-pagesetup.php');

// Check if we have to perform an action for this page
// for example lock/unlock
if(isset($_REQUEST["action"])) {
  if($_REQUEST["action"]=='lock') {
    $wikilib->lock_page($page);
  } elseif ($_REQUEST["action"]=='unlock') {
    $wikilib->unlock_page($page);
  }  
}


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


// Now increment page hits since we are visiting this page
if($count_admin_pvs == 'y' || $user!='admin') {
  $tikilib->add_hit($page);
}

// Get page data
$info = $tikilib->get_page_info($page);
if(isset($wiki_feature_copyrights) && $wiki_feature_copyrights == 'y' && isset($wikiLicensePage)) {
  // insert license if wiki copyrights enabled
  $license_info = $tikilib->get_page_info($wikiLicensePage);
  $tikilib->add_hit($wikiLicensePage);
  $info["data"] = $info["data"]."\n<HR>\n".$license_info["data"];
  $_REQUEST['copyrightpage'] = $page;
}

// Verify lock status
if($info["flag"] == 'L') {
  $smarty->assign('lock',true);  
} else {
  $smarty->assign('lock',false);
}
$pdata = $tikilib->parse_data($info["data"]);
$smarty->assign_by_ref('parsed',$pdata);
$smarty->assign_by_ref('lastModif',$info["lastModif"]);
if(empty($info["user"])) {
  $info["user"]='anonymous';  
}
$smarty->assign_by_ref('lastUser',$info["user"]);

//Store the page URL to be displayed on print page
$http_domain = $tikilib->get_preference('http_domain', false);
$http_port = $tikilib->get_preference('http_port', 80);
$http_prefix = $tikilib->get_preference('http_prefix', '/');
if ($http_domain) {
	$prefix = 'http://' . $http_domain;
	if ($http_port != 80)
		$prefix .= ':' . $http_port;
	$prefix .= $https_prefix;
	$smarty->assign('urlprefix',$prefix);
}


// Display the Index Template

$creator = $wikilib->get_creator($page);
$smarty->assign('creator',$creator);
$smarty->assign('mid','tiki-show_page.tpl');
$smarty->assign('show_page_bar','n');
$smarty->assign('print_page','y');
$smarty->display("styles/$style_base/tiki-print.tpl");

?>
