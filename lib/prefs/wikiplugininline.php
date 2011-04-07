<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_wikiplugininline_list($partial = false) {
	global $tikilib;

	if ($partial) {
		$defaultInline = array(
			'file' => 'y',
			'getaccesstoken' => 'y',
			'googleanalytics' => 'y',
			'group' => 'y',
			'grouplist' => 'y',
			'mail' => 'y',
			'perm' => 'y',
			'smarty' => 'y',
			'trackeritemfield' => 'y',
			'transclude' => 'y',
			'zotero' => 'y',
		);

		$out = array();
		foreach( $tikilib->plugin_get_list() as $plugin ) {
			$preference = 'wikiplugininline_' . $plugin;
			$out[$preference] = array(
				'default' => isset($defaultInline[$plugin]) ? 'y' : 'n',
			);
		}

		return $out;
	}

	$prefs = array();

	foreach( $tikilib->plugin_get_list() as $plugin ) {
		$info = $tikilib->plugin_info( $plugin );

		$prefs['wikiplugininline_' . $plugin] = array(
			'name' => tr('Inline plugin %0', $info['name'] ),
			'description' => '',
			'type' => 'flag',
		);
	}

	return $prefs;
}
