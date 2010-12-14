<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// This script may only be included - so it's better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/*
PHPLayersMenus in Tiki !

That smarty function is mostly intended to be used in .tpl files
syntax: {phplayers [type=tree|phptree|plain|hort|vert] [id=1] [file=/path/to/menufile]}

*/
function smarty_function_phplayers($params, &$smarty) {
	if (empty($params)) return '';

	global $prefs, $tikiphplayers;
	include_once('lib/phplayers_tiki/tiki-phplayers.php');

	if ($prefs['feature_phplayers'] != 'y') {
		return tra("PHPLayersMenus are not enabled on this site");
	}

	if (empty($params['type'])) {
		$params['type'] = 'tree';
	}
	if (!isset($params['sectionLevel'])) {
		$params['sectionLevel'] = '';
	}
	if (!isset($params['translate'])) {
		$params['translate'] = 'y';
	}

	$use_items_icons = false;
	if (!empty($params['id'])) {
		$params['output'] = $tikiphplayers->mkMenuEntry(
			$params['id'],
			$params['curOption'],
			$params['sectionLevel'],
			$params['translate'],
			$use_items_icons // Passed by reference to change the value
		);
	}
	if (!isset($params['file'])) {
		$params['file'] = '';
	}

	$return = $tikiphplayers->mkMenu($params['output'], 'usermenu'.$params['id'], $params['type'], $params['file'], $params['curOption']);
	if ( $use_items_icons ) $return = str_replace('class="mdkverbar"', 'class="mdkverbar mdkverbar-with-icons"', $return);

	return '<div class="role_navigation">' . $return . '</div>';
}
