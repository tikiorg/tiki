<?php
/**
 * This script may only be included
 *
 * provides inclusion calls to internal Tiki components
 *
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 *
 * @package Tikiwiki\admin
 * @copyright (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
 */
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

/** test for $_REQUEST["cmsprefs"], check 'admin-inc-cms' ticket */
if (isset($_REQUEST["cmsprefs"])) {
	check_ticket('admin-inc-cms');
}
/** test for $_REQUEST["articlecomprefs"], check 'admin-inc-cms' ticket */
if (isset($_REQUEST["articlecomprefs"])) {
	check_ticket('admin-inc-cms');
}
/** test for $_REQUEST['import'], check 'admin-inc-cms' ticket then list articles */
if (isset($_REQUEST['import'])) {
	global $artlib;
	include_once ('lib/articles/artlib.php');
	check_ticket('admin-inc-cms');
	$fname = $_FILES['csvlist']['tmp_name'];
	$msgs = array();
	$artlib->import_csv($fname, $msgs);
	if (!empty($msgs)) {
		print_r($msgs);
		$smarty->assign_by_ref('msgs', $msgs);
	}
}
ask_ticket('admin-inc-cms');
