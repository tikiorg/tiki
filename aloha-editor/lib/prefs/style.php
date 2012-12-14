<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_style_list($partial = false)
{
	global $tikilib, $prefs;

	$all_styles = array();
	$styles = array(
		'' => tra('None'),
	);
	$style_options = array(
		'' => tra('None'),
	);
	if (! $partial) {
		$all_styles = $tikilib->list_styles();
		foreach ($all_styles as $style) {
			$styles[$style] = substr($style, 0, strripos($style, '.css'));
		}

		$list = $tikilib->list_style_options($prefs['site_style']);
		if (!empty($list)) {
			foreach ($list as $opt) {
				$style_options[$opt] = str_replace('.css', '', $opt);
			}
		}
	}

	$all = glob('styles/*/options/*.css');
	foreach ( $all as $location ) {
		$option = basename($location);

		if ( ! isset( $style_options[$option] ) ) {
			$style_options[$option] = "X - $option";
		}
	}

	return array(
		'style_option' => array(
			'name' => tra('Theme options'),
			'type' => 'list',
			'help' => 'Themes',
			'description' => tra('Style options'),
			'options' => $style_options,
			'default' => '',
			'tags' => array('basic'),
		),
		'style_admin' => array(
			'name' => tra('Admin Theme'),
			'type' => 'list',
			'help' => 'Themes',
			'description' => tra('Style of the admin pages.'),
			'options' => $styles,
			'default' => '',
		),
		'style_admin_option' => array(
			'name' => tra('Admin Theme options'),
			'type' => 'list',
			'help' => 'Themes',
			'description' => tra('Style options for admin pages'),
			'options' => $style_options,
			'default' => '',
		),
	);
}
