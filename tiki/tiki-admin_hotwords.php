<?php
// Initialization
require_once('tiki-setup.php');

if($feature_hotwords != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}


if($user != 'admin') {
  if($tiki_p_admin != 'y') {
    $smarty->assign('msg',tra("You dont have permission to use this feature"));
    $smarty->display("styles/$style_base/error.tpl");
    die;
  }
}

// Process the form to add a user here
if(isset($_REQUEST["add"])) {
  $tikilib->add_hotword($_REQUEST["word"],$_REQUEST["url"]);
}

if(isset($_REQUEST["remove"])&&!empty($_REQUEST["remove"])) {
  $tikilib->remove_hotword($_REQUEST["remove"]);
}



if(!isset($_REQUEST["sort_mode"])) {
  $sort_mode = 'word_desc'; 
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
$smarty->assign('find',$find);

$words = $tikilib->list_hotwords($offset,$maxRecords,$sort_mode,$find);
$cant_pages = ceil($words["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages',$cant_pages);
$smarty->assign('actual_page',1+($offset/$maxRecords));
if($words["cant"] > ($offset+$maxRecords)) {
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



// Get users (list of users)
$smarty->assign_by_ref('words',$words["data"]);

// Display the template
$smarty->assign('mid','tiki-admin_hotwords.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>