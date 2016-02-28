<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('tiki-setup.php');

if ($prefs['feature_jquery'] != 'y' || $prefs['feature_jquery_validation'] != 'y') {
	echo '{}';
	exit;
}

if (empty($_REQUEST['validator']) || empty($_REQUEST["input"]) && $_REQUEST["input"] != '0') {
	echo '{}';
	exit;
}

if (empty($_REQUEST["parameter"])) {
	$_REQUEST["parameter"] = '';
}

if (empty($_REQUEST["message"])) {
	$_REQUEST["message"] = '';
}

$validatorslib = TikiLib::lib('validators');

if (!in_array($_REQUEST['validator'], $validatorslib->available)) {
	echo '{}';
	exit;
}

$validatorslib->setInput($_REQUEST["input"]);
$result = $validatorslib->validateInput($_REQUEST["validator"], $_REQUEST["parameter"], $_REQUEST["message"]);

header('Content-Type: application/json');
echo json_encode($result);

