<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_randominclude_info() {
	return array(
		'name' => tra('Random Include'),
		'documentation' => 'PluginRandomInclude',
		'description' => tra('Include a random page\'s content.'),
		'prefs' => array('wikiplugin_randominclude'),
		'icon' => 'pics/icons/page_copy.png',
		'params' => array(),
	);
}

function wikiplugin_randominclude($data, $params) {
	global $tikilib,$userlib,$user,$page;
	static $included_pages, $data;

	$params=array($page);
	$query='SELECT count(*) AS `max` FROM `tiki_pages` WHERE `pageName`!=?';
	$cant = $tikilib->getOne($query,$params);
	if ($cant) {
		$pick = rand(0, $cant - 1);
			
		$query = 'select `pageName` from `tiki_pages` WHERE `pageName`!=?';
		$incpage = $tikilib->getOne($query,$params,1,$pick);
		if (isset($included_pages[$incpage])) return ''; //don't include random pages into random pages
	} else {
		return '';
	}

	$included_pages[$incpage] = 1;
        // only evaluate permission the first time round
        // evaluate if object or system permissions enables user to see the included page
        $data = $tikilib->get_page_info($incpage);
	$perms = $tikilib->get_perm_object($incpage, 'wiki page', $data, false);
	if ($perms['tiki_p_view'] != 'y') {
		return '';
	}
	$text = $data['data'];
	$tikilib->parse_wiki_argvariable($text);
	return $text;
}
