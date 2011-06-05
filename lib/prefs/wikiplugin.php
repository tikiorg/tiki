<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_wikiplugin_list($partial = false) {
	global $tikilib;

	if ($partial) {
		// Note that most of these will be disabled by an other feature check.
		$defaultPlugins = array(
			'article' => 'y',
			'articles' => 'y',
			'attach' => 'y',
			'author' => 'y',
			'bigbluebutton' => 'y',
			'box' => 'y',
			'calendar' => 'y',
			'category' => 'y',
			'catorphans' => 'y',
			'catpath' => 'y',
			'center' => 'y',
			'chart' => 'y',
			'code' => 'y',
			'content' => 'y',
			'copyright' => 'y',
			'div' => 'y',
			'dl' => 'y',
			'draw' => 'y',
			'events' => 'y',
			'fade' => 'y',
			'fancylist' => 'y',
			'fancytable' => 'y',
			'file' => 'y',
			'files' => 'y',
			'flash' => 'y',
			'googlemap' => 'y',
			'group' => 'y',
			'html' => 'y',
			'img' => 'y',
			'include' => 'y',
			'invite' => 'y',
			'kaltura' => 'y',
			'lang' => 'y',
			'map' => 'y',
			'mediaplayer' => 'y',
			'memberpayment' => 'y',
			'miniquiz' => 'y',
			'module' => 'y',
			'mouseover' => 'y',
			'payment' => 'y',
			'poll' => 'y',
			'quote' => 'y',
			'rcontent' => 'y',
			'remarksbox' => 'y',
			'rss' => 'y',
			'sheet' => 'y',
			'snarf_cache' => 0,
			'sort' => 'y',
			'split' => 'y',
			'sub' => 'y',
			'sup' => 'y',
			'survey' => 'y',
			'tabs' => 'y',
			'thumb' => 'y',
			'toc' => 'y',
			'topfriends' => 'y',
			'trackercomments' => 'y',
			'trackerfilter' => 'y',
			'trackeritemfield' => 'y',
			'trackerlist' => 'y',
			'trackertimeline' => 'y',
			'tracker' => 'y',
			'trackerprefill' => 'y',
			'trackerstat' => 'y',
			'trackertoggle' => 'y',
			'trackerif' => 'y',
			'transclude' => 'y',
			'translated' => 'y',
			'twitter' => 'y',
			'userlink' => 'y',
			'vimeo' => 'y',	
			'vote' => 'y',
			'youtube' => 'y',
			'zotero' => 'y',
		);

		$out = array();
		$list = array();
		$alias = array();
		foreach( glob( 'lib/wiki-plugins/wikiplugin_*.php' ) as $file )
		{
			$base = basename( $file );
			$plugin = substr( $base, 11, -4 );

			$list[] = $plugin;
		}

		global $prefs;
		if( isset($prefs['pluginaliaslist']) ) {
			$alias = @unserialize($prefs['pluginaliaslist']);
			$alias = array_filter($alias);
		}
		$list = array_filter(array_merge( $list, $alias ));
		sort($list);

		foreach( $list as $plugin ) {
			$preference = 'wikiplugin_' . $plugin;
			$out[$preference] = array(
				'default' => isset($defaultPlugins[$plugin]) ? 'y' : 'n',
				'default' => isset($defaultPlugins[$plugin]) ? 'y' : 'n'
			);
		}

		return $out;
	}

	$prefs = array();

	foreach( $tikilib->plugin_get_list() as $plugin ) {
		$info = $tikilib->plugin_info( $plugin );
		if (empty($info['prefs'])) $info['prefs'] = array();
		$dependencies = array_diff( $info['prefs'], array( 'wikiplugin_' . $plugin ) );

		$prefs['wikiplugin_' . $plugin] = array(
			'name' => tr( 'Plugin %0', $info['name'] ),
			'description' => $info['description'],
			'type' => 'flag',
			'help' => 'Plugin' . $plugin,
			'dependencies' => $dependencies,
		);
	}
	$prefs['wikiplugin_snarf_cache'] = array(
		'name' => tra('Global cache time for the plugin snarf in seconds'),
		'description' => tra('Default cache time for the plugin snarf') . ', ' . tra('0 for no cache'),
		'default' => 0,
		'dependencies' => array('wikiplugin_snarf'),
		'filter' => 'int',
		'type' => 'text'
	);

	return $prefs;
}
