<?php
// Initialization
require_once('tiki-setup.php');

// PERMISSIONS: NEEDS p_admin
if($user != 'admin') {
  if($tiki_p_admin != 'y') {
    $smarty->assign('msg',tra("You dont have permission to use this feature"));
    $smarty->display("styles/$style_base/error.tpl");
    die;
  }
}

// Process the form to add a group
if(isset($_REQUEST["newgroup"])) {
  // Check if the user already exists
  if($userlib->group_exists($_REQUEST["name"])) {
    $smarty->assign('msg',tra("Group already exists"));
    $smarty->display("styles/$style_base/error.tpl");
    die;
  } else {
    $userlib->add_group(addslashes($_REQUEST["name"]),addslashes($_REQUEST["desc"]));
    if (isset($_REQUEST["include_groups"])) {
      foreach($_REQUEST["include_groups"] as $include) {
        $userlib->group_inclusion(addslashes($_REQUEST["name"]),$include);
      }
    }
  }
}

// Process a form to remove a group
if(isset($_REQUEST["action"])) {
  if($_REQUEST["action"]=='delete') {
    $userlib->remove_group($_REQUEST["group"]); 
  } 
  if($_REQUEST["action"]=='remove') {
    $userlib->remove_permission_from_group($_REQUEST["permission"],$_REQUEST["group"]); 
  }
}



// Sort options and pagination for the group list
if(!isset($_REQUEST["sort_mode"])) {
  $sort_mode = 'groupName_desc'; 
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

$users = $userlib->get_groups($offset,$maxRecords,$sort_mode,$find);
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
// Assign the list of groups
$smarty->assign_by_ref('users',$users["data"]);
// Display the template for group administration
$smarty->assign('mid','tiki-admingroups.tpl');
$smarty->display("styles/$style_base/tiki.tpl");


?>
