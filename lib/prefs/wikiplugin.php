<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_wikiplugin_list($partial = false) {
	global $tikilib;

	if ($partial) {
		return array();
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
