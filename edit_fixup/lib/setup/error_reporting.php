<?php

// $Id$
// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for
// details.

//this script may only be included - so its better to die if called directly.
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

if ( $prefs['error_reporting_level'] == 2047 && $prefs['error_reporting_adminonly'] == 'y' ) {
	$prefs['error_reporting_level'] = ( $tiki_p_admin == 'y' ) ? 2047 : 0; // 2047 means E_ALL in PHP < 5.2.x (if we support PHP >= 5.2 only, 6143 should be used instead)
} elseif ( $prefs['error_reporting_level'] == 2039 ) {
	$prefs['error_reporting_level'] = ( $tiki_p_admin == 'y' ) ? 2039 : 0; // should mean E_ALL & ~E_NOTICE
}

if ( $prefs['error_reporting_adminonly'] == 'y' and $tiki_p_admin != 'y' ) {
	$prefs['error_reporting_level'] = 0;
}

if ($prefs['error_reporting_level'] != 0) {
	ini_set('display_errors', 1); // just in case the server allows it
} else {
	$prefs['error_reporting_level'] = 0;
}

error_reporting($prefs['error_reporting_level']);

if ( $prefs['log_sql'] == 'y' && $api_tiki == 'adodb' ) {
	$dbTiki->LogSQL();
	global $ADODB_PERF_MIN;
	$ADODB_PERF_MIN = $prefs['log_sql_perf_min'];
}

$tikifeedback = array();

// TODO: check this only once per session or only if a feature ask for it
TikiSetup::check($tikidomain);

if ( ! isset($phpErrors) ) $phpErrors = array();
$smarty->assign_by_ref('phpErrors', $phpErrors);
