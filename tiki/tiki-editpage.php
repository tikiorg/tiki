<?php
// Initialization
require_once('tiki-setup.php');

// Get the page from the request var or default it to HomePage
if(!isset($_REQUEST["page"])) {
  $smarty->assign('msg',tra("No page indicated"));
  $smarty->display('error.tpl');
  die;
} else {
  $page = $_REQUEST["page"];
  $smarty->assign_by_ref('page',$_REQUEST["page"]); 
}

/*
if(!page_exists($page)) {
  $smarty->assign('msg',tra("Page cannot be found"));
  $smarty->display('error.tpl');
  die;
}
*/

include_once("tiki-pagesetup.php");

// Now check permissions to access this page
if($tiki_p_edit != 'y') {
  $smarty->assign('msg',tra("Permission denied you cannot edit this page"));
  $smarty->display('error.tpl');
  die;  
}


// Get page data
$info = $tikilib->get_page_info($page);

if($info["flag"]=='L') {
  $smarty->assign('msg',tra("Cannot edit page because it is locked"));
  $smarty->display('error.tpl');
  die;
}

// Permissions
// if this page has at least one permission then we apply individual group/page permissions
// if not then generic permissions apply
if($tiki_p_admin != 'y') {
  if($userlib->object_has_one_permission($page,TIKI_PAGE_RESOURCE)) {
    if(!$userlib->object_has_permission($user,$page,TIKI_PAGE_RESOURCE,TIKI_P_EDIT)) {
      $smarty->assign('msg',tra("Permission denied you cannot edit this page"));
      $smarty->display('error.tpl');
      die;  
    }
  } else {
    if($tiki_p_edit != 'y')  {
      $smarty->assign('msg',tra("Permission denied you cannot edit this page"));
      $smarty->display('error.tpl');
      die;  
    }
  }
}

if($tiki_p_admin != 'y') {
  if($tiki_p_use_HTML != 'y') {
    $_REQUEST["allowhtml"] = 'off';
  }
}

$smarty->assign('allowhtml','n');

/*
if(!$user && $anonCanEdit<>'y') {
  
  header("location: tiki-index.php");
  die;
  //$smarty->assign('msg',tra("Anonymous users cannot edit pages"));
  //$smarty->display('error.tpl');
  //die;
}
*/

$smarty->assign_by_ref('data',$info);

if(isset($_REQUEST["edit"])) {
  
  if(isset($_REQUEST["allowhtml"]) && $_REQUEST["allowhtml"]=="on") {
    $edit_data = $_REQUEST["edit"];  
  } else {
    $edit_data = strip_tags($_REQUEST["edit"]);
  }
  
  
  
} else {
  if(isset($info["data"])) {
    $edit_data = $info["data"];
  } else {
    $edit_data = ''; 
  }
}
$smarty->assign('commentdata','');
if(isset($_REQUEST["comment"])) {
  $smarty->assign_by_ref('commentdata',$_REQUEST["comment"]); 
}
if(isset($_REQUEST["allowhtml"])) {
  if($_REQUEST["allowhtml"] == "on") {
    $smarty->assign('allowhtml','y');
  }
}
$smarty->assign_by_ref('pagedata',$edit_data);
$smarty->assign('parsed',$tikilib->parse_data($edit_data));

$smarty->assign('preview',0);
// If we are in preview mode then preview it!
if(isset($_REQUEST["preview"])) {
  $smarty->assign('preview',1); 
} 

// Pro
if(isset($_REQUEST["save"])) {
  
  if(isset($_REQUEST["allowhtml"]) && $_REQUEST["allowhtml"]=="on") {
    $edit = $_REQUEST["edit"];  
  } else {
    $edit = strip_tags($_REQUEST["edit"]);
  }

  // Parse $edit and eliminate image references to external URIs (make them internal)
  $edit = $tikilib->capture_images($edit);
  
  // If page exists
  if(!$tikilib->page_exists($_REQUEST["page"])) {
    // Extract links and update the page
    $links = $tikilib->get_links($_REQUEST["edit"]);
    $tikilib->cache_links($links);
    $t = date("U");
    $tikilib->create_page($_REQUEST["page"], 0, $edit, $t, $_REQUEST["comment"],$user,$_SERVER["REMOTE_ADDR"]);  
  } else {
    $links = $tikilib->get_links($edit);
    $tikilib->cache_links($links);
    $tikilib->update_page($_REQUEST["page"],$edit,$_REQUEST["comment"],$user,$_SERVER["REMOTE_ADDR"]);
  }
  header("location: tiki-index.php?page=$page");
}


// Display the Index Template
$smarty->assign('mid','tiki-editpage.tpl');
$smarty->assign('show_page_bar','y');
$smarty->display('tiki.tpl');
?>