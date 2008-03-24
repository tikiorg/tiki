<?php

// $Header: /cvsroot/tikiwiki/tiki/lib/setup/error_reporting.php,v 1.2.2.1 2007-11-04 22:08:34 nyloth Exp $
// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for
// details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'],'tiki-setup.php')!=FALSE) {
  header('location: index.php');
  exit;
}

if ( $prefs['error_reporting_level'] == 1 ) $prefs['error_reporting_level'] = ( $tiki_p_admin == 'y' ) ? E_ALL : 0;
elseif ( $prefs['error_reporting_adminonly'] == 'y' and $tiki_p_admin != 'y' ) $prefs['error_reporting_level'] = 0;
error_reporting($prefs['error_reporting_level']);

if ( $prefs['log_sql'] == 'y' ) $dbTiki->LogSQL();

$tikifeedback = array();

// TODO: check this only once per session or only if a feature ask for it
TikiSetup::check($tikidomain);

if ( ! isset($phpErrors) ) $phpErrors = array();
$smarty->assign_by_ref('phpErrors', $phpErrors);
