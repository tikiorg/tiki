<?php
// Initialization
require_once('tiki-setup.php');
require_once('lib/tikilib.php'); # httpScheme()

if($feature_polls != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display('error.tpl');
  die;  
}

if(!isset($_REQUEST["pollId"])) {
    $smarty->assign('msg',tra("No poll indicated"));
    $smarty->display('error.tpl');
    die;
}

$poll_info = $tikilib->get_poll($_REQUEST["pollId"]);
//$polls = $tikilib->list_active_polls(0,-1,'publishDate_desc','');
$options = $tikilib->list_poll_options($_REQUEST["pollId"],0,-1,'title_desc','');

$smarty->assign_by_ref('menu_info',$poll_info);
//$smarty->assign_by_ref('polls',$polls["data"]);
$smarty->assign_by_ref('channels',$options["data"]);
$smarty->assign('ownurl',httpScheme().'://'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]);

// Display the template
$smarty->assign('mid','tiki-poll_form.tpl');
$smarty->display('tiki.tpl');
?>
