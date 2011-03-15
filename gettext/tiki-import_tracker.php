<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

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
		$total = $trklib->import_csv($_REQUEST["trackerId"],$fp, 
				isset($_REQUEST['add_items']) ? false : true,
				isset($_REQUEST['dateFormat'])? $_REQUEST['dateFormat']: '',
				isset($_REQUEST['encoding'])? $_REQUEST['encoding']: 'UTF8',
				isset($_REQUEST['separator'])? $_REQUEST['separator']:',');
	}
	fclose($fp);
	if (!is_numeric($total)) {
		$smarty->assign('msg', $total);
		$smarty->display('error.tpl');
		die;
	}
}
if (isset($_SERVER['HTTP_REFERER']) && strpos('tiki-admin_trackers.php') !== false) {
	header('Location: tiki-admin_trackers.php?trackerId='.$_REQUEST["trackerId"]);
} else {
	header('Location: tiki-view_tracker.php?trackerId='.$_REQUEST["trackerId"]);
}
die;
