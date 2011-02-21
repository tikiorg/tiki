<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
$access->check_script($_SERVER["SCRIPT_NAME"], basename(__FILE__));
if (isset($_REQUEST['save'])) {
	check_ticket('admin-inc-sefurl');
	$_REQUEST['feature_sefurl_paths'] = preg_split('/ *[,\/] */', $_REQUEST['feature_sefurl_paths']);
	simple_set_value('feature_sefurl_paths');
}

// Check if .htaccess is present and current
$htaccess = "missing";
$fp = fopen('.htaccess', "r");
if ($fp) {
	$htCurrent = fopen('_htaccess', "r");
	$installedFirstLine = fgets($fp); 
	if ($installedFirstLine == fgets($htCurrent)) { // Do not warn if the first line of each file is identical. First lines contain _htaccess revision
		$htaccess = 'current';
	} elseif(strstr($installedFirstLine, 'This line is used to check that this htaccess file is up to date.')) {
		$htaccess = 'outdated';
	}
	fclose($htCurrent);
	fclose($fp);
}
$smarty->assign('htaccess', $htaccess);

ask_ticket('admin-inc-sefurl');
