<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (basename($_SERVER['SCRIPT_NAME']) == basename(__FILE__)) {
	header("location: index.php");
	exit;
}

if (!class_exists('Expose\Manager')){ // make sure expose is installed
	return;
}

$data = array(
	'GET' => $_GET,
	'POST' => $_POST,
);

$filters = new \Expose\FilterCollection();
$filters->load();

if (!empty($prefs['ids_log_to_file'])) {
	$filepath = $prefs['ids_log_to_file'];
} else {
	$filepath = 'ids.log';
}

if (!empty($prefs['ids_custom_rules_file']) && file_exists($prefs['ids_custom_rules_file'])) {
	$filters->load($prefs['ids_custom_rules_file']);
}

$logger = new \IDS_log($filepath);

$manager = new \Expose\Manager($filters, $logger);
$manager->run($data);

if ($manager->getImpact() > 0) {
	$report = $manager->export();
	$logger->info("Impact: " . $manager->getImpact() . ", Report: " . $report);

	$isRequestToSecurityAdmin = false;
	if (isset($_SERVER['REQUEST_URI'])){
		$parts = parse_url($_SERVER['REQUEST_URI']);
		$requestFile = (isset($parts['path'])) ? basename($parts['path']) : '';
		$requestQuery = (isset($parts['query'])) ? $parts['query'] : '';
		$requestMethod = (isset($_SERVER['REQUEST_METHOD'])) ? $_SERVER['REQUEST_METHOD'] : '';
		if ($requestMethod === 'POST' && $requestFile === 'tiki-admin.php' && $requestQuery === 'page=security'){
			$isRequestToSecurityAdmin = true;
		}
	}

	if ($prefs['ids_mode'] === 'log_block'
		&& (int)$prefs['ids_threshold']
		&& $manager->getImpact() > (int)$prefs['ids_threshold']
		&& !$isRequestToSecurityAdmin
	) {
		header("location: index.php");
		exit;
	}

}
