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



if(!isset($_REQUEST["noteId"])) {
  $smarty->assign('msg',tra("No note indicated"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}


if(isset($_REQUEST["remove"])) {
    $notepadlib->remove_note($user, $_REQUEST['remove']);
}


$info = $notepadlib->get_note($user,$_REQUEST["noteId"]);

if(!isset($_REQUEST['mode'])) $_REQUEST['mode']='raw';
if($_REQUEST['mode']=='raw') {
  $info['parsed']=htmlentities(nl2br($info['data']));
}
if($_REQUEST['mode']=='wiki') {
  $info['parsed']=$tikilib->parse_data($info['data']);
}
$smarty->assign('mode',$_REQUEST['mode']);


$smarty->assign('noteId',$_REQUEST["noteId"]);
$smarty->assign('info',$info);

include_once('tiki-mytiki_shared.php');


$smarty->assign('mid','tiki-notepad_read.tpl');
$smarty->display('tiki.tpl');
?>
