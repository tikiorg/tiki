<?php
// Initialization
require_once('tiki-setup.php');

if($tiki_p_admin != 'y') {
    $smarty->assign('msg',tra("You dont have permission to use this feature"));
    $smarty->display('error.tpl');
    die;
}

if(isset($_REQUEST["add"])) {
  $tikilib->add_mail_event($_REQUEST["event"],'*',$_REQUEST["email"]);
}

if(isset($_REQUEST["removeevent"])) {
  $tikilib->remove_mail_event($_REQUEST["removeevent"],$_REQUEST["object"],$_REQUEST["email"]);
}

if(!isset($_REQUEST["sort_mode"])) {
  $sort_mode = 'event_asc'; 
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
$channels = $tikilib->list_mail_events($offset,$maxRecords,$sort_mode,$find);

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

$admin_mail=$tikilib->get_user_email('admin');
$smarty->assign('admin_mail',$admin_mail);




// Display the template
$smarty->assign('mid','tiki-admin_notifications.tpl');
$smarty->display('tiki.tpl');
?>