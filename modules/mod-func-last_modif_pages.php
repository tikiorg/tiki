<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
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
function module_last_modif_pages_info()
{
	return array(
		'name' => tra('Latest Changes'),
		'description' => tra('List the specified number of pages, starting from the most recently modified.'),
		'prefs' => array("feature_wiki"),
		'params' => array(
			'absurl' => array(
				'name' => tra('Absolute URL'),
				'description' => tra('If set to "y", some of the links use an absolute URL instead of a relative one. This can avoid broken links if the module is to be sent in a newsletter, for example.') . " " . tr('Default: "n".')
			),
			'url' => array(
				'name' => tra('Link Target'),
				'description' => tra('Target URL of the "...more" link at the bottom of the module.') . " " . tr('Default:') . ' tiki-lastchanges.php'
			),
			'maxlen' => array(
				'name' => tra('Maximum Length'),
				'description' => tra('Maximum number of characters in page names allowed before truncating.'),
				'filter' => 'int'
			),
			'show_namespace' => array(
					'name' => tra('Show Namespace'),
					'description' => tra('Show namespace prefix in page names.').' ( y / n )',	// Do not translate y/n
					'default' => 'y'
			)
		),
		'common_params' => array('nonums', 'rows')
	);
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_last_modif_pages($mod_reference, $module_params)
{
	$tikilib = TikiLib::lib('tiki');
	$smarty = TikiLib::lib('smarty');
	$ranking = $tikilib->list_pages(0, $mod_reference['rows'], 'lastModif_desc', '', '', true, false, false, false, '', false, 'y');

	$smarty->assign('modLastModif', $ranking["data"]);
	$smarty->assign('maxlen', isset($module_params["maxlen"]) ? $module_params["maxlen"] : 0);
	$smarty->assign('absurl', isset($module_params["absurl"]) ? $module_params["absurl"] : 'n');
	$smarty->assign('url', isset($module_params["url"]) ? $module_params["url"] : 'tiki-lastchanges.php');
	$smarty->assign('namespaceoption', isset($module_params['show_namespace']) ? $module_params['show_namespace'] : 'n');
}
