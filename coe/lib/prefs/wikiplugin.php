<?php

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
