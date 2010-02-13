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
				'description' => tra('Tracker ID'),
				'filter' => 'digits'
			),
			'shownbitems' => array(
				'required' => false,
				'name' => tra('shownbitems'),
				'description' => 'y|n',
				'filter' => 'alpha'
			),
			'view' => array(
				'required' => false,
				'name' => tra('View'),
				'description' => 'user '.tra('Select automatically the item of the current user'),
				'filter' => 'alpha'
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
