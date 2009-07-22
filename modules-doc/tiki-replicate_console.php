<?php
// (c) Copyright 2002-2009 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: /cvsroot/tikiwiki/tiki/tiki-replicate_console.php,v 1.4 2005-05-18 10:58:59 mose Exp $
require_once ('tiki-setup.php');
include_once 'lib/logs/logslib.php';
if ($tiki_p_admin != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("You do not have permission to use this feature"));
	$smarty->display("error.tpl");
	die;
}
$dumps = array();
$path = "backups";
if ($tikidomain) {
	$path.= "/$tikidomain";
}
$h = opendir($path);
while ($file = readdir($h)) {
	if (strstr($file, ".sql") and substr($file, 0, 1) != '.') {
		$dumps[] = $file;
	}
}
$smarty->assign('dumps', $dumps);
ask_ticket('replicate');
$smarty->display('tiki-replicate_console.tpl');
