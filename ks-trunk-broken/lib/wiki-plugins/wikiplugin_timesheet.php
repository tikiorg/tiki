<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_timesheet_info()
{
	return array(
		'name' => tra('TimeSheet'),
		'documentation' => 'Timesheet',
		'description' => tra('A portable timesheet usable in a webpage'),
		'prefs' => array('wikiplugin_timesheet', 'feature_time_sheet'),
		'body' => tra('text'),
		'icon' => 'img/icons/layout_header.png',
		'tags' => array( 'basic' ),
		'params' => array(
		),
	);
}

function wikiplugin_timesheet($data, $params)
{
	global $tikilib, $tiki_p_view_trackers, $tiki_p_create_tracker_items;
	extract($params, EXTR_SKIP);

	if ( $tiki_p_view_trackers != "y" || $tiki_p_create_tracker_items != "y") return "";

	TikiLib::lib("header")
		->add_cssfile("lib/jquery/jtrack/css/jtrack.css")
		->add_jsfile("lib/jquery/jtrack/js/domcached-0.1-jquery.js")
		->add_jsfile("lib/jquery/jtrack/js/jtrack.js")
		->add_jq_onready(
			"jTask.init();"
		);
	return "~np~" . TikiLib::lib("smarty")->fetch('wiki-plugins/wikiplugin_timesheet.tpl') . '~/np~';
}
