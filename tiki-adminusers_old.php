<?php
// Initialization
require_once('tiki-setup.php');

// Only an admin can use this script
if(  (!isset($_SESSION["user"])) || ($_SESSION["user"]!='admin') ) {
  header("location: tiki-index.php");
  die; 
}

if(isset($_REQUEST["newuser"])) {
  // Check if the user already exists
  if($_REQUEST["pass"] != $_REQUEST["pass2"]) {
    $smarty->assign('msg',tra("The passwords dont match"));
    $smarty->display("styles/$style_base/error.tpl");
    die;
  } else {
    if($tikilib->user_exists($_REQUEST["name"])) {
      $smarty->assign('msg',tra("User already exists"));
      $smarty->display("styles/$style_base/error.tpl");
      die;
    } else {
      $tikilib->add_user($_REQUEST["name"],$_REQUEST["pass"],$_REQUEST["email"]);
    }
  }
}

if(isset($_REQUEST["action"])) {
  if($_REQUEST["action"]=='delete') {
    $tikilib->remove_user($_REQUEST["user"]); 
  } 
}

if(!isset($_REQUEST["sort_mode"])) {
  $sort_mode = 'user_desc'; 
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

$users = $tikilib->get_users($offset,$maxRecords,$sort_mode);

$cant_pages = ceil($users["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages',$cant_pages);
$smarty->assign('actual_page',1+($offset/$maxRecords));
if($users["cant"] > ($offset+$maxRecords)) {
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
$smarty->assign_by_ref('users',$users["data"]);

// Display the template
$smarty->assign('mid','tiki-adminusers.tpl');
$smarty->display("styles/$style_base/tiki.tpl");

?>