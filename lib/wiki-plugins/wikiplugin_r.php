<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_rr.php 59285 2016-07-27 11:58:36Z xavidp $
//
// Initial inspiration from the R Plugin for Mediawiki (2006- Sigbert Klinke (sigbert@wiwi.hu-berlin.de), Markus Cozowicz, Michael Cassin)
// Fully rewritten by the Tiki community for LGPL licencing compliance
//
// Parses R code (r-project.org) and shows the output in a wiki page.
// Corresponding author: Xavier de Pedro. <xavier.depedro (a) ub.edu>
// Contributors: Rodrigo Sampaio, Lukáš Mašek, Louis-Philippe Huberdau, Sylvie Greverend
// Usage:
// {R()}R code{R}. See documentation: http://doc.tiki.org/PluginR
//
// $Id: wikiplugin_r.php 46110 2013-05-31 12:36:01Z xavidp $


require_once('lib/wiki-plugins/wikiplugin_rr.php');

/**
 * This plugin is just an alias to wikiplugin_rr.php. The only difference
 * is that while wikiplugin_rr.php accepts unsecure R commands (once validated
 * the plugin call by a tiki admin), this one only accepts secure R commands,
 * without the need of any validation. For a list of accepted commands see
 * checkCommands() on wikiplugin_rr.php
 *
 * Probably the same functionality could be achieved using Plugin Alias feature
 * (http://doc.tiki.org/PluginAlias)
 */
function wikiplugin_r_info()
{
	$info = [
		'name' => tra('R syntax'),
		'documentation' => 'PluginR',
		'prefs' => [ 'wikiplugin_r' ],
		'description' => tra('Parses R syntax and shows the output either from the code introduced between the plugin tags or from the file attached to a tracker item sent through PluginTracker. It can also be combined with Pretty Trackers to edit params from the script through web forms.'),
	];

	$info = array_merge(wikiplugin_rr_info(), $info);
	unset($info['params']['security']);
	unset($info['validate']);

	return $info;
}

function wikiplugin_r($data, $params)
{
	$params['security'] = 1;
	$params['caption'] = "R Code";
	return wikiplugin_rr($data, $params);
}
