<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_theme_list($partial = false)
{
	global $prefs;
	$themelib = TikiLib::lib('theme');
	
	//get list of themes and make the first character of array values uppercase
	$themes = array_map('ucfirst', $themelib->list_themes());
	//get list of theme options
	$theme_options = [
		'' => tr('None'),
	];
	if (! $partial) {
		$theme_options = $theme_options + $themelib->get_options();
	}
	
	//admin themes -> add empty option which means that site theme should be used. Also remove Custom URL.
	$admin_themes = [
		'' => tr('Site theme'),
	];
	$admin_themes = $admin_themes + $themes;
	unset($admin_themes['custom_url']); //remove custom URL from the list

	//get list of icon sets shipped by Tiki
	$iconsets = $themelib->list_base_iconsets();
	$iconsets['theme_specific_iconset'] = tr('Icons of the displayed theme'); //add a specific option to allow theme specific icon set to be used
	
	return array(
		'theme' => array(
			'name' => tr('Site theme'),
			'description' => tr('The default theme for the site. Themes are bootstrap.css variants, including updated legacy Tiki themes as well as themes from Bootswatch.com. For more information about Bootstrap, see getbootstrap.com.'),
			'type' => 'list',
			'default' => 'default',
			'options' => $themes,
			'help' => 'Themes',
			'tags' => array('basic'),
		),
		'theme_option' => array(
			'name' => tra('Site theme option'),
			'type' => 'list',
			'help' => 'Themes',
			'description' => tra('Supplemental style sheet for the selected theme'),
			'options' => $theme_options,
			'default' => '',
			'tags' => array('basic'),
			'keywords' => tra('theme option, theme-option, style option, options, css'),
		),		
		'theme_custom_url' => array(
			'name' => tr('Custom theme URL'),
			'description' => tr('Local or external URL of the custom Bootstrap-compatible CSS file to use.'),
			'type' => 'text',
			'filter' => 'url',
			'default' => '',
			'tags' => array('basic'),
		),
		'theme_admin' => array(
			'name' => tra('Admin theme'),
			'type' => 'list',
			'help' => 'Themes',
			'description' => tra('Theme for the settings panels.'),
			'options' => $admin_themes,
			'default' => '',
			'tags' => array('basic'),
		),
		'theme_option_admin' => array(
			'name' => tra('Admin theme option'),
			'type' => 'list',
			'help' => 'Themes',
			'description' => tra('Supplemental style sheet for the selected theme'),
			'options' => $theme_options,
			'default' => '',
			'tags' => array('basic'),
		),
		'theme_iconset' => array(
			'name' => tr('Icons'),
			'description' => tr('Icon set used by the site.'),
			'type' => 'list',
			'options' => $iconsets,
			'default' => 'default',
			'help' => 'Icons',
			'tags' => array('basic'),
		),
	);
}