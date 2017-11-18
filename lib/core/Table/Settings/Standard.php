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
 * Class Table_Settings_Standard
 *
 * Used for tables within Tiki, which typically have a smarty template and where ajax will be used
 */
class Table_Settings_Standard extends Table_Settings_Abstract
{
	protected $default2 = [
		'selflinks' => true,
		'sorts' => [
			'type' => 'reset',
			'group' => false,
			'multisort' => false,
		],
		'pager' => [
			'type' => true,
		],
		'ajax' => [
			'type' => true,
			'offset' => 'offset',
			'sortparam' => 'sort_mode',
			'numrows' => 'numrows',
			'url' => [
				'query' => '?{sort:sort}&{filter:filter}',
			],
		],
		'usecolselector' => true,
		'colselect' => [
			'type' => true,
		],
	];
}
