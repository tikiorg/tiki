<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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
			'format' => array(
				'required' => false,
				'name' => tra('Date format'),
				'description' => tra('Set date and time format according to site settings.'),
				'since' => '15.0',
				'filter' => 'text',
				'options' => [
					['text' => '', 'value' => ''],
					['text' => tra('Long date'), 'value' => 'long_date'],
					['text' => tra('Short date'), 'value' => 'short_date'],
					['text' => tra('Long datetime'), 'value' => 'long_datetime'],
					['text' => tra('Short datetime'), 'value' => 'short_datetime'],
					['text' => tra('ISO'), 'value' => 'iso'],
				],
			),
		),
	);
}

function wikiplugin_lastmod($data, $params)
{
	$tikilib = TikiLib::lib('tiki');
	global $page, $user;
	//set page
	if (!isset($params['page'])) {
		if (!empty($page)) {
			$thispage = $page;
		} else {
			return false;
		}
	} else {
		$thispage = $params['page'];
	}
	//set datetime format
	$format = isset($params['format']) ? $params['format'] : 'long_datetime';
	switch ($format) {
		case 'long_date':
			$lastmod = $tikilib->get_long_date($tikilib->page_exists_modtime($thispage), $user);
			break;
		case 'short_date':
			$lastmod = $tikilib->get_short_date($tikilib->page_exists_modtime($thispage), $user);
			break;
		case 'short_datetime':
			$lastmod = $tikilib->get_short_datetime($tikilib->page_exists_modtime($thispage), $user);
			break;
		case 'iso':
			$lastmod = $tikilib->get_iso8601_datetime($tikilib->page_exists_modtime($thispage), $user);
			break;
		default:
			$lastmod = $tikilib->get_long_datetime($tikilib->page_exists_modtime($thispage), $user);
	}
	return $lastmod;
}
