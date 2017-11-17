<?php
// (c) Copyright 2002-2017 by authors of the Tiki Wiki/CMS/Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

@ignore_user_abort(true); // Allow execution to continue even if the request gets canceled.

try {
	require_once 'tiki-setup.php';
} catch (Exception $e) {
	return;
}

// Check if Feature Scheduler is enabled
$feature_enabled = $tikilib->get_preference('feature_scheduler');

if ($feature_enabled != 'y') {
	return;
}

// Check if Web Cron is enabled
$webcron_enabled = $tikilib->get_preference('webcron_enabled');

if ($webcron_enabled != 'y') {
	return;
}

// Validate if the Token to run the Web Cron matches the stored token
$cron_token = $tikilib->get_preference('webcron_token');

if (! isset($_REQUEST['token']) || $_REQUEST['token'] !== $cron_token) {
	return;
}

$asUser = 'admin';

if (TikiLib::lib('user')->user_exists($asUser)) {
	$permissionContext = new Perms_Context($asUser);
}

$tikilib = TikiLib::lib('tiki');

$last_cron_run = $tikilib->get_preference('webcron_last_run');
$cron_interval = $tikilib->get_preference('webcron_run_interval');

if (empty($cron_interval)) {
	$cron_interval = 60;
}

$start_time = time();

if ($last_cron_run + $cron_interval >= $start_time) {
	//too soon;
	return;
}

$last_cron_run = $tikilib->set_preference('webcron_last_run', $start_time);

$logger = new Tiki_Log('Webcron', \Psr\Log\LogLevel::ERROR);
$manager = new Scheduler_Manager($logger);
$manager->run();
