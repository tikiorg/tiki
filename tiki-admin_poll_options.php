<?php
// Initialization
require_once('tiki-setup.php');

if($tiki_p_admin != 'y') {
    $smarty->assign('msg',tra("You dont have permission to use this feature"));
    $smarty->display('error.tpl');
    die;
}

if(!isset($_REQUEST["pollId"])) {
    $smarty->assign('msg',tra("No poll indicated"));
    $smarty->display('error.tpl');
    die;
}


$smarty->assign('pollId',$_REQUEST["pollId"]);
$menu_info = $tikilib->get_poll($_REQUEST["pollId"]);
$smarty->assign('menu_info',$menu_info);

if(!isset($_REQUEST["optionId"])) {
    $_REQUEST["optionId"]=0;
}
$smarty->assign('optionId',$_REQUEST["optionId"]);


if($_REQUEST["optionId"]) {
  $info = $tikilib->get_poll_option($_REQUEST["optionId"]);
} else {
  $info = Array();
  $info["title"]='';
  $info["votes"]=0;
}
$smarty->assign('title',$info["title"]);
$smarty->assign('votes',$info["votes"]);

if(isset($_REQUEST["remove"])) {
  $tikilib->remove_poll_option($_REQUEST["remove"]);
}

if(isset($_REQUEST["save"])) {
   $tikilib->replace_poll_option($_REQUEST["pollId"], $_REQUEST["optionId"], $_REQUEST["title"]);
}

if(!isset($_REQUEST["sort_mode"])) {
  $sort_mode = 'pollId_asc'; 
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
$channels = $tikilib->list_poll_options($_REQUEST["pollId"],0,-1,$sort_mode,$find);
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
$smarty->assign('ownurl','http://'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]);
$smarty->assign_by_ref('channels',$channels["data"]);


// Display the template
$smarty->assign('mid','tiki-admin_poll_options.tpl');
$smarty->display('tiki.tpl');
?>