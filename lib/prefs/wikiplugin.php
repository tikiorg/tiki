<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_wikiplugin_list($partial = false)
{
	$parserlib = TikiLib::lib('parser');

	// Note that most of the plugins controlled by the following preferences will be disabled by another feature check. For example, PluginCalendar depends not only on wikiplugin_calendar, but also on feature_calendar.
	// There is inefficiency in this data structure and the calls to in_array() below.
	// PHP 7 TODO: Once we require PHP 7+, this native array should be replaced with Ds\Set.
	$defaultPlugins = [
		'article',
		'articles',
		'attach',
		'author',
		'bigbluebutton',
		'box',
		'calendar',
		'category',
		'catorphans',
		'catpath',
		'center',
		'chart',
		'code',
		'content',
		'copyright',
		'div',
		'dl',
		'draw',
		'events',
		'fade',
		'fancylist',
		'fancytable',
		'file',
		'files',
		'googlemap',
		'group',
		'html',
		'img',
		'include',
		'invite',
		'kaltura',
		'lang',
		'list',
		'map',
		'mediaplayer',
		'memberpayment',
		'miniquiz',
		'module',
		'mouseover',
		'now',
		'payment',
		'poll',
		'quote',
		'rcontent',
		'remarksbox',
		'rss',
		'sheet',
		'sort',
		'split',
		'sub',
		'sup',
		'survey',
		'tabs',
		'thumb',
		'toc',
		'topfriends',
		'trackercomments',
		'trackerfilter',
		'trackeritemfield',
		'trackerlist',
		'trackertimeline',
		'tracker',
		'trackerprefill',
		'trackerstat',
		'trackertoggle',
		'trackerif',
		'transclude',
		'translated',
		'twitter',
		'userlink',
		'vimeo',
		'vote',
		'youtube',
		'zotero',
	];

	if ($partial) {
		$out = [];
		$list = [];
		$alias = [];
		foreach (glob('lib/wiki-plugins/wikiplugin_*.php') as $file) {
			$base = basename($file);
			$plugin = substr($base, 11, -4);

			$list[] = $plugin;
		}

		global $prefs;
		if (isset($prefs['pluginaliaslist'])) {
			$alias = @unserialize($prefs['pluginaliaslist']);
			$alias = array_filter($alias);
		}
		$list = array_filter(array_merge($list, $alias));
		sort($list);

		foreach ($list as $plugin) {
			$preference = 'wikiplugin_' . $plugin;
			$out[$preference] = [
				'default' => in_array($plugin, $defaultPlugins) ? 'y' : 'n',
			];
		}
		$out['wikiplugin_snarf_cache'] = ['default' => 0];
		$out['wikiplugin_list_gui'] = ['default' => 'n'];
		$out['wikiplugin_maximum_passes'] = ['default' => 500];

		return $out;
	}

	$prefs = [];

	foreach ($parserlib->plugin_get_list() as $plugin) {
		$info = $parserlib->plugin_info($plugin);
		if (empty($info['prefs'])) {
			$info['prefs'] = [];
		}
		$dependencies = array_diff($info['prefs'], [ 'wikiplugin_' . $plugin ]);

		$prefs['wikiplugin_' . $plugin] = [
			'name' => tr('Plugin %0', $info['name']),
			'description' => isset($info['description']) ? $info['description'] : '',
			'type' => 'flag',
			'help' => 'Plugin' . $plugin,
			'dependencies' => $dependencies,
			'packages_required' => (isset($info['packages_required']) && ! empty($info['packages_required'])) ? $info['packages_required'] : [],
			'default' => in_array($plugin, $defaultPlugins) ? 'y' : 'n',
		];

		if (isset($info['tags'])) {
			$prefs['wikiplugin_' . $plugin]['tags'] = (array) $info['tags'];
		}
	}

	// The wikiplugin_snarf_cache preference does not toggle some SNARFCACHE plugin, but controls the cache time of the SNARF plugin.
	$prefs['wikiplugin_snarf_cache'] = [
		'name' => tra('Global cache time for the plugin snarf'),
		'description' => tra('Default cache time for the plugin snarf'),
		'default' => 0,
		'shorthint' => tra('0 for no cache'),
		'dependencies' => ['wikiplugin_snarf'],
		'filter' => 'int',
		'units' => tra('seconds'),
		'type' => 'text'
	];

	// temporary pref for developpment of the list plugin GUI
	$prefs['wikiplugin_list_gui'] = [
		'name' => tr('GUI for the list plugin'),
		'description' => tr('Experimental GUI for the list plugin in popup plugin edit forms.') . (' ') . tr('(listgui)'),
		'tags' => ['experimental'],
		'default' => 'n',
		'dependencies' => ['wikiplugin_list'],
		'filter' => 'alpha',
		'type' => 'flag'
	];

	$prefs['wikiplugin_maximum_passes'] = [
		'name' => tr('Maximum plugin parsing passes'),
		'description' => tr('Affects the number of wiki plugins that can be used. The default of 500 allows for 1 plugin nested seven times, or 44 plugins nested once. A value of 1200 is required for 1 eighth level plugin.'),
		'default' => 500,
		'dependencies' => ['feature_wiki'],
		'filter' => 'digits',
		'type' => 'text',
		'units' => tr('passes'),
		'tags' => ['experimental'],
		'warning' => tr('Setting this to a higher value than the default of 500 may have performance implications.'),
	];

	return $prefs;
}
