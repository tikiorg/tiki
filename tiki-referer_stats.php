<?php
// Initialization
require_once('tiki-setup.php');


if($feature_referer_stats != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display('error.tpl');
  die;  
}


if($tiki_p_view_referer_stats != 'y') {
    $smarty->assign('msg',tra("You dont have permission to use this feature"));
    $smarty->display('error.tpl');
    die;
}

if(isset($_REQUEST["clear"])) {
  $tikilib->clear_referer_stats();	
}

/*
if($tiki_p_take_quiz != 'y') {
    $smarty->assign('msg',tra("You dont have permission to use this feature"));
    $smarty->display('error.tpl');
    die;
}
*/


if(!isset($_REQUEST["sort_mode"])) {
  $sort_mode = 'hits_desc'; 
} else {
  $sort_mode = $_REQUEST["sort_mode"];
} 

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
$smarty->assign('find',$find);

$smarty->assign_by_ref('sort_mode',$sort_mode);
$channels = $tikilib->list_referer_stats($offset,$maxRecords,$sort_mode,$find);



$cant_pages = ceil($channels["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages',$cant_pages);
$smarty->assign('actual_page',1+($offset/$maxRecords));
if($channels["cant"] > ($offset+$maxRecords)) {
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

$smarty->assign_by_ref('channels',$channels["data"]);


// Display the template
$smarty->assign('mid','tiki-referer_stats.tpl');
$smarty->display('tiki.tpl');
?>