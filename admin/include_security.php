<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

TikiLib::lib('smarty')->assign('openssl_available', extension_loaded('openssl'));

if($prefs['feature_user_encryption'] == 'y') {
	$cryptlib = TikiLib::lib('crypt');
	$stats = $cryptlib->getUserCryptDataStats();
	$smarty->assign('show_user_encyption_stats', 'y');
	$smarty->assign('user_encyption_stat_mcrypt', $stats['mcrypt']);
	$smarty->assign('user_encyption_stat_openssl', $stats['openssl']);
}