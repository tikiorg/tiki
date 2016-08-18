<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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
 * Class Table_Settings_PluginWithAjax
 *
 * Standard settings for tables used in plugins when ajax may be used (e.g., tracker list or list)
 */
class Table_Settings_PluginWithAjax extends Table_Settings_Plugin
{
	protected $ts = array(
		'selflinks' => true,
		'sorts' => array(
			'multisort' => false,	//$trklib->list_items doesn't seem to support multisorts
			'group' => true,
		),
		'ajax' => array(
			'custom' => false,		//url sort and filter params manipulated on the server side for this plugin
		)
	);
}

