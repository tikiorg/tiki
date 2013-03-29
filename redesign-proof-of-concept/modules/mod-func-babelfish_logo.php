<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/**
 * @return array
 */
function module_babelfish_logo_info()
{
	return array(
		'name' => tra('Babel Fish Icon Link'),
		'description' => tra('Display an icon linked to the Yahoo! Babel Fish translation service'),
		'prefs' => array('feature_babelfish_logo'),
		'params' => array(),
	);
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_babelfish_logo($mod_reference, $module_params)
{
	global $smarty, $prefs;
	require_once('lib/Babelfish.php');
	$smarty->assign('babelfish_links', Babelfish::links($prefs['language']));
}
