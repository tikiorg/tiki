<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * SHOWPAGES plugin
 * Displays wiki pages that match a supplied pagename criteria.
 *
 * Usage:
 * {SHOWPAGES(find=>namepattern [, max=>qty])/}
 *
 * Params:
 * @param	namepattern	Search criteria for the page
 * @param	qty		Max qty of results to return
 *
 * Notes:
 * Make sure that the wiki page this is used on is not cached if you want more "live" results.
 * under normal loads and sites, this should be cached.
 *
 * @package Tikiwiki
 * @subpackage TikiPlugins
 * @version $Revision: 1.7 $
 */

function wikiplugin_showpages_help() {
	return tra("List wiki pages").":<br />~np~{SHOWPAGES(find=>criteria [, max=>qty] [, display=>name|desc])/}~/np~";
}

function wikiplugin_showpages_info() {
	return array(
		'name' => tra('Show Pages'),
		'documentation' => 'PluginShowPages',
		'description' => tra('Find pages by searching within page names'),
		'prefs' => array( 'wikiplugin_showpages' ),
		'icon' => 'pics/icons/page_find.png',
		'params' => array(
			'find' => array(
				'required' => true,
				'name' => tra('Find'),
				'description' => tra('Search criteria'),
				'default' => '',
			),
			'max' => array(
				'required' => false,
				'name' => tra('Result Count'),
				'description' => tra('Maximum amount of results displayed.'),
				'filter' => 'digits',
				'default' => '',
			),
			'display' => array(
				'required' => false,
				'name' => tra('Display'),
				'description' => tra('Display page name and/or description. Both displayed by default.'),
				'filter' => 'striptags',
				'default' => 'name|desc',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Name'), 'value' => 'name'), 
					array('text' => tra('Description'), 'value' => 'desc'),
					array('text' => tra('Name & Description'), 'value' => 'name|desc')
				)
			)
		)
	);
}

function wikiplugin_showpages($data, $params) {
	global $tikilib, $prefs;

	extract ($params,EXTR_SKIP);
	if (!isset($find)) {
		return ("<b>missing find parameter for plugin SHOWPAGES</b><br />");
	}

	if (!isset($max)) {
		$max = -1;
	}

	if (!isset($display) || (strpos($display,'name') === false && strpos($display,'desc') === false)) {
		$display = 'name|desc';
	}

	$data = $tikilib->list_pages(0, $max, 'pageName_asc', $find);

	$text = '';

	foreach ($data["data"] as $page) {
		if (isset($prefs['feature_wiki_description']) && $prefs['feature_wiki_description'] == 'y' && strpos($display,'desc') !== false) {
			$desc = $tikilib->page_exists_desc($page["pageName"]);
		} else {
			$desc = '';
		}
		$text .= "<a href=\"tiki-index.php?page=".$page["pageName"]."\" title=\"".tra("Last modified by")." ".$page["user"]."\" class=\"wiki\">";
		$text .= (strpos($display,'name') !== false || strlen($desc) == 0 ? $page["pageName"] : $desc);
		$text .= "</a>";
		$text .= (strpos($display,'name') !== false && $desc !== $page["pageName"] && strlen($desc) > 0 ? " - $desc" : "");
		$text .= "<br />";
	}

	return $text;
}
