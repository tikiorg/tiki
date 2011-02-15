<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_trackercomments_info() {
	return array(
		'name' => tra('Tracker Comments'),
		'documentation' => 'PluginTrackerComments',
		'description' => tra('Displays the number of tracker comments'),
		'params' => array(
			'trackerId' => array(
				'required' => true,
				'name' => tra('Tracker ID'),
				'description' => tra('Numeric value representing the tracker ID'),
				'filter' => 'digits',
				'default' => '',
			),
			'shownbitems' => array(
				'required' => false,
				'name' => tra('Item Count'),
				'description' => tra('Determines whether the number of items will be shown (not shown by default)'),
				'filter' => 'alpha',
				'default' => '',
			'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'view' => array(
				'required' => false,
				'name' => tra('View'),
				'description' => tra('Enter a user name to select the items of the current user'),
				'accepted' => tra('a user name'),
				'filter' => 'alpha',
				'default' => ''
			),
		)
	);
}
function wikiplugin_trackercomments($data, $params) {
	global $trklib; include_once('lib/trackers/trackerlib.php');
	global $user;
	extract ($params,EXTR_SKIP);
	$ret = '';
	if ($shownbitems == 'y') {
		$ret .= tra('Comments found:').' '.$trklib->nbComments($user);
	}
	return $ret;
}						   
