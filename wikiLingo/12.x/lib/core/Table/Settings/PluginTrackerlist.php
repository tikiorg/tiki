<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

/**
 * Class Table_Settings_PluginTrackerlist
 *
 * Adds settings specific to PluginTrackerlist tables
 */
class Table_Settings_PluginTrackerlist extends Table_Settings_Abstract
{
	protected $ts = array(
		'selflinks' => true,
		'sort' => array(
			'multisort' => false,	//$trklib->list_items doesn't seem to support multisorts
			'group' => true,
		),
		'ajax' => array(
			'custom' => false,		//url sort and filter params manipulated on the server side for this plugin
		)
	);
}

