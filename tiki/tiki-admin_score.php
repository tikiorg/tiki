<?php
require_once('tiki-setup.php');
include_once('lib/score/scorelib.php');

if ($tiki_p_admin != 'y') {
        $smarty->assign('msg', tra("You dont have permission to use this feature"));
                                                                                
        $smarty->display("error.tpl");
        die;
}

if ($feature_score != 'y') {
	$smarty->assign('msg', tra("Feature disabled"));
	$smarty->display("error.tpl");
	die;
}

if (isset($_REQUEST['events']) && is_array($_REQUEST['events'])) {
    check_ticket('admin-score');
    $scorelib->update_events($_REQUEST['events']);
}

ask_ticket('admin-score');
$smarty->assign('events',$scorelib->get_all_events());

$smarty->assign('mid','tiki-admin_score.tpl');
$smarty->display("tiki.tpl");

?>
