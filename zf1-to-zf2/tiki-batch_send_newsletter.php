<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

include('tiki-setup.php');

$access->check_feature('feature_newsletters');

if (php_sapi_name() != 'cli') {
	$access->check_permission('tiki_p_send_newsletters');
}

global $nllib; include_once('lib/newsletters/nllib.php');

function display_usage() 
{
	$helpMsg = "\nUsage: php tiki-batch_send_newsletter.php editionId=X\n"
		. "Usage: http://path_to_tiki/tiki-batch_send_newsletter.php?editionId=X\n";
		
	if (php_sapi_name() == 'cli') {
		echo $helpMsg;
	} else {
		echo nl2br($helpMsg);
	}
	die;
}
error_reporting(E_ALL);

$request = new Tiki_Request();

$editionId = $request->getProperty('editionId'); 

if (empty($editionId)) {
	display_usage();
}

if (!($edition_info = $nllib->get_edition($editionId))) {
	echo "Incorrect editionId: $editionId";
	die;
}
if (!($nl_info = $nllib->get_newsletter($edition_info['nlId']))) {
	echo 'Incorrect nlId: '.$edition_info['nlId'];
}
$edition_info['editionId'] = 0;
$sent = $errors = array();
$logFileName = '';
$edition_info['begin'] = 'y';
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
