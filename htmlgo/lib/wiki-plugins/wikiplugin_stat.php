<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

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
				'description' => 'trackeritem|wiki|article '.tra('separated by :'),
			),
			'parentId' => array(
				'required' => false,
				'name' => tra('Parent Id'),
				'description' => tra('Parent Id'),
			),
			'lasthour' => array(
				'required' => false,
				'name' => tra('Added last hour'),
				'description' => 'a|v '.tra('separated by :'). tra('Added last hour')
			),
			'lastday' => array(
				'required' => false,
				'name' => tra('Added last 24 hours'),
				'description' => 'a|v '.tra('separated by :'). tra('Added last 24 hours')
			),
			'day' => array(
				'required' => false,
				'name' => tra('Added since the beginning of the day'),
				'description' => 'a|v '.tra('separated by :'). tra('Added(a) or viewed(v) since the beginning of the day')
			),
			'lastweek' => array(
				'required' => false,
				'name' => tra('Added last 7 days'),
				'description' => 'a|v '.tra('separated by :'). tra('Added(a) or viewed(v) last 7 days')
			),
			'week' => array(
				'required' => false,
				'name' => tra('Added since the beginning of the week'),
				'description' => 'a|v '.tra('separated by :'). tra('Added(a) or viewed(v) since the beginning of the week')
			),
			'lastmonth' => array(
				'required' => false,
				'name' => tra('Added last month'),
				'description' => 'a|v '.tra('separated by :'). tra('Added(a) or viewed(v) last month')
			),
			'month' => array(
				'required' => false,
				'name' => tra('Added since the beginning of the month'),
				'description' => 'a|v '.tra('separated by :'). tra('Added(a) or viewed(v) since the beginning of the month')
			),
			'lastyear' => array(
				'required' => false,
				'name' => tra('Added last year'),
				'description' => 'a|v '.tra('separated by :'). tra('Added(a) or viewed(v) last year')
			),
			'year' => array(
				'required' => false,
				'name' => tra('Added since the beginning of the year'),
				'description' => 'a|v '.tra('separated by :'). tra('Added(a) or viewed(v) since the beginning of the year')
			),
			
		),
	);
}

function wikiplugin_stat($data, $params) {
	global $smarty;
	global $statslib; include_once('lib/stats/statslib.php');
	$stat = array();
	foreach ($params as $when=>$whats) {
		if ($when == 'type' || $when == 'parentId') {
			continue;
		}
		if (!in_array($when, array('day', 'lastday', 'week', 'lastweek', 'month', 'lastmonth', 'year', 'lastyear'))) {
			return tra('Incorrect param:').$when;
		}
		$whats = explode(':', $whats);
		$types = explode(':', $params['type']);
		foreach ($types as $type) {
			foreach ($whats as $what) {
				switch ($type) {
				case 'trackeritem':
					if ($what != 'v') {
						return tra('Incorrect param:', $what);
					}
					if (empty($params['parentId'])) {
						$params['parentId'] = 0;
					}
					$stat[$when][$type]['Added items'] = $statslib->count_this_period('tiki_tracker_items', 'created', $when, 'trackerId', $params['parentId']);
					break;
				case 'wiki':
					if ($what == 'v') {
						$stat[$when][$type]['Viewed wiki pages'] = $statslib->hit_this_period('wiki', $when);
					} elseif ($what == 'a'){
						$stat[$when][$type]['Added wiki pages'] = $statslib->count_this_period('tiki_pages', 'created', $when);
					} else {
						return tra('Incorrect param:', $what);
					}
					break;
				case 'article':
					if ($what == 'v') {
						$stat[$when][$type]['Viewed articles'] = $statslib->hit_this_period('article', $when);
					} elseif ($what == 'a') {
						$stat[$when][$type]['Added articles'] = $statslib->count_this_period('tiki_articles', 'created', $when);
					} else {
						return tra('Incorrect param:', $what);
					}
					break;
				default:
					return tra('Incorrect param:').$type;
				}
			}
		}
	
	}
	$smarty->assign_by_ref('stat', $stat);
	$code = $smarty->fetch('wiki-plugins/wikiplugin_stat.tpl');
	return "~np~$code~/np~";
}
