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
    $notepadlib->remove_note($user, $_REQUEST['noteId']);
    header('location: tiki-notepad_list.php');
    die;
}


$info = $notepadlib->get_note($user,$_REQUEST["noteId"]);



if($tiki_p_edit == 'y') {
	if(isset($_REQUEST['wikify'])) {
	  if(empty($_REQUEST['wiki_name'])) {
		$smarty->assign('msg',tra("No name indicated for wiki page"));
	  	$smarty->display("styles/$style_base/error.tpl");
		die;  
	  }
	  if($tikilib->page_exists($_REQUEST['wiki_name']) && !isset($_REQUEST['over'])) {
		$smarty->assign('msg',tra("Page already exists"));
	  	$smarty->display("styles/$style_base/error.tpl");
		die;  
	  }  
  	  if($tikilib->page_exists($_REQUEST['wiki_name'])) {
	  	$tikilib->update_page($_REQUEST['wiki_name'],$info['data'],tra('created from notepad'), $user, '127.0.1.1',$info['name']);
	  } else {
		$tikilib->create_page($_REQUEST['wiki_name'], 0, $info['data'], date("U"), tra('created from notepad'), $user, $ip='0.0.0.0',$info['name']);
	  }
	}
}

if($tikilib->page_exists($info['name'])) {
  $smarty->assign("wiki_exists","y");
} else {
  $smarty->assign("wiki_exists","n");
}


if(!isset($_REQUEST['parse_mode'])) $_REQUEST['parse_mode']=$info['parse_mode'];
if($_REQUEST['parse_mode']=='raw') {
  $info['parsed']=nl2br(htmlentities($info['data']));
}
if($_REQUEST['parse_mode']=='wiki') {
  $info['parsed']=$tikilib->parse_data($info['data']);
}
$notepadlib->set_note_parsing($user,$_REQUEST['noteId'],$_REQUEST['parse_mode']);
$smarty->assign('parse_mode',$_REQUEST['parse_mode']);


$smarty->assign('noteId',$_REQUEST["noteId"]);
$smarty->assign('info',$info);

include_once('tiki-mytiki_shared.php');


$smarty->assign('mid','tiki-notepad_read.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>
