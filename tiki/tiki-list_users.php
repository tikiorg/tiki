<?php
// Initialization
require_once('tiki-setup.php');

if($feature_friends != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

if($tiki_p_list_users != 'y') {
    $smarty->assign('msg',tra("You dont have permission to use this feature"));
    $smarty->display("styles/$style_base/error.tpl");
    die;
}

if(isset($_REQUEST["find"])) {
  $find = $_REQUEST["find"];  
} else {
  $find = ''; 
}
$smarty->assign('find',$find);

if(!isset($_REQUEST["sort_mode"])) {
  $sort_mode = $user_list_order;
} else {
  $sort_mode = $_REQUEST["sort_mode"];
} 


$smarty->assign_by_ref('sort_mode',$sort_mode);

// If offset is set use it if not then use offset =0
// use the maxRecords php variable to set the limit
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

$listusers = $tikilib->list_users($offset,$maxRecords,$sort_mode,$find);

// If there're more records then assign next_offset
$cant_pages = ceil($listusers["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages',$cant_pages);
$smarty->assign('actual_page',1+($offset/$maxRecords));

if($listusers["cant"] > ($offset + $maxRecords)) {
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

$smarty->assign_by_ref('listusers',$listusers["data"]);

$section='users';
include_once('tiki-section_options.php');

// Display the template
$smarty->assign('mid','tiki-list_users.tpl');
$smarty->display("tiki.tpl");
?>
