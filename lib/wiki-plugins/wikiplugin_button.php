<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_button.php 26196 2010-03-18 14:08:55Z sylvieg $

// this script may only be included - so it's better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  die;
}

function wikiplugin_button_info() {
	return array(
		'name' => tra('Button'),
		'documentation' => 'PluginButton',			
		'description' => tra('Produces a link with the shape of a button, reusing the button smarty function and requiring no validation of that plugin'),
		'prefs' => array('wikiplugin_button'),
		'validate' => 'none',
		'extraparams' => false,
		'params' => array(
			'href' => array(
				'required' => true,
				'name' => tra('Url'),
				'description' => 'URL to be produced by the button. You can use wiki argument variables like {{itemId}} in it',
				'filter' => 'url',
			),
			'_text' => array(
				'required' => false,
				'name' => tra('Label'),
				'description' => 'Label for the button',
				'filter' => 'word',
			),
		),
	);
}

function wikiplugin_button($data, $params) {
	global $tikilib,$smarty;
	if (empty($params['href'])) {
		return tra('Incorrect param');
	}
	$path = 'lib/smarty_tiki/function.button.php';
	if (!file_exists($path)) {
		return tra('lib/smarty_tiki/function.button.php is missing or unreadable');
	}

	// Parse wiki argument variables in the url, if any (i.e.: {{itemId}} for it's numeric value).
	$tikilib->parse_wiki_argvariable($params['href']);

	include_once($path);
	$func = 'smarty_function_button';
	$content = $func($params, $smarty);
	return '~np~'.$content.'~/np~';
}
