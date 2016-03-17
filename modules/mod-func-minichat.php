<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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
function module_minichat_info()
{
	return array(
		'name' => tra('Minichat'),
		'description' => tra('Small live chat box'),
		'prefs' => array("feature_minichat"),
		'params' => array(
			'channels' => array(
				'name' => tra('Channels'),
				'description' => tra('List of chat channels. Channel names are separated by a comma (",").') . ' ' . tra('Example value:') . ' english,french. ' . tra('By default, a single channel named "default" exists.'),
				'filter' => 'striptags'
			)
		),
		'common_params' => array('rows')
	);
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_minichat($mod_reference, $module_params)
{
	$smarty = TikiLib::lib('smarty');
	global $prefs;
	if (isset($module_params["channels"])) {
		$channels = explode(',', $module_params["channels"]);
	} else
		$channels = array('default');

	if (isset($_SESSION['minichat_channels'])) {
		$channels = $_SESSION['minichat_channels'];
	}

	$jscode='';
	foreach ($channels as $k => $channel) {
		$channel = '#' . preg_replace('/[^a-zA-Z0-9\-\_]/i', '', $channel);
		$channel = substr($channel, 0, 30);
		$channels[$k] = $channel;

		$jscode .= "minichat_addchannel('" . $channel . "');\n";
	}

	$smarty->assign('jscode', $jscode);
}
