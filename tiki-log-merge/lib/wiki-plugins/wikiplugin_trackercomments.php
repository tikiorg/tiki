<?php
// $Id: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_trackerfilter.php,v 1.14.2.18 2008-03-17 21:10:11 sylvieg Exp $
function wikiplugin_trackercomments_info() {
	$params = array(
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