<?php
// Initialization
require_once('tiki-setup.php');

if($user != 'admin') {
  if($tiki_p_admin != 'y') {
    $smarty->assign('msg',tra("You dont have permission to use this feature"));
    $smarty->display('error.tpl');
    die;
  }
}

// Process the form to add a user here
if(isset($_REQUEST["newuser"])) {
  // Check if the user already exists
  if($_REQUEST["pass"] != $_REQUEST["pass2"]) {
    $smarty->assign('msg',tra("The passwords dont match"));
    $smarty->display('error.tpl');
    die;
  } else {
    if($userlib->user_exists($_REQUEST["name"])) {
      $smarty->assign('msg',tra("User already exists"));
      $smarty->display('error.tpl');
      die;
    } else {
      $userlib->add_user($_REQUEST["name"],$_REQUEST["pass"],$_REQUEST["email"]);
    }
  }
}

// Process actions here
// Remove user or remove user from group
if(isset($_REQUEST["action"])) {
  if($_REQUEST["action"]=='delete') {
    $userlib->remove_user($_REQUEST["user"]); 
  } 
  if($_REQUEST["action"]=='removegroup') {
    $userlib->remove_user_from_group($_REQUEST["ruser"],$_REQUEST["group"]); 
  }
}

if(!isset($_REQUEST["sort_mode"])) {
  $sort_mode = 'login_desc'; 
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

$users = $userlib->get_users($offset,$maxRecords,$sort_mode,$find);
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
$smarty->display('tiki.tpl');
?>