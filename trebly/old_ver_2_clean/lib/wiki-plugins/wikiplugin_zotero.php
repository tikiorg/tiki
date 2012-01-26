<?php

function wikiplugin_zotero_info()
{
	return array(
		'name' => tra('Zotero Citation'),
		'description' => tra('Retrieves and includes a Zotero reference in the page.'),
		'prefs' => array('zotero_enabled', 'wikiplugin_zotero', 'wikiplugin_footnote'),
		'params' => array(
			'key' => array(
				'name' => tra('Reference Key'),
				'description' => tra('Unique reference for the group associated to the site. Can be retrieved from the Zotero Bibliography module.'),
				'required' => true,
				'filter' => 'alnum',
			),
		),
	);
}

function wikiplugin_zotero($data, $params)
{
	if (! isset($params['key'])) {
		return WikiParser_PluginOutput::argumentError(array('key'));
	}

	$zotero = TikiLib::lib('zotero');
	$cachelib = TikiLib::lib('cache');

	$key = $params['key'];

	if ($cached = $cachelib->getCached($key, 'zotero')) {
		$info = unserialize($cached);
	} else {
		$info = $zotero->get_entry($key);

		$cachelib->cacheItem($key, serialize($info), 'zotero');
	}

	$content = $info['content'];
	$content = str_replace('<div', '<span', $content);
	$content = str_replace('</div>', '</span>', $content);
	return "{FOOTNOTE()}~np~{$content}~/np~{FOOTNOTE}";
}

