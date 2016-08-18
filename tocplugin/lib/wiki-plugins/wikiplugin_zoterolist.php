<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_zoterolist_info()
{
	return array(
		'name' => tra('Zotero Reference List'),
		'description' => tra('Display the complete reference list from a Zotero server'),
		'prefs' => array('zotero_enabled', 'wikiplugin_zoterolist'),
		'iconname' => 'bookmark',
		'introduced' => 7,
		'params' => array(
			'tag' => array(
				'name' => tra('Tag'),
				'description' => tra('Provide the list of references with a given tag.'),
				'since' => '7.0',
				'required' => true,
				'filter' => 'alnum',
			),
		),
	);
}

function wikiplugin_zoterolist($data, $params)
{
	if (! isset($params['tag'])) {
		return WikiParser_PluginOutput::argumentError(array('tag'));
	}

	$cachelib = TikiLib::lib('cache');
	$tikilib = TikiLib::lib('tiki');

	$cacheKey = 'zoterolist.' . $params['tag'];

	if ($cached = $cachelib->getSerialized($cacheKey, 'zotero')) {
		if (($cached['created'] + 3600) > $tikilib->now) {
			return WikiParser_PluginOutput::html($cached['data']);
		}
	}
	
	$zoterolib = TikiLib::lib('zotero');
	$html = $zoterolib->get_formatted_references($params['tag']);

	$cachelib->cacheItem($cacheKey, serialize(array('created' => $tikilib->now, 'data' => $html)), 'zotero');

	if ($html) {
		return WikiParser_PluginOutput::html($html);
	} else {
		return WikiParser_PluginOutput::error(tra('Error'), tra('No results obtained. The Zotero citation server may be down.'));
	}
}
