<?php
// Initialization
require_once('tiki-setup.php');

/*
if($feature_articles != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display('error.tpl');
  die;  
}
*/

if(!isset($_REQUEST["contentId"])) {
  $smarty->assign('msg',tra("No content id indicated"));
  $smarty->display('error.tpl');
  die;  
}
$smarty->assign('contentId',$_REQUEST["contentId");
$info = $tikilib->get_content($_REQUEST["contentId");
$smarty->assign('description',$info["description"]);



if(isset($_REQUEST["remove"])) {
  $tikilib->remove_programmed_content($_REQUEST["remove"],$_REQUEST["date"]);  
}

$now = date("U");
$smarty->assign('data','');
$smarty->assign('publishDate',$now);
$smarty->assign('contentId',0);

if(isset($_REQUEST["save"])) {

}

if(isset($_REQUEST["edit"])) {

}

// This script can receive the thresold
// for the information as the number of
// days to get in the log 1,3,4,etc
// it will default to 1 recovering information for today
if(!isset($_REQUEST["sort_mode"])) {
  $sort_mode = 'contentId_desc'; 
} else {
  $sort_mode = $_REQUEST["sort_mode"];
} 


$smarty->assign_by_ref('sort_mode',$sort_mode);

// If offset is set use it if not then use offset =0
// use the maxRecords php variable to set the limit
// if sortMode is not set then use lastModif_desc
if(!isset($_REQUEST["offset"])) {
  $offset = 0;
} else {
  $offset = $_REQUEST["offset"]; 
}
$smarty->assign_by_ref('offset',$offset);


if(isset($_REQUEST["find"])) {
  $find = $_REQUEST["find"];  
} else {
  $find = ''; 
}

// Get a list of last changes to the Wiki database
$listpages = $tikilib->list_programmed_content($contentId,$offset,$maxRecords,$sort_mode,$find);
// If there're more records then assign next_offset
$cant_pages = ceil($listpages["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages',$cant_pages);
$smarty->assign('actual_page',1+($offset/$maxRecords));

if($listpages["cant"] > ($offset + $maxRecords)) {
  $smarty->assign('next_offset',$offset + $maxRecords);
} else {
  $smarty->assign('next_offset',-1); 
}
// If offset is > 0 then prev_offset
if($offset>0) {
  $smarty->assign('prev_offset',$offset - $maxRecords);  
} else {
  $smarty->assign('prev_offset',-1); 
}

$smarty->assign_by_ref('listpages',$listpages["data"]);
//print_r($listpages["data"]);

// Display the template
$smarty->assign('mid','tiki-edit_prigrammed_content.tpl');
$smarty->display('tiki.tpl');
?>
