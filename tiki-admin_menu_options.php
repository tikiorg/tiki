<?php
// Initialization
require_once('tiki-setup.php');

if($tiki_p_admin != 'y') {
    $smarty->assign('msg',tra("You dont have permission to use this feature"));
    $smarty->display('error.tpl');
    die;
}

if(!isset($_REQUEST["menuId"])) {
    $smarty->assign('msg',tra("No menu indicated"));
    $smarty->display('error.tpl');
    die;
}

$maxPos = $tikilib->get_max_option($_REQUEST["menuId"]);

$smarty->assign('menuId',$_REQUEST["menuId"]);
$menu_info = $tikilib->get_menu($_REQUEST["menuId"]);
$smarty->assign('menu_info',$menu_info);

if(!isset($_REQUEST["optionId"])) {
    $_REQUEST["optionId"]=0;
}
$smarty->assign('optionId',$_REQUEST["optionId"]);


if($_REQUEST["optionId"]) {
  $info = $tikilib->get_menu_option($_REQUEST["optionId"]);
} else {
  $info = Array();
  $info["name"]='';
  $info["url"]='';
  $info["type"]='o';
  $info["position"]=$maxPos+1;
}
$smarty->assign('name',$info["name"]);
$smarty->assign('url',$info["url"]);
$smarty->assign('type',$info["type"]);
$smarty->assign('position',$info["position"]);

if(isset($_REQUEST["remove"])) {
  $tikilib->remove_menu_option($_REQUEST["remove"]);
}

if(isset($_REQUEST["save"])) {
   $tikilib->replace_menu_option($_REQUEST["menuId"], $_REQUEST["optionId"], $_REQUEST["name"], $_REQUEST["url"], $_REQUEST["type"],$_REQUEST["position"]);
}

if(!isset($_REQUEST["sort_mode"])) {
  $sort_mode = 'position_asc'; 
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

$smarty->assign_by_ref('sort_mode',$sort_mode);
$channels = $tikilib->list_menu_options($_REQUEST["menuId"],0,-1,$sort_mode,$find);
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
$smarty->assign('mid','tiki-admin_menu_options.tpl');
$smarty->display('tiki.tpl');
?>