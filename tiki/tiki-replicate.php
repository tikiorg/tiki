<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-replicate.php,v 1.1 2004-02-29 01:22:52 mose Exp $

require_once ('tiki-setup.php');
include_once 'lib/logs/logslib.php';

if ($tiki_p_admin != 'y') {
	if (!$user) {
		$smarty->assign('msg',$smarty->fetch('modules/mod-login_box.tpl'));
		$smarty->assign('errortitle',tra("Please login"));
	} else {
		$smarty->assign('msg', tra("You dont have permission to use this feature"));
	}
	$smarty->display("error.tpl");
	die;
}

$master = $tikilib->get_preference('replimaster','');

if (isset($_POST['action']) and $_POST['action']  == 'save') {
	if ($master != $newmaster) {
		check_ticket('replicate');
		if (isset($_POST['master']) and $_POST['master']) {
			$newmaster = $_POST['master'];
		} else {
			$newmaster = '';
		}
		$tikilib->set_preference('replimaster',$newmaster);
		$mess = sprintf(tra("changed '%s' from '%s' to '%s'"),'replimaster',$master,$newmaster);
		$logslib->add_log('replicate',$mess,$user);
		$tikifeedback[] = array('num'=>0,'mes'=>$mess);
	}
	$master = $newmaster;
}

$dumps = array();
$h = opendir("/");
while ($file = readdir($h)) {
	if (strstr($file, ".sql") and substr($file,0,1) != '.') {
		$dumps[] = $file;
	}
}
$smarty->assign('dumps', $dumps);
$smarty->assign_by_ref('master', $master);

ask_ticket('replicate');

$smarty->assign('mid', 'tiki-replicate.tpl');
$smarty->display('tiki.tpl');
?>
