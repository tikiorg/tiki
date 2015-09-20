<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_lastmod_info()
{
	return array(
		'name' => tra('Last Modification'),
		'documentation' => 'PluginLastMod',
		'description' => tra('Show the last modification date for a page'),
		'prefs' => array('feature_wiki', 'wikiplugin_lastmod'),
		'iconname' => 'edit',
		'introduced' => 2,
		'params' => array(
			'page' => array(
				'required' => false,
				'name' => tra('Page'),
				'description' => tra('Page name to display information of. Default value is current page.'),
				'since' => '2.0',
				'profile_reference' => 'wiki_page',
				'filter' => 'pagename',
			),
		),
	);
}

function wikiplugin_lastmod($data, $params)
{
	global $tikilib, $page;

	if (!isset($params['page'])) {
		if (!empty($page)) {
			$thispage = $page;
		} else {
			return;
		}
	} else {
		$thispage = $params['page'];
	}

	$lastmod = $tikilib->date_format("%a, %e %b %Y %H:%M:%S %Z", $tikilib->page_exists_modtime($thispage));

	return $lastmod;

}
