<?php
// Initialization
require_once('tiki-setup.php');
include_once('lib/htmlpages/htmlpageslib.php');  

if($feature_html_pages != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

if($tiki_p_edit_html_pages != 'y') {
    $smarty->assign('msg',tra("You dont have permission to use this feature"));
    $smarty->display("styles/$style_base/error.tpl");
    die;
}

if(!isset($_REQUEST["pageName"])) {
    $smarty->assign('msg',tra("No page indicated"));
    $smarty->display("styles/$style_base/error.tpl");
    die;
}
$smarty->assign('pageName',$_REQUEST["pageName"]);

if(!isset($_REQUEST["zone"])) {
    $_REQUEST["zone"]='';
}
$smarty->assign('zone',$_REQUEST["zone"]);

$page_info = $htmlpageslib->get_html_page($_REQUEST["pageName"]);

if($_REQUEST["zone"]) {
  $info = $htmlpageslib->get_html_page_content($_REQUEST["pageName"],$_REQUEST["zone"]);
} else {
  $info = Array();
  $info["content"]='';
  $info["type"]='';
}

$smarty->assign('content',$info["content"]);
$smarty->assign('type',$info["type"]);

/* NO REMOVAL
if(isset($_REQUEST["remove"])) {
  $htmlpageslib->remove_html_page_content($_REQUEST["pageName"],$_REQUEST["remove"]);
}
*/

if(isset($_REQUEST["editmany"])) {
  $zones = $htmlpageslib->list_html_page_content($_REQUEST["pageName"],0,-1,'zone_asc','');
  for($i=0;$i<count($zones["data"]);$i++) {
    if(isset($_REQUEST[$zones["data"][$i]["zone"]])) {
      $htmlpageslib->replace_html_page_content($_REQUEST["pageName"], $zones["data"][$i]["zone"],$_REQUEST[$zones["data"][$i]["zone"]]);
    }
  }
  
}

if(isset($_REQUEST["save"])) {
  
  $htmlpageslib->replace_html_page_content($_REQUEST["pageName"], $_REQUEST["zone"],$_REQUEST["content"]);
  $smarty->assign('zone','');
  $smarty->assign('content','');
}

if(!isset($_REQUEST["sort_mode"])) {
  $sort_mode = 'zone_asc'; 
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
$channels = $htmlpageslib->list_html_page_content($_REQUEST["pageName"],$offset,$maxRecords,$sort_mode,$find);

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
$smarty->assign('mid','tiki-admin_html_page_content.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>