<?php
// Initialization
require_once('tiki-setup.php');

if($feature_wiki != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display('error.tpl');
  die;  
}

if($tiki_p_view != 'y') {
  $smarty->assign('msg',tra("Permission denied you cannot view this section"));
  $smarty->display('error.tpl');
  die;  
}


// Get the page from the request var or default it to HomePage
if(!isset($_REQUEST["page"])) {
  $smarty->assign('msg',tra("No page indicated"));
  $smarty->display('error.tpl');
  die;
} else {
  $page = $_REQUEST["page"];
  $smarty->assign_by_ref('page',$_REQUEST["page"]); 
}


if(substr($page,0,8)=="UserPage") {
  $name = substr($page,8);
  if($user != $name) {
    if($tiki_p_admin != 'y') { 
      $smarty->assign('msg',tra("You cannot edit this page because it is a user personal page"));
      $smarty->display('error.tpl');
      die;
    }
  }
}

if($_REQUEST["page"]=='SandBox' && $feature_sandbox!='y') {
  $smarty->assign('msg',tra("The SandBox is disabled"));
  $smarty->display('error.tpl');
  die;	
}

if(!isset($_REQUEST["comment"])) {
  $_REQUEST["comment"]='';
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
if($page != 'SandBox') {
if($tiki_p_edit != 'y') {
  $smarty->assign('msg',tra("Permission denied you cannot edit this page"));
  $smarty->display('error.tpl');
  die;  
}
}


// Get page data
$info = $tikilib->get_page_info($page);

if($info["flag"]=='L') {
  $smarty->assign('msg',tra("Cannot edit page because it is locked"));
  $smarty->display('error.tpl');
  die;
}

if($page != 'SandBox') {
// Permissions
// if this page has at least one permission then we apply individual group/page permissions
// if not then generic permissions apply
if($tiki_p_admin != 'y') {
  if($userlib->object_has_one_permission($page,'wiki page')) {
    if(!$userlib->object_has_permission($user,$page,'wiki page','tiki_p_edit')) {
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
}

if($tiki_p_admin != 'y') {
  if($tiki_p_use_HTML != 'y') {
    $_REQUEST["allowhtml"] = 'off';
  }
}

$smarty->assign('allowhtml','y');

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

if(isset($_REQUEST["templateId"])&&$_REQUEST["templateId"]>0) {
  $template_data = $tikilib->get_template($_REQUEST["templateId"]);
  $_REQUEST["edit"]=$template_data["content"];
  $_REQUEST["preview"]=1;
}

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
$parsed = $tikilib->parse_data($edit_data);

/* SPELLCHECKING INITIAL ATTEMPT */
//This nice function does all the job!
if($wiki_spellcheck == 'y') {
if(isset($_REQUEST["spellcheck"])&&$_REQUEST["spellcheck"]=='on') {
  $parsed = $tikilib->spellcheckreplace($edit_data,$parsed,$language,'editwiki');
  $smarty->assign('spellcheck','y');
} else {
  $smarty->assign('spellcheck','n');
}
}

$smarty->assign_by_ref('parsed',$parsed);

$smarty->assign('preview',0);
// If we are in preview mode then preview it!
if(isset($_REQUEST["preview"])) {
  $smarty->assign('preview',1); 
} 

if(isset($_REQUEST["cancel"])) {
  header("location: tiki-index.php?page=$page");
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
  
  $cat_type='wiki page';
  $cat_objid = $_REQUEST["page"];
  $cat_desc = substr($_REQUEST["edit"],0,200);
  $cat_name = $_REQUEST["page"];
  $cat_href="tiki-index.php?page=".$cat_objid;
  include_once("categorize.php");
  
  header("location: tiki-index.php?page=$page");
  die;
}

if($feature_wiki_templates == 'y' && $tiki_p_use_content_templates == 'y') {
  $templates = $tikilib->list_templates('wiki',0,-1,'name_asc','');
}
$smarty->assign_by_ref('templates',$templates["data"]);

$cat_type='wiki page';
$cat_objid = $_REQUEST["page"];
include_once("categorize_list.php");





// Display the Index Template
$smarty->assign('mid','tiki-editpage.tpl');
$smarty->assign('show_page_bar','y');
$smarty->display('tiki.tpl');
?>