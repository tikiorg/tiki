<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//require_once ('lib/tikilib.php');
//require_once ('tiki-setup.php');
require_once ("lib/copyrights/copyrightslib.php");

// Insert copyright notices
// Usage:
// {COPYRIGHT()}
// text
// ~title~ &copy; ~year~ ; ~authors~
// text
// {COPYRIGHT}
function wikiplugin_copyright_help() {
	return tra("Insert copyright notices").":<br />~np~{COPYRIGHT()}~title~~year~~authors~".tra("text")."{COPYRIGHT}~/np~";
}

function wikiplugin_copyright_info() {
	return array(
		'name' => tra('Copyright'),
		'documentation' => tra('PluginCopyright'),
		'description' => tra('Insert copyright notices'),
		'prefs' => array( 'wiki_feature_copyrights', 'wikiplugin_copyright' ),
		'body' => tra('Pattern to display the copyright in. May contain ~title~, ~year~ and ~authors~.'),
		'params' => array(
		),
	);
}

function wikiplugin_copyright($data, $params) {
	global $dbTiki;

	$copyrightslib = new CopyrightsLib;

	if (!isset($_REQUEST['page'])) {
		return '';
	}

	$result = '';

	$copyrights = $copyrightslib->list_copyrights($_REQUEST['page']);

	for ($i = 0; $i < $copyrights['cant']; $i++) {
		$notice = str_replace("~title~", $copyrights['data'][$i]['title'], $data);

		$notice = str_replace("~year~", $copyrights['data'][$i]['year'], $notice);
		$notice = str_replace("~authors~", $copyrights['data'][$i]['authors'], $notice);
		$result = $result . $notice;
	}

	global $tiki_p_edit_copyrights;

	if ((isset($tiki_p_edit_copyrights)) && ($tiki_p_edit_copyrights == 'y')) {
		$result = $result . "\n<a href=\"copyrights.php?page=" . $_REQUEST['page'] . "\">Edit copyrights</a> for ((" . $_REQUEST['page'] . "))\n";
	}

	return $result;
}
