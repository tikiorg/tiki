<?php
// (c) Copyright 2002-2009 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once ('tiki-setup.php');
$access->check_script($_SERVER["SCRIPT_NAME"], basename(__FILE__));
if (isset($_REQUEST['save'])) {
	check_ticket('admin-inc-sefurl');
	$_REQUEST['feature_sefurl_paths'] = preg_split('/ *[,\/] */', $_REQUEST['feature_sefurl_paths']);
	simple_set_value('feature_sefurl_paths');
}

$needtowarn = 1;
$fp = fopen('.htaccess', "r");
if ($fp) {
	$fdata = '';
	while(!feof($fp)) {
      	 	$fdata .= fread($fp, filesize('.htaccess')); 
	}
	fclose ($fp);
	
	if (strpos($fdata,'tiki-index.php?page=$1') !== FALSE) {
		$needtowarn = 0;
	} 
}
$smarty->assign('needtowarn', $needtowarn);
ask_ticket('admin-inc-sefurl');
