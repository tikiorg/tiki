<?php

require_once('tiki-setup.php');
include_once('lib/trackers/trackerlib.php');

$access->check_feature('feature_trackers');

if (!isset($_REQUEST["trackerId"])) {
  $smarty->assign('msg', tra("No tracker indicated"));
  $smarty->display("error.tpl");
  die;
}

$access->check_permission('tiki_p_admin_trackers');

if (isset($_FILES['importfile']) && is_uploaded_file($_FILES['importfile']['tmp_name'])) {
	$replace = false;
	$total = 'Incorrect file';
	$fp = @ fopen($_FILES['importfile']['tmp_name'], "rb");
	if ($fp) {
		$total = $trklib->import_csv($_REQUEST["trackerId"],$fp, true, isset($_REQUEST['dateFormat'])? $_REQUEST['dateFormat']: '', isset($_REQUEST['encoding'])? $_REQUEST['encoding']: 'UTF8', isset($_REQUEST['separator'])? $_REQUEST['separator']:',');
	}
	fclose($fp);
	if (!is_numeric($total)) {
		$smarty->assign('msg', $total);
		$smarty->display('error.tpl');
		die;
	}
}
header('Location: tiki-view_tracker.php?trackerId='.$_REQUEST["trackerId"]);
die;
