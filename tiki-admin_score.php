<?php
require_once('tiki-setup.php');
include_once('lib/score/scorelib.php');

if (isset($_REQUEST['events']) && is_array($_REQUEST['events'])) {
    $scorelib->update_events($_REQUEST['events']);
}

$smarty->assign('events',$scorelib->get_all_events());

$smarty->assign('mid','tiki-admin_score.tpl');
$smarty->display("tiki.tpl");

?>
