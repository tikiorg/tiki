<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
global $prefs, $tiki_p_admin;
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) != false) {
	header('location: index.php');
	exit;
}
$smarty = TikiLib::lib('smarty');
if ($prefs['error_reporting_adminonly'] == 'y' and $tiki_p_admin != 'y') {
	$errorReportingLevel = 0;
} elseif ($prefs['error_reporting_level'] == 2047) {
	$errorReportingLevel = E_ALL & ~E_STRICT;
} elseif ($prefs['error_reporting_level'] == 2039) {
	$errorReportingLevel = E_ALL & ~E_STRICT & ~E_NOTICE & ~E_USER_NOTICE;
} elseif ($prefs['error_reporting_level'] == -1) {
	$errorReportingLevel = E_ALL;
} elseif ($prefs['error_reporting_level'] == 1) {
	$errorReportingLevel = error_reporting();
} else {
	$errorReportingLevel = $prefs['error_reporting_level'];
}

// Handle Smarty notices
if (! empty($prefs['smarty_notice_reporting']) and $prefs['smarty_notice_reporting'] === 'y') {
	// FIXME: This reports all notices, whether or not they are from Smarty. But if we don't do it, we get no Smarty notices if they are enabled and notices are not. Solving involves clarifying the interface so that $prefs['smarty_notice_reporting'] depends on notices being reported.
	$errorReportingLevel = $errorReportingLevel | E_NOTICE | E_USER_NOTICE ;
}

set_error_handler('tiki_error_handling', $errorReportingLevel);
error_reporting($errorReportingLevel);

if ($prefs['log_sql'] == 'y' && $api_tiki == 'adodb') {
	$dbTiki->LogSQL();
	global $ADODB_PERF_MIN;
	$ADODB_PERF_MIN = $prefs['log_sql_perf_min'];
}

// TODO: check this only once per session or only if a feature ask for it
TikiSetup::check($tikidomain);

if (! isset($phpErrors)) {
	$phpErrors = [];
}
$smarty->assign_by_ref('phpErrors', $phpErrors);
