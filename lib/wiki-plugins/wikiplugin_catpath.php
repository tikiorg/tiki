<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_catpath_info() {
	return array(
		'name' => tra('Category Path'),
		'documentation' => 'PluginCatPath',
		'description' => tra('Show the full category path for a wiki page'),
		'prefs' => array( 'feature_categories', 'wikiplugin_catpath' ),
		'icon' => 'pics/icons/sitemap_color.png',
		'params' => array(
			'divider' => array(
				'required' => false,
				'name' => tra('Separator'),
				'description' => tra('String used to separate the categories in the path. Default character is >.'),
				'default' => '>',
			),
			'top' => array(
				'required' => false,
				'name' => tra('Display Top Category'),
				'description' => tra('Show the top category as part of the path name (not shown by default)'),
				'filter' => 'alpha',
				'default' => 'no',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				),
			),
		),
	);
}

function wikiplugin_catpath($data, $params) {
	global $dbTiki, $smarty, $tikilib, $prefs, $categlib;

	if (!is_object($categlib)) {
		require_once ("lib/categories/categlib.php");
	}

	if ($prefs['feature_categories'] != 'y') {
		return "<span class='warn'>" . tra("Categories are disabled"). "</span>";
	}

	extract ($params,EXTR_SKIP);

	// default divider is '>'
	if (!(isset($divider))) {
		$divider = '>';
	}

	// default setting for top is 'no'
	if (!(isset($top))) {
		$top = 'no';
	} elseif ($top != 'y' and $top != 'yes' and $top != 'n' and $top != 'no') {
		$top = 'no';
	}

	$objId = urldecode($_REQUEST['page']);

	$cats = $categlib->get_object_categories('wiki page', $objId);

	$catpath = '';

	foreach ($cats as $categId) {
		$catpath .= '<span class="categpath">';

		// Display TOP on each line if wanted
		if ($top == 'yes' or $top == 'y') {
			$catpath .= '<a class="categpath" href="tiki-browse_categories.php?parentId=0">TOP</a> ' . $divider . ' ';
		}

		$path = '';
		$info = $categlib->get_category($categId);
		$path
			= '<a class="categpath" href="tiki-browse_categories.php?parentId=' . $info["categId"] . '">' . htmlspecialchars($info["name"]) . '</a>';

		while ($info["parentId"] != 0) {
			$info = $categlib->get_category($info["parentId"]);

			$path = '<a class="categpath" href="tiki-browse_categories.php?parentId=' . $info["categId"] . '">' . htmlspecialchars($info["name"]) . '</a> ' . htmlspecialchars($divider) . ' ' . $path;
		}

		$catpath .= $path . '</span><br />';
	}

	return $catpath;
}
