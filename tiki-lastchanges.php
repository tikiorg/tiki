<?php
// Initialization
require_once('tiki-setup.php');

if($feature_lastChanges != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display('error.tpl');
  die;  
}

// This script can receive the thresold
// for the information as the number of
// days to get in the log 1,3,4,etc
// it will default to 1 recovering information for today
if(!isset($_REQUEST["days"])) {
  $days = 1; 
} else {
  $days = $_REQUEST["days"]; 
}
if(!isset($_REQUEST["sort_mode"])) {
  $sort_mode = 'lastModif_desc'; 
} else {
  $sort_mode = $_REQUEST["sort_mode"];
} 

$smarty->assign_by_ref('days',$days);
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


// Get a list of last changes to the Wiki database
$more=0;
$lastchanges = $tikilib->get_last_changes($days,$offset,$maxRecords,$sort_mode);
// If there're more records then assign next_offset
$cant_pages = ceil($lastchanges["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages',$cant_pages);
$smarty->assign('actual_page',1+($offset/$maxRecords));

if($lastchanges["cant"] > ($offset + $maxRecords)) {
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

$smarty->assign_by_ref('lastchanges',$lastchanges["data"]);


// Display the template
$smarty->assign('mid','tiki-lastchanges.tpl');
$smarty->display('tiki.tpl');
?>
