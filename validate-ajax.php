<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: list-tracker_field_values_ajax.php 25072 2010-02-11 15:18:57Z changi67 $

require_once('tiki-setup.php');

if ($prefs['feature_jquery'] != 'y' || $prefs['feature_jquery_validation'] != 'y') {
	echo '{}';
	exit;
}

if (empty($_REQUEST['validator']) || empty($_REQUEST["input"]) || empty($_REQUEST["parameter"])) {
	echo '{}';
	exit;
}

global $validatorslib;
include_once('lib/validatorslib.php');

if (!in_array($_REQUEST['validator'], $validatorslib->available)) {
	echo '{}';
	exit;
}

$validatorslib->setInput($_REQUEST["input"]);
$result = $validatorslib->validateInput($_REQUEST["validator"], $_REQUEST["parameter"]);

header( 'Content-Type: application/json' );
echo json_encode( $result );

