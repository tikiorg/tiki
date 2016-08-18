<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_zotero_info()
{
	return array(
		'name' => tra('Zotero Citation'),
		'description' => tra('Retrieves and includes a Zotero reference in the page.'),
		'prefs' => array('zotero_enabled', 'wikiplugin_zotero', 'wikiplugin_footnote'),
		'iconname' => 'bookmark',
		'introduced' => 7,
		'params' => array(
			'key' => array(
				'name' => tra('Reference Key'),
				'description' => tra('Unique reference for the group associated to the site. Can be retrieved from the
					Zotero Bibliography module.'),
				'required' => false,
				'since' => '7.0',
				'filter' => 'alnum',
			),
			'tag' => array(
				'name' => tra('Reference Tag'),
				'description' => tra('Uses the first result using the specified tag. Useful when the tag mechanism is
					coerced into creating unique human memorizable keys.'),
				'since' => '7.0',
				'required' => false,
				'filter' => 'alnum',
			),
			'note' => array(
				'name' => tra('Note'),
				'description' => tra('Append a note to the reference for additional information, like page numbers or
					other sub-references.'),
				'since' => '7.0',
				'required' => false,
				'filter' => 'text',
			),
		),
	);
}

function wikiplugin_zotero($data, $params)
{
	$zotero = TikiLib::lib('zotero');
	$cachelib = TikiLib::lib('cache');

	$tag = null;
	$key = null;
	$note = null;

	if (isset($params['key'])) {
		$key = $params['key'];
		$cacheKey = "key_$key";
	} elseif (isset($params['tag'])) {
		$tag = $params['tag'];
		$cacheKey = "tag_$tag";
	} else {
		return WikiParser_PluginOutput::argumentError(array('key', 'tag'));
	}

	if (isset($params['note'])) {
		$note = $params['note'];
	}

	if ($cached = $cachelib->getCached($cacheKey, 'zotero')) {
		$info = unserialize($cached);
	} else {
		if ($key) {
			$info = $zotero->get_entry($key);
		} else {
			$info = $zotero->get_first_entry($tag);
		}

		$cachelib->cacheItem($cacheKey, serialize($info), 'zotero');
	}

	$content = $info['content'];
	$content = str_replace('<div', '<span', $content);
	$content = str_replace('</div>', '</span>', $content);
	return "{FOOTNOTE()}~np~{$content} {$note}~/np~{FOOTNOTE}";
}

