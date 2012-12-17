<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
$access->check_feature('ids_enabled');
$access->check_permission('tiki_p_admin');

$fp = fopen('temp/phpids_log.txt', 'r');
$headers = array('ip', 'date', 'impact', 'tags', 'attackedParameters', 'request', 'serverIp');
while (($line = fgets($fp)) !== false) {
	$line = trim($line);
	$intrusion = explode(',', $line);
	$intrusions[] = array_combine($headers, $intrusion);
}
fclose($fp);

foreach ($intrusions as &$value) {
	$value['ip'] = trim($value['ip'], '"');
	$value['tags'] = trim($value['tags'], '"');
	$value['attackedParameters'] = trim($value['attackedParameters'], '"');
	$value['attackedParameters'] = rawurldecode(urldecode($value['attackedParameters']));
	$value['request'] = trim($value['request'], '"');
	$value['request'] = rawurldecode(urldecode($value['request']));
	$value['serverIp'] = trim($value['serverIp'], '"\n');
}

$intrusions = array_reverse($intrusions);

$smarty->assign('intrusions', $intrusions);
$smarty->assign('mid', 'tiki-ids.tpl');
$smarty->display('tiki.tpl');
