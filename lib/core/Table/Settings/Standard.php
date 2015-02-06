<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Plugin.php 53199 2014-11-27 03:36:22Z lindonb $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

/**
 * Class Table_Settings_Standard
 *
 * Used for tables within Tiki, which typically have a smarty template and where ajax will be used
 */
class Table_Settings_Standard extends Table_Settings_Abstract
{
	protected $default2 = array(
		'selflinks' => true,
		'sorts' => array(
			'type' => 'reset',
			'group' => false,
			'multisort' => false,
		),
		'pager' => array(
			'type' => true,
		),
		'ajax' => array(
			'type' => true,
			'offset' => 'offset',
			'sortparam' => 'sort_mode',
			'numrows' => 'numrows',
			'url' => array(
				'query' => '?{sort:sort}&{filter:filter}',
			),
		),
		'usecolselector' => true,
		'colselect' => array(
			'type' => true,
		),
	);
}

