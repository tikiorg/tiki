<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: mod-func-mustread.php 52093 2014-07-23 19:11:54Z lphuberdeau $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/**
 * @return array
 */
function module_collapse_info()
{
	return array(
		'name' => tr('Collapse Button'),
		'description' => tr('Bootstrap collapse button.'),
		'params' => array(
			'target' => array(
				'required' => true,
				'name' => tr('Target'),
				'description' => tr('CSS selector defining which objects get collapsed.'),
				'filter' => 'xss',
			),
			'containerclass' => array(
				'required' => false,
				'name' => tr('CSS Class'),
				'description' => tr('CSS class for containing DIV element'),
				'filter' => 'text',
				'default' => 'navbar-header',
			),
			'parent' => array(
				'required' => false,
				'name' => tr('Parent'),
				'description' => tr("CSS selector defining the collapsing objects' container."),
				'filter' => 'xss',
			),
		),
	);
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_collapse($mod_reference, $module_params)
{
}
