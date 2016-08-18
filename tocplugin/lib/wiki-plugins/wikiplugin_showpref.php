<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_showpref_info()
{
	return array(
		'name' => tra('Show Preference'),
		'documentation' => 'PluginShowpref',
		'description' => tra('Display the value of public global preferences'),
		'prefs' => array('wikiplugin_showpref'),
		'filter' => 'wikicontent',
		'iconname' => 'cog',
		'introduced' => 13,
		'params' => array(
			'pref' => array(
				'required' => true,
				'name' => tra('Preference Name'),
				'description' => tra('Name of preference to be displayed.'),
				'since' => '13.0',
				'filter' => 'text',
			),
		),
	);
}

function wikiplugin_showpref($data, $params)
{
	global $prefs;
	$tikilib = TikiLib::lib('tiki');

	$name = $params['pref'];
	if ( substr($name, 0, 3) == 'ta_' ) {
		$midpos = strpos($name, '_', 3);
		$pos = strpos($name, '_', $midpos + 1);
		$file = substr($name, 0, $pos);
        } elseif ( false !== $pos = strpos($name, '_') ) {
		$file = substr($name, 0, $pos);
	} else {
		$file = 'global';
	}

	$inc_file = "lib/prefs/{$file}.php";
	if (substr($file, 0, 3) == "ta_") {
		$paths = TikiAddons::getPaths();
		$package = str_replace('_', '/', substr($file, 3));
		$inc_file = $paths[$package] .  "/prefs/{$file}.php";
	}
	
	if (file_exists($inc_file)) {
		require_once $inc_file;
		$function = "prefs_{$file}_list";
		if ( function_exists($function) ) {
			$preffile = $function();
		} else {
			$preffile = array();
		}
	}

	// Security public prefs only, you would not want all prefs to be displayed via wiki syntax

	if (isset($preffile[$name]['public']) && $preffile[$name]['public']) {
		return $tikilib->get_preference($name);	
	} else  {
		return '';
	}	
}
