<?php
require_once('tiki-setup.php');
include_once('lib/notepad/notepadlib.php');

if($feature_notepad != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

if(!$user) {
  $smarty->assign('msg',tra("Must be logged to use this feature"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

if($tiki_p_notepad != 'y') {
  $smarty->assign('msg',tra("Permission denied to use this feature"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}


if(!isset($_REQUEST["noteId"])) $_REQUEST["noteId"]=0;




if(isset($_REQUEST["remove"])) {
    $notepadlib->remove_note($user, $_REQUEST['remove']);
}

if($_REQUEST["noteId"]) {
  $info = $notepadlib->get_note($user,$_REQUEST["noteId"]);
} else {
  $info=Array();
  $info['name']='';
  $info['data']='';
}

if(isset($_REQUEST['save'])) {
  $notepadlib->replace_note($user,$_REQUEST["noteId"],$_REQUEST["name"],$_REQUEST["data"]);
  //:TODO: replace this putting the code in notepad-list
  header('location: tiki-notepad_list.php');
  die;
}
$smarty->assign('noteId',$_REQUEST["noteId"]);
$smarty->assign('info',$info);

include_once('tiki-mytiki_shared.php');


$smarty->assign('mid','tiki-notepad_write.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>
