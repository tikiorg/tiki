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


$info = $notepadlib->get_note($user,$_REQUEST["noteId"]);

header("Content-type: text/plain");
//header( "Content-Disposition: attachment; filename=$file" );
header( "Content-Disposition: inline; filename=tiki-calendar" );
echo $info['data'];
?>