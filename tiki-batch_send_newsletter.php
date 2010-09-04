<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$
if (empty($argv)) { // can only be used in a cron or line command
	return;
}

include('tiki-setup.php');
global $nllib; include_once('lib/newsletters/nllib.php');

function display_usage() {
	echo 'Usage: php tiki-batch_send_newsletter.php <editionId>';
	die;
}
error_reporting (E_ALL);
	
if (empty($argv[1])) {
	display_usage();
}
$editionId = $argv[1];

if (!($edition_info = $nllib->get_edition($editionId))) {
	echo "Incorrect editionId; $editionId";
	die;
}
if (!($nl_info = $nllib->get_newsletter($edition_info['nlId']))) {
	echo 'Incorrect nlId: '.$edition_info['nlId'];
}
$edition_info['editionId'] = 0;
$sent = $errors = array();
$logFileName = '';
$nllib->send($nl_info, $edition_info, false, $sent, $errors, $logFileName);
if (!empty($errors)) {
	echo "Errors\n";
	foreach ($errors as $error) {
		echo $error."\n";
	}
	die;
}
echo "Sent to\n";
foreach ($sent as $s) {
	echo $s."\n";
}
echo "Log: $logFileName\n";
