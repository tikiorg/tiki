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


if(!isset($_REQUEST["noteId"])) {
  $smarty->assign('msg',tra("No note indicated"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}


if(isset($_REQUEST["remove"])) {
    $notepadlib->remove_note($user, $_REQUEST['remove']);
}


$info = $notepadlib->get_note($user,$_REQUEST["noteId"]);

if(!isset($_REQUEST['parse_mode'])) $_REQUEST['parse_mode']='raw';
if($_REQUEST['parse_mode']=='raw') {
  $info['parsed']=nl2br(htmlentities($info['data']));
}
if($_REQUEST['parse_mode']=='wiki') {
  $info['parsed']=$tikilib->parse_data($info['data']);
}
$smarty->assign('parse_mode',$_REQUEST['parse_mode']);


$smarty->assign('noteId',$_REQUEST["noteId"]);
$smarty->assign('info',$info);

include_once('tiki-mytiki_shared.php');


$smarty->assign('mid','tiki-notepad_read.tpl');
$smarty->display('tiki.tpl');
?>
