<?php
// Initialization
require_once('tiki-setup.php');

if($feature_wiki != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

//print($GLOBALS["HTTP_REFERER"]);

// Create the HomePage if it doesn't exist
if(!$tikilib->page_exists("HomePage")) {
  $tikilib->create_page("HomePage",0,'',date("U"),'Tiki initialization'); 
}

if(!isset($_SESSION["thedate"])) {
  $thedate = date("U");
} else {
  $thedate = $_SESSION["thedate"];
}

// Get the page from the request var or default it to HomePage
if(!isset($_REQUEST["page"])) {
  $_REQUEST["page"]='HomePage';
  $page = 'HomePage';
  $smarty->assign('page','HomePage'); 
} else {
  $page = $_REQUEST["page"];
  $smarty->assign_by_ref('page',$_REQUEST["page"]); 
}

require_once('tiki-pagesetup.php');

// Check if we have to perform an action for this page
// for example lock/unlock
if($tiki_p_admin_wiki == 'y') {
if(isset($_REQUEST["action"])) {
  if($_REQUEST["action"]=='lock') {
    $tikilib->lock_page($page);
  } elseif ($_REQUEST["action"]=='unlock') {
    $tikilib->unlock_page($page);
  }  
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

// BreadCrumbNavigation here
// Get the number of pages from the default or userPreferences
// Remember to reverse the array when posting the array
$anonpref = $tikilib->get_preference('userbreadCrumb',4);
if($user) {
  $userbreadCrumb = $tikilib->get_user_preference($user,'userbreadCrumb',$anonpref);
} else {
  $userbreadCrumb = $anonpref;
}
if(!isset($_SESSION["breadCrumb"])) {
  $_SESSION["breadCrumb"]=Array();
}
if(!in_array($page,$_SESSION["breadCrumb"])) {
  if(count($_SESSION["breadCrumb"])>$userbreadCrumb) {
    array_shift($_SESSION["breadCrumb"]);
  } 
  array_push($_SESSION["breadCrumb"],$page);
} else {
  // If the page is in the array move to the last position
  $pos = array_search($page, $_SESSION["breadCrumb"]);
  unset($_SESSION["breadCrumb"][$pos]);
  array_push($_SESSION["breadCrumb"],$page);
}
//print_r($_SESSION["breadCrumb"]);


// Now increment page hits since we are visiting this page
if($count_admin_pvs == 'y' || $user!='admin') {
  $tikilib->add_hit($page);
}
// Get page data
$info = $tikilib->get_page_info($page);

// Verify lock status
if($info["flag"] == 'L') {
  $smarty->assign('lock',true);  
} else {
  $smarty->assign('lock',false);
}

// If not locked and last version is user version then can undo
$smarty->assign('canundo','n');	
if($info["flag"]!='L' && ( ($tiki_p_edit == 'y' && $info["user"]==$user)||($tiki_p_remove=='y') )) {
   $smarty->assign('canundo','y');	
}
if($tiki_p_admin_wiki == 'y') {
  $smarty->assign('canundo','y');		
}



$pdata = $tikilib->parse_data_raw($info["data"]);
$smarty->assign_by_ref('parsed',$pdata);
//$smarty->assign_by_ref('lastModif',date("l d of F, Y  [H:i:s]",$info["lastModif"]));
$smarty->assign_by_ref('lastModif',$info["lastModif"]);
if(empty($info["user"])) {
  $info["user"]='anonymous';  
}
$smarty->assign_by_ref('lastUser',$info["user"]);

/*
// force enable wiki comments (for development)
$feature_wiki_comments = 'y';
$smarty->assign('feature_wiki_comments','y');
*/

// Comments engine!
if($feature_wiki_comments == 'y') {
  $comments_per_page = $wiki_comments_per_page;
  $comments_default_ordering = $wiki_comments_default_ordering;
  $comments_vars=Array('page');
  $comments_prefix_var='wiki page';
  $comments_object_var='page';
  include_once("comments.php");
}

$section='wiki';
include_once('tiki-section_options.php');

// Display the Index Template
$smarty->assign('dblclickedit','y');
//$smarty->assign('mid','tiki-show_page.tpl');
//$smarty->assign('show_page_bar','y');
$smarty->display("styles/$style_base/tiki-show_page_raw.tpl");

?>