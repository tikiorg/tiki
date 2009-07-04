<?php

// $Id: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_flash.php,v 1.8.2.1 2007-11-29 00:25:57 xavidp Exp $

function wikiplugin_stat_info() {
	return array(
		'name' => tra('Stat'),
		'documentation' => 'PluginStat',
		'description' => tra('Displays some stats'),
		'prefs' => array('wikiplugin_stat'),
		'params' => array(
			'type' => array(
				'required' => true,
				'name' => tra('Object type'),
				'description' => 'trackeritem '.tra('or').'wiki',
			),
			'parentId' => array(
				'required' => false,
				'name' => tra('Parent Id'),
				'description' => tra('Parent Id'),
			),
			'lastday' => array(
				'required' => false,
				'name' => tra('Added last 24 hours'),
				'description' => 'a:'. tra('Added last 24 hours')
			),
			'day' => array(
				'required' => false,
				'name' => tra('Added since the beginning of the day'),
				'description' => 'a:'. tra('Added since the beginning of the day')
			),
			'lastweek' => array(
				'required' => false,
				'name' => tra('Added last 7 days'),
				'description' => 'a:'. tra('Added last 7 days')
			),
			'week' => array(
				'required' => false,
				'name' => tra('Added since the beginning of the week'),
				'description' => 'a:'. tra('Added since the beginning of the week')
			),
			'lastmonth' => array(
				'required' => false,
				'name' => tra('Added last month'),
				'description' => 'a:'. tra('Added last month')
			),
			'month' => array(
				'required' => false,
				'name' => tra('Added since the beginning of the month'),
				'description' => 'a:'. tra('Added since the beginning of the month')
			),
			'lastyear' => array(
				'required' => false,
				'name' => tra('Added last year'),
				'description' => 'a:'. tra('Added last year')
			),
			'year' => array(
				'required' => false,
				'name' => tra('Added since the beginning of the year'),
				'description' => 'a:'. tra('Added since the beginning of the year')
			),
			
		),
	);
}

function wikiplugin_stat($data, $params) {
	global $smarty;
	global $statslib; include_once('lib/stats/statslib.php');
	$stat = array();
	foreach ($params as $when=>$what) {
		if ($when == 'type' || $when == 'parentId') {
			continue;
		}
		if (!in_array($when, array('day', 'lastday', 'week', 'lastweek', 'month', 'lastmonth', 'year', 'lastyear'))) {
			return tra('Incorrect param:').$when;
		}
		switch ($params['type']) {
		case 'trackeritem':
			if (empty($params['parentId'])) {
				$params['parentId'] = 0;
			}
			$stat[$params['type']][$when]['added'] = $statslib->count_this_period('tiki_tracker_items', 'created', $when, 'trackerId', $params['parentId']);
			break;
		case 'wiki':
			$stat[$params['type']][$when]['added'] = $statslib->count_this_period('tiki_pages', 'created', $when);
			break;
		default:
			return tra('Incorrect param:').$params['type'];
		}
	
	}
	$smarty->assign_by_ref('stat', $stat);
	$code = $smarty->fetch('wiki-plugins/wikiplugin_stat.tpl');
	return "~np~$code~/np~";
}
