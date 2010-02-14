<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_wikiplugin_list() {
	global $tikilib;

	$prefs = array();

	foreach( $tikilib->plugin_get_list() as $plugin ) {
		$info = $tikilib->plugin_info( $plugin );
		if (empty($info['prefs'])) $info['prefs'] = array();
		$dependencies = array_diff( $info['prefs'], array( 'wikiplugin_' . $plugin ) );

		$prefs['wikiplugin_' . $plugin] = array(
			'name' => tr( 'Enable plugin %0', $info['name'] ),
			'description' => $info['description'],
			'type' => 'flag',
			'help' => 'Plugin' . $plugin,
			'dependencies' => $dependencies,
		);
	}

	return $prefs;
}
