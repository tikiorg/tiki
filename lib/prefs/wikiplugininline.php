<?php

function prefs_wikiplugininline_list() {
	global $tikilib;

	$prefs = array();

	foreach( $tikilib->plugin_get_list() as $plugin ) {
		$info = $tikilib->plugin_info( $plugin );

		$prefs['wikiplugininline_' . $plugin] = array(
			'name' => tr( 'Inline plugin %0 (hide plugin edit icon)', $info['name'] ),
			'description' => tr('When inlined, the plugin edit icon will not appear.'),
			'type' => 'flag',
		);
	}

	return $prefs;
}
