<?php
// (c) Copyright 2002-2014 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_showpref_info()
{
	return array(
		'name' => tra('Show Preference'),
		'documentation' => 'PluginShowpref',
		'description' => tra('Display contents of certain whitelisted global preference'),
		'prefs' => array('wikiplugin_showpref'),
		'filter' => 'wikicontent',
		'icon' => 'img/icons/wrench.png',
		'params' => array(
			'pref' => array(
				'required' => true,
                                'name' => tra('Preference Name'),
                                'description' => tra('Name of preference to be displayed.'),
			),	
		),
	);
}

function wikiplugin_showpref($data, $params)
{
	global $prefs, $tikilib, $prefslib;
                $tikilib->get_user_preference($user, 'pref_filters', 'basic');
	// Security public prefs only, you would not want all prefs to be displayed via wiki syntax
	
	$name=$params['pref'];
	if ( false !== $pos = strpos($name, '_') ) {
			$file = substr($name, 0, $pos);
		} else {
			$file = 'global';
		}

	$inc_file = "lib/prefs/{$file}.php";
		if (file_exists($inc_file)) {
			require_once $inc_file;
			$function = "prefs_{$file}_list";
			if ( function_exists($function) ) {
				$preffile = $function($partial);
			} else {
				$preffile = array();
			}
	}

	if (empty($preffile[$name]['public'])) {
		return '';
	} else  {
		return $tikilib->get_preference($name);
	}	
	return '';
}
